<?php

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

return [
    'dependencies' => [
        'aliases' => [
            LoggerInterface::class => NullLogger::class
        ],
        'invokables' => [
            NullLogger::class,
        ]
    ]
];
