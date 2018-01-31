<?php

declare(strict_types=1);

namespace NAttreid\CsasApi\DI;

use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\CsasApi\CsasClient;
use NAttreid\CsasApi\Hooks\CsasHook;
use NAttreid\CsasApi\ICsasClientFactory;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\CompilerExtension;
use Nette\DI\Statement;

/**
 * Class CsasExtension
 *
 * @author Attreid <attreid@gmail.com>
 */
class CsasExtension extends CompilerExtension
{
	use ExtensionTranslatorTrait;

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

		$hook = $builder->getByType(HookService::class);
		if ($hook) {
			$builder->addDefinition($this->prefix('csasHook'))
				->setType(CsasHook::class)
				->addSetup('setConfig', [$csasConfig]);

			$this->setTranslation(__DIR__ . '/../lang/', [
				'webManager'
			]);

			$csasConfig = new Statement('?->csas \?: ?', ['@' . Configurator::class, '@' . CsasHook::class]);
		}

		$builder->addDefinition($this->prefix('factory'))
			->setImplement(ICsasClientFactory::class)
			->setFactory(CsasClient::class)
			->setArguments([$config['debug'], $csasConfig]);
	}
}