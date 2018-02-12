<?php

declare(strict_types=1);

namespace NAttreid\CsasApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use NAttreid\CsasApi\DI\CsasConfig;
use Nette\Application\UI\Control;
use Nette\Application\UI\InvalidLinkException;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\Session;
use Nette\Http\SessionSection;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use stdClass;

/**
 * Class AbstractClient
 *
 * @author Attreid <attreid@gmail.com>
 */
abstract class AbstractClient extends Control
{
	/** @var Client */
	private $client;

	/** @var string */
	protected $uri;

	/** @var bool */
	private $debug;

	/** @var CsasConfig */
	private $config;

	/** @var Request */
	private $request;

	/** @var SessionSection */
	private $section;

	/** @var Response */
	private $response;

	/** @var string */
	protected $authorizeUrl;

	/** @var string */
	protected $tokenUrl;

	public function __construct(bool $debug, CsasConfig $config, Session $session, Request $request, Response $response)
	{
		parent::__construct();
		$this->debug = $debug;
		$this->config = $config;
		$this->section = $session->getSection('nattreid/csac-api/oauth2-' . (new ReflectionClass($this))->getShortName());
		$this->request = $request;
		$this->response = $response;
	}

	/**
	 * @param ResponseInterface $response
	 * @return stdClass|null
	 * @throws JsonException
	 */
	private function getResponse(ResponseInterface $response): ?stdClass
	{
		$json = $response->getBody()->getContents();
		if (!empty($json)) {
			return Json::decode($json);
		}
		return null;
	}

	private function getClient(): Client
	{
		if ($this->client === null) {
			$this->client = new Client(['base_uri' => $this->uri]);
		}
		return $this->client;
	}

	public function handleToken(string $backlink): void
	{
		$this->template->setFile(__DIR__ . '/templates/default.latte');

		$this->template->backlink = $backlink;
	}

	public function handleProcess(string $token, string $expiration, string $backlink)
	{
		$this->section->setExpiration(((int) $expiration) . ' seconds');
		$this->section->token = $token;
		$this->presenter->restoreRequest($backlink);
	}

	/**
	 * @return string
	 * @throws InvalidLinkException
	 */
	private function getToken(): string
	{
		if ($this->section->token === null) {
			$backlink = $this->presenter->storeRequest();
			$this->response->redirect($this->authorizeUrl . '?' . http_build_query([
					'client_id' => $this->config->clientId,
					'redirect_uri' => $this->link('//token', [$backlink]),
					'scope' => 'profile',
					'response_type' => 'token'
				]));
			exit;
		}
		return $this->section->token;
	}

	/**
	 * @param string $method
	 * @param string $url
	 * @param array $args
	 * @return stdClass|null
	 * @throws CsasClientException
	 */
	private function request(string $method, string $url, array $args = []): ?stdClass
	{
		if (empty($this->config->apiKey)) {
			throw new CsasClientException('ApiKey must be set');
		}

		try {
			$token = $this->getToken();
			$options = [
				RequestOptions::HEADERS => [
					'WEB-API-key' => $this->config->apiKey,
					'Authorization' => $token
				]
			];

			if (count($args) >= 1) {
				$options[RequestOptions::JSON] = $args;
			}

			$response = $this->getClient()->request($method, $url, $options);

			switch ($response->getStatusCode()) {
				case 200:
				case 201:
					return $this->getResponse($response);
				case 204:
					return new stdClass();
			}
		} catch (ClientException $ex) {
			switch ($ex->getCode()) {
				default:
					throw new CsasClientException($ex);
					break;
				case 400:
				case 404:
				case 422:
					if ($this->debug) {
						throw new CsasClientException($ex);
					} else {
						return null;
					}
			}
		} catch (\Exception $ex) {
			throw new CsasClientException($ex);
		}
		return null;
	}

	/**
	 * @param string $url
	 * @return stdClass|null
	 * @throws CsasClientException
	 */
	protected function get(string $url): ?stdClass
	{
		return $this->request('GET', $url);
	}

	/**
	 * @param string $url
	 * @param string[] $args
	 * @return stdClass|null
	 * @throws CsasClientException
	 */
	protected function post(string $url, array $args = []): ?stdClass
	{
		return $this->request('POST', $url, $args);
	}

	/**
	 * @param string $url
	 * @return bool
	 * @throws CsasClientException
	 */
	protected function delete(string $url): bool
	{
		return $this->request('DELETE', $url) !== null;
	}

	/**
	 * @param string $url
	 * @param string[] $args
	 * @return stdClass|null
	 * @throws CsasClientException
	 */
	protected function patch(string $url, array $args = []): ?stdClass
	{
		return $this->request('PATCH', $url, $args);
	}

	/**
	 * @param string $url
	 * @param string[] $args
	 * @return stdClass|null
	 * @throws CsasClientException
	 */
	protected function put(string $url, array $args = []): ?stdClass
	{
		return $this->request('PUT', $url, $args);
	}

	public function render(): void
	{
		if ($this->template->getFile()) {
			$this->template->render();
		}
	}
}