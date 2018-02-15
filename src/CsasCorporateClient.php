<?php

declare(strict_types=1);

namespace NAttreid\CsasApi;

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
			$this->authorizeUrl = "	https://api.csas.cz/sandbox/widp/oauth2/auth";
			$this->tokenUrl = "http://api.csas.cz/sandbox/widp/oauth2/token";
		} else {
		}
	}

	/**
	 * @return null|stdClass
	 * @throws CsasClientException
	 * @throws CredentialsNotSetException
	 */
	public function companies(): ?stdClass
	{
		return $this->get('companies');
	}

	/**
	 * @return null|stdClass
	 * @throws CredentialsNotSetException
	 * @throws CsasClientException
	 */
	public function accounts(): ?stdClass
	{
		return $this->get('accounts');
	}
}

interface ICsasCorporateClientFactory
{
	public function create(): CsasCorporateClient;
}
