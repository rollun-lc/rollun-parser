<?php
/**
 * @copyright Copyright © 2014 Rollun LC (http://rollun.com/)
 * @license LICENSE.md New BSD License
 */

namespace rollun\parser;

use rollun\datastore\DataStore\Factory\DataStoreAbstractFactory;
use rollun\datastore\DataStore\Factory\HttpClientAbstractFactory;
use rollun\datastore\DataStore\HttpClient;
use rollun\parser\Factory\ParserAbstractFactory;
use rollun\parser\Factory\TaskSourceAbstractFactory;

class ConfigProvider
{
    const PROXY_DATASTORE = 'proxyDatastore';

    public function __invoke()
    {
        return [
            'dependencies' => [
                'abstract_factories' => [
                    TaskSourceAbstractFactory::class,
                    ParserAbstractFactory::class,
                ],
            ],
            DataStoreAbstractFactory::KEY_DATASTORE => [
                self::PROXY_DATASTORE => [
                    HttpClientAbstractFactory::KEY_CLASS => HttpClient::class,
                    HttpClientAbstractFactory::KEY_URL => getenv('PROXY_MANAGER_URI'),
                ],
            ],
        ];
    }
}
