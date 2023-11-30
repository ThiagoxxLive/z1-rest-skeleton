<?php

namespace App\Controllers;

class Controller {

    /**
     * Método responsável por replicar uma classe para acessar seus atributos.
     * @param string $id
     * @return mixed
     */
    protected static function get(string $id) : mixed {
        
        $container = new \App\DependencyInjection\Container();
        return $container->get($id);
    }
    
}