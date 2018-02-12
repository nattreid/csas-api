<?php

declare(strict_types=1);

namespace NAttreid\CsasApi;

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
			$this->uri = "https://api.csas.cz/sandbox/webapi/api/v3/netbanking/my/";
			$this->authorizeUrl = "	https://api.csas.cz/sandbox/widp/oauth2/auth";
			$this->tokenUrl = "http://api.csas.cz/sandbox/widp/oauth2/token";
		} else {
		}
	}

	/**
	 * @return null|stdClass
	 * @throws CsasClientException
	 */
	public function accounts(): ?stdClass
	{
		return $this->get('accounts');
	}

	public function diggest(): ?stdClass
	{
		return $this->get('accounts/diggest');
	}
}

interface ICsasClientFactory
{
	public function create(): CsasClient;
}
