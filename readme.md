# Česká spořitelna (Csas) API pro Nette Framework

Nastavení v **config.neon**
```neon
extensions:
    csas: NAttreid\CsasApi\DI\CsasExtension

csas:
    apiKey: 'apiKey'
    clientId: 'clientId'
    clientSecret: 'clientSecret'
    debug: true # default false
```

Použití

```php
/** @var NAttreid\CsasApi\ICsasClientFactory @inject */
public $csasFactory;

public function createComponentCsas(): CsasClient
{
    return $this->csasClientFactory->create();
}

public function process(): void
{
    $cards = $this['csas']->cards();
}
```

Do šablony přidáme komponentu kvůli tokenu
```latte
{control csas}
```
