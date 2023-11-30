<?php 

namespace App\Http\Middleware;

use App\Http\Request;
use Closure;
use Exception;

class Queue {

    /**
     * Mapeamento do middleware
     * @var array
     */
    private static $map = [];

    /**
     * Mapeamento do middleware defaults
     * @var array
     */
    private static $default = [];

    /**
     * Fila de middlewares a serem executados.
     * @var array
     */
    private $middlewares = [];

    /**
     * Função de execução do controlador.
     * @var Closure
     */
    private $controller;

    /**
     * Argumentos da função do controlador.
     * @var array
     */
    private $controllerArgs = [];

    /**
     * Método responsável por construir a classe de middlewares.
     * @param array $middlewares
     * @param Closure $controller
     * @param array $controllerArgs
     */
    public function __construct($middlewares, $controller, $controllerArgs) {
        $this->middlewares =  array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }


    /**
     * Método responsável por definir o mapeamento de middleware.
     * @param [type] $map
     * @return void
     */
    public static function setMap($map) {
        self::$map = $map;
    }

    /**
     * Método responsável por definir o mapeamento de middleware.
     * @param [type] $map
     * @return void
     */
    public static function setDefault($default) {
        self::$default = $default;
    }

    /**
     * Método responsável por executar o próximo nível da fila de middlewares.
     * @param Request $request
     */
    public function next($request)  {

        if(empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

        $middleware = array_shift($this->middlewares);

        if(!isset(self::$map[$middleware])){
            throw new Exception('Middleware not found', 400);
        }

        $queue = $this;
        $next = function($request) use($queue){
            return $queue->next($request);
        };

        return (new self::$map[$middleware])->handle($request, $next);
    }

}