<?php

declare(strict_types=1);

namespace NAttreid\CsasApi\DI;

use Nette\SmartObject;

/**
 * Class CsasConfig
 *
 * @property string|null $apiKey
 * @property string|null $clientId
 * @property string|null $clientSecret
 *
 * @author Attreid <attreid@gmail.com>
 */
class CsasConfig
{
	use SmartObject;

	/** @var string|null */
	private $apiKey;

	/** @var string|null */
	private $clientId;

	/** @var string|null */
	private $clientSecret;

	protected function getApiKey(): ?string
	{
		return $this->apiKey;
	}

	protected function setApiKey(?string $apiKey): void
	{
		$this->apiKey = $apiKey;
	}

	public function getClientId(): ?string
	{
		return $this->clientId;
	}

	public function setClientId(?string $clientId): void
	{
		$this->clientId = $clientId;
	}

	public function getClientSecret(): ?string
	{
		return $this->clientSecret;
	}

	public function setClientSecret(?string $clientSecret): void
	{
		$this->clientSecret = $clientSecret;
	}
}