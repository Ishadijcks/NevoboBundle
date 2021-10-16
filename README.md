# Punch Nevobo Bundle

> A Symfony Bundle to help you query the Nevobo v1 Api

Inspired by [Nevobo-js](https://www.npmjs.com/package/nevobo-js)

## Installation

```bash
composer require punch/nevobo-bundle
```

The bundle does not use Symfony Flex (yet), so manually register it in `config/bundles/php`

```php
<?php

return [
    // ...
    Punch\NevoboBundle\PunchNevoboBundle::class => ['all' => true],
]
```

And add the config in `config/packages/punch_nevobo.yaml`:

```yaml
punch_nevobo:
  cache_duration: 86400 # 1 day
```

You can change how long you want to cache the results, use 0 to disable caching entirely.

## Usage

```php
use Punch\NevoboBundle\Service\NevoboClient;

class YourController extends AbstractController
{
    #[Route(path: '/', name: 'some_route')]
    public function index(NevoboClient $client): Response
    {
        $client->getVereniging('ckl9m4l');
        $client->getTeamsForVereniging('ckl9m4l');
        $client->getSporthallenForVereniging('ckl9m4l');
        $client->getWedstrijdenForVereniging('ckl9m4l');
        $client->getWedstrijdResultaatForVereniging('ckl9m4l');
        $client->getWedstrijdProgrammaForVereniging('ckl9m4l');    
        $client->getTeam('ckl9m4l-hs-4');
        $client->getPouleIndelingen('regio-west-h1f-7');
        $client->getWedstrijdenForPoule('regio-west-h1f-7');
    }
}
```

## FAQ

**Q: Why is the codebase a mix of Dutch and English?**

A: All variables and classes follow the names from the Nevobo API, blame them.

**Q: I just want the rankings for a specific team?**

A: First use `getPoules(<teamId>)`, then use `getPouleIndelingen(<pouleId>)` to get the standings.

