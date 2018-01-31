<?php

declare(strict_types=1);

namespace NAttreid\CsasApi\Hooks;

use NAttreid\CsasApi\DI\CsasConfig;
use NAttreid\Form\Form;
use NAttreid\WebManager\Services\Hooks\HookFactory;
use Nette\ComponentModel\Component;
use Nette\Utils\ArrayHash;

/**
 * Class CsasHook
 *
 * @author Attreid <attreid@gmail.com>
 */
class CsasHook extends HookFactory
{
	/** @var IConfigurator */
	protected $configurator;

	/** @var CsasConfig */
	private $config;

	public function setConfig(CsasConfig $config)
	{
		$this->config = $config;
	}

	public function init(): void
	{
		if (!$this->configurator->csas) {
			$this->configurator->csas = $this->config;
		}
	}

	/** @return Component */
	public function create(): Component
	{
		$form = $this->formFactory->create();

		$form->addText('apiKey', 'webManager.web.hooks.csas.apiKey')
			->setDefaultValue($this->configurator->csas->apiKey);

		$form->addText('clientId', 'webManager.web.hooks.csas.clientId')
			->setDefaultValue($this->configurator->csas->clientId);

		$form->addText('clientSecret', 'webManager.web.hooks.csas.clientSecret')
			->setDefaultValue($this->configurator->csas->clientSecret);

		$form->addSubmit('save', 'form.save');

		$form->onSuccess[] = [$this, 'csasFormSucceeded'];

		return $form;
	}

	public function csasFormSucceeded(Form $form, ArrayHash $values): void
	{
		$config = $this->configurator->csas;

		$config->apiKey = $values->apiKey;
		$config->clientId = $values->clientId;
		$config->clientSecret = $values->clientSecret;

		$this->configurator->csas = $config;

		$this->flashNotifier->success('default.dataSaved');

		$this->onDataChange();
	}
}