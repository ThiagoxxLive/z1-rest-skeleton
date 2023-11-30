<?php

namespace App\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Container {

    private $container;

    public function __construct() {

        $this->container = new ContainerBuilder();
    }

    public function init() {

        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../Config/'));
        $loader->load('services.yml');

        return $this->container;
    }

    public function get(string $id) {        
        return $this->init()->get($id);
    }
}