<?php

use GuzzleHttp\Middleware;
use GuzzleHttp\Handler\MockHandler;
use HttpClient\Example\HttpClientMiddleware\FirstHttpClientMiddleware;
use HttpClient\Example\HttpClientMiddleware\SecondHttpClientMiddleware;
use HttpClient\Example\HttpClientMiddleware\SimpleHttpClientMiddleware;
use HttpClient\Example\HttpResponseValidator\RollunComNotFoundResponseValidator;
use HttpClient\HandlerStack\HandlerStackAbstractFactory;
use HttpClient\HttpClient\HttpClientAbstractFactory;
use HttpClient\HttpClientMiddleware\Factory\InvokableHttpMiddlewareAbstractFactory;
use HttpClient\HttpClientMiddleware\Factory\ResponseValidatorHttpMiddlewareAbstractFactory;
use HttpClient\HttpClientMiddleware\Factory\UserAgentHttpMiddlewareAbstractFactory;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManager;
use HttpClient\HttpClientMiddleware\HttpClientMiddlewarePluginManagerFactory;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonBotDetectionResponseValidator;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonCaptchaResponseValidator;
use HttpClient\Example\HttpResponseValidator\Amazon\AmazonZipCodeResponseValidator;
use HttpClient\HttpResponseValidator\StatusOk;
use rollun\utils\Factory\AbstractServiceAbstractFactory;


return [
    'dependencies' => [
        'invokables' => [
            AmazonBotDetectionResponseValidator::class => AmazonBotDetectionResponseValidator::class,
            AmazonCaptchaResponseValidator::class => AmazonCaptchaResponseValidator::class,
            RollunComNotFoundResponseValidator::class => RollunComNotFoundResponseValidator::class,
            MockHandler::class => MockHandler::class,
            SecondHttpClientMiddleware::class => SecondHttpClientMiddleware::class,
            StatusOk::class => StatusOk::class,
        ],
        'aliases' => [
        ],
        'factories' => [
            HttpClientMiddlewarePluginManager::class => HttpClientMiddlewarePluginManagerFactory::class,
        ],
        'abstract_factories' => [
            HandlerStackAbstractFactory::class,
            AbstractServiceAbstractFactory::class,
            HttpClientAbstractFactory::class,
        ],
    ],
    AbstractServiceAbstractFactory::class => [
        AmazonZipCodeResponseValidator::class => [
            AbstractServiceAbstractFactory::KEY_CLASS => AmazonZipCodeResponseValidator::class,
            AbstractServiceAbstractFactory::KEY_DEPENDENCIES => [
                'zipCode' => [
                    AbstractServiceAbstractFactory::KEY_TYPE => AbstractServiceAbstractFactory::TYPE_SIMPLE,
                    AbstractServiceAbstractFactory::KEY_VALUE => '77632',
                ],
            ],
        ]
    ],
    HandlerStackAbstractFactory::class => [
        'HandlerStackEmptyServiceName' => [], // curl middleware only
        'HandlerStackStandartServiceName' => [
            'http_errors' => [Middleware::class, 'httpErrors'],
            'allow_redirects' => [Middleware::class, 'redirect'],
            'cookies' => [Middleware::class, 'cookies'],
            'prepare_body' => [Middleware::class, 'prepareBody'],
        ],
        'HandlerStackCustomServiceName' => [
            'http_errors' => [Middleware::class, 'httpErrors'],
            'prepare_body' => [Middleware::class, 'prepareBody'],
            'my_own_middleware' => 'SimpleUserAgentHttpClientMiddleware',
        ],
        'TestAmazonHandlerStackCustomServiceName' => [
            'user_agent' => 'EmptyUserAgentHttpClientMiddleware',
            'simple' => SimpleHttpClientMiddleware::class,
        ],
    ],
    UserAgentHttpMiddlewareAbstractFactory::class => [
        'SimpleUserAgentHttpClientMiddleware' => [
            UserAgentHttpMiddlewareAbstractFactory::KEY_USER_AGENT => 'tetetetetetet',
        ],
        'EmptyUserAgentHttpClientMiddleware' => [
            UserAgentHttpMiddlewareAbstractFactory::KEY_USER_AGENT => '',
        ]
    ],
    InvokableHttpMiddlewareAbstractFactory::class => [
        FirstHttpClientMiddleware::class => FirstHttpClientMiddleware::class,
        //SecondHttpClientMiddleware::class => SecondHttpClientMiddleware::class,
        SimpleHttpClientMiddleware::class => SimpleHttpClientMiddleware::class,
    ],
    ResponseValidatorHttpMiddlewareAbstractFactory::class => [
        'TestAmazonResponseValidatorHttpClientMiddleware' => [
            ResponseValidatorHttpMiddlewareAbstractFactory::KEY_VALIDATORS => [
                StatusOk::class,
                AmazonBotDetectionResponseValidator::class,
                AmazonCaptchaResponseValidator::class,
                AmazonZipCodeResponseValidator::class,
            ],
        ],
        'serviceStatusOkResponseValidatorHttpMiddleware' => [
            ResponseValidatorHttpMiddlewareAbstractFactory::KEY_VALIDATORS => [
                StatusOk::class,
            ],
        ],
        'serviceRollunComResponseValidatorHttpMiddleware' => [
            ResponseValidatorHttpMiddlewareAbstractFactory::KEY_VALIDATORS => [
                RollunComNotFoundResponseValidator::class,
            ],
        ],
    ],
    HttpClientMiddlewarePluginManagerFactory::class => [
        'invokables' => [

        ],
        'abstract_factories' => [
            UserAgentHttpMiddlewareAbstractFactory::class,
            ResponseValidatorHttpMiddlewareAbstractFactory::class,
            InvokableHttpMiddlewareAbstractFactory::class,
            AbstractServiceAbstractFactory::class,
        ],
    ]
];