<?php

declare(strict_types=1);

namespace NAttreid\CsasApi;

use DateTimeInterface;
use NAttreid\CsasApi\DI\CsasConfig;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\Session;
use stdClass;

/**
 * Class CsasCorporateClient
 *
 * @author Attreid <attreid@gmail.com>
 */
class CsasCorporateClient extends AbstractClient
{
	public function __construct(bool $debug, CsasConfig $config, Session $session, Request $request, Response $response)
	{
		parent::__construct($debug, $config, $session, $request, $response);
		if ($debug) {
			$this->uri = "https://api.csas.cz/sandbox/webapi/api/v1/corporate/our/";
			$this->authorizeUrl = "https://api.csas.cz/sandbox/widp/oauth2/auth";
			$this->tokenUrl = "http://api.csas.cz/sandbox/widp/oauth2/token";
		} else {
		}
	}

	/**
	 * @return array
	 * @throws CsasClientException
	 * @throws CredentialsNotSetException
	 */
	public function companies(): array
	{
		return $this->get('companies');
	}

	/**
	 * @return array
	 * @throws CredentialsNotSetException
	 * @throws CsasClientException
	 */
	public function accounts(): array
	{
		return $this->get('accounts')->accounts ?? [];
	}

	/**
	 * @param string $id
	 * @param DateTimeInterface $from
	 * @param DateTimeInterface $to
	 * @return array
	 * @throws CredentialsNotSetException
	 * @throws CsasClientException
	 */
	public function transactions(string $id, DateTimeInterface $from, DateTimeInterface $to): array
	{
		$format = 'c';
		$sfrom = urlencode($from->format($format));
		$sTo = urlencode($to->format($format));
		return $this->get("accounts/{$id}/transactions?dateStart={$sfrom}&dateEnd={$sTo}")->transactions ?? [];
	}
}

interface ICsasCorporateClientFactory
{
	public function create(): CsasCorporateClient;
}
