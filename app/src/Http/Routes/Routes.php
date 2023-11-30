<?php

namespace App\Http\Routes;


use App\Controllers\Example\ExampleController;
use \App\Http\Middleware\Queue as MiddlewareQueue;
use App\Http\Response;

class Routes
{
    /**
     * Url do projeto.
     * @var string
     */
    private string $url;

    public function __construct($url) {
        $this->url = $url;
    }

    public function start() {

        MiddlewareQueue::setMap([
            'cache' => \App\Http\Middleware\Cache::class
        ]);

        $router = new \App\Http\Router($this->url);

        //Registrar Rotas

        $router->get('/v1/example', [
            ['middlewares' => 'cache'],
            function ($request) {
                return new Response(ExampleController::getAction($request), Response::OK);
            }
        ]);

        $router->post('/v1/example', [
            function ($request) {
                return new Response(ExampleController::postAction($request), Response::CREATED);
            }
        ]);

        $router->put('/v1/example/{id}', [
            function ($id, $request) {
                return new Response(ExampleController::putAction($id, $request), Response::NO_CONTENT);
            }
        ]);

        //Inicializar Roteador.
        $router->run()->sendResponse();
    }
}
