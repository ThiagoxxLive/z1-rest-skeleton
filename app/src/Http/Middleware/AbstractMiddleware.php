<?php
namespace App\Http\Middleware;

class AbstractMiddleware {

    /**
     * Retorna um container.
     * @param string $id
     * @return mixed
     */
    protected function get(string $id) : mixed {
        $container = new \App\DependencyInjection\Container();
        return $container->get($id);
    } 
}