<?php

declare(strict_types=1);

namespace NAttreid\CsasApi\DI;

use NAttreid\Cms\Configurator\Configurator;
use NAttreid\Cms\DI\ExtensionTranslatorTrait;
use NAttreid\CsasApi\Hooks\CsasHook;
use NAttreid\WebManager\Services\Hooks\HookService;
use Nette\DI\ServiceDefinition;
use Nette\DI\Statement;

if (trait_exists('NAttreid\Cms\DI\ExtensionTranslatorTrait')) {
	class CsasExtension extends AbstractCsasExtension
	{
		use ExtensionTranslatorTrait;

		protected function prepareHook(ServiceDefinition $csasConfig)
		{
			$builder = $this->getContainerBuilder();
			$hook = $builder->getByType(HookService::class);
			if ($hook) {
				$builder->addDefinition($this->prefix('csasHook'))
					->setType(CsasHook::class)
					->addSetup('setConfig', [$csasConfig]);

				$this->setTranslation(__DIR__ . '/../lang/', [
					'webManager'
				]);

				return new Statement('?->csas \?: ?', ['@' . Configurator::class, '@' . CsasHook::class]);
			} else {
				return parent::prepareHook($csasConfig);
			}
		}
	}
} else {
	class CsasExtension extends AbstractCsasExtension
	{
	}
}