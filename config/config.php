<?php

use Symfony\Component\Dotenv\Dotenv;
use Zend\ConfigAggregator\ConfigAggregator;
use Zend\ConfigAggregator\PhpFileProvider;

// Make environment variables stored in .env accessible via getenv(), $_ENV or $_SERVER.
(new Dotenv())->load('.env');

// Determine application environment ('dev', 'test' or 'prod').
$appEnv = getenv('APP_ENV');

$aggregator = new ConfigAggregator([
    // Include your config providers here.
    // ...

    // Default App module config
    // Load application config in a pre-defined order in such a way that local settings
    // overwrite global settings. (Loaded as first to last):
    //   - `global.php`
    //   - `*.global.php`
    //   - `local.php`
    //   - `*.local.php`
    new PhpFileProvider('config/autoload/{{,*.}global,{,*.}local}.php'),

    // Load application config according to environment:
    //   - `global.dev.php`,   `global.test.php`,   `prod.global.prod.php`
    //   - `*.global.dev.php`, `*.global.test.php`, `*.prod.global.prod.php`
    //   - `local.dev.php`,    `local.test.php`,     `prod.local.prod.php`
    //   - `*.local.dev.php`,  `*.local.test.php`,  `*.prod.local.prod.php`
    new PhpFileProvider(realpath(__DIR__) . "/autoload/{{,*.}global.{$appEnv},{,*.}local.{$appEnv}}.php"),
]);

return $aggregator->getMergedConfig();
