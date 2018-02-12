<?php

declare(strict_types=1);

namespace NAttreid\CsasApi\DI;

use NAttreid\CsasApi\CsasClient;
use NAttreid\CsasApi\CsasCorporateClient;
use NAttreid\CsasApi\ICsasClientFactory;
use NAttreid\CsasApi\ICsasCorporateClientFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;

/**
 * Class AbstractCsasExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
class AbstractCsasExtension extends CompilerExtension
{

	private $defaults = [
		'apiKey' => null,
		'debug' => false,
	];

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults, $this->getConfig());

		$csasConfig = $builder->addDefinition($this->prefix('config'))
			->setType(CsasConfig::class)
			->addSetup(new Statement('$service->apiKey = ?', [$config['apiKey']]));

		$csasConfig = $this->prepareHook($csasConfig);

		$builder->addDefinition($this->prefix('factoryClient'))
			->setImplement(ICsasClientFactory::class)
			->setFactory(CsasClient::class)
			->setArguments([$config['debug'], $csasConfig]);

		$builder->addDefinition($this->prefix('factoryCorporate'))
			->setImplement(ICsasCorporateClientFactory::class)
			->setFactory(CsasCorporateClient::class)
			->setArguments([$config['debug'], $csasConfig]);
	}

	protected function prepareHook(ServiceDefinition $csasConfig)
	{
		return $csasConfig;
	}
}