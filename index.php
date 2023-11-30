<?php

require 'vendor/autoload.php';

date_default_timezone_set('America/Sao_Paulo');

use \App\Common\Environment;
Environment::load(__DIR__);

$container = new \App\DependencyInjection\Container();
$container->init();

$routes = new \App\Http\Routes\Routes(getenv('URL_ROUTER'));
$routes->start();