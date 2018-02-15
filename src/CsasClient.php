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
 * Class CsasClient
 *
 * @author Attreid <attreid@gmail.com>
 */
class CsasClient extends AbstractClient
{
	public function __construct(bool $debug, CsasConfig $config, Session $session, Request $request, Response $response)
	{
		parent::__construct($debug, $config, $session, $request, $response);
		if ($debug) {
			$this->uri = "https://api.csas.cz/sandbox/webapi/api/v3/netbanking/";
			$this->authorizeUrl = "https://api.csas.cz/sandbox/widp/oauth2/auth";
			$this->tokenUrl = "http://api.csas.cz/sandbox/widp/oauth2/token";
		} else {
		}
	}

	/**
	 * @return array
	 * @throws CredentialsNotSetException
	 * @throws CsasClientException
	 */
	public function accounts(): array
	{
		return $this->get('my/accounts')->accounts ?? [];
	}

	/**
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws CsasClientException
	 */
	public function diggest(): ?stdClass
	{
		return $this->get('my/accounts/diggest');
	}

	/**
	 * @param string $iban
	 * @param DateTimeInterface $from
	 * @param DateTimeInterface $to
	 * @return array
	 * @throws CredentialsNotSetException
	 * @throws CsasClientException
	 */
	public function transaction(string $iban, DateTimeInterface $from, DateTimeInterface $to): array
	{
		$format = 'c';
		$sfrom = urlencode($from->format($format));
		$sTo = urlencode($to->format($format));
		return $this->get("cz/my/accounts/{$iban}/transactions?dateStart={$sfrom}&dateEnd={$sTo}")->transactions ?? [];
	}
}

interface ICsasClientFactory
{
	public function create(): CsasClient;
}
