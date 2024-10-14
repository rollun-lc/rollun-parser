<?php

global $argv;

use rollun\dic\InsideConstruct;

// Change to the project root, to simplify resolving paths
chdir(dirname(__DIR__));
require 'vendor/autoload.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$container = require 'config/container.php';
InsideConstruct::setContainer($container);

if (getenv("APP_ENV") != 'dev') {
    echo "You cannot start test if environment var APP_ENV not set in dev!";
    exit(1);
}
