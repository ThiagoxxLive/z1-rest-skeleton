<?php 

namespace App\Http;

use App\Exceptions\HttpNotFoundException;
use App\Exceptions\MethodNotAllowedException;
use Closure;
use Exception;
use ReflectionFunction;
use App\Http\Middleware\Queue as MiddlewareQueue;
use InvalidArgumentException;

class Router {

    /**
     * URL completa do projeto.
     * @var string
     */
    private string $url;

    /**
     * Prefixo das rotas
     * @var string
     */
    private string $prefix = '';

    /**
     * Índice de rotas.
     * @var array
     */
    private $routes = [];

    /**
     * Instância de request.
     * @var Request
     */
    private $request;

    /**
     * Método construtor.
     * @param string $url
     */
    public function __construct(string $url) {
        $this->request = new \App\Http\Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Método responsável por definir os prefixos das rotas.
     * @return void
     */
    private function setPrefix() :void {
        $parseUrl = parse_url($this->url);
        $this->prefix = $parseUrl['path'] ?? null;
    }

    /**
     * Método responsável por adicionar uma rota na classe.
     * @param string $method
     * @param string $route
     * @param array $params
     * @return void
     */
    private function addRoute(string $method, string $route, $params = []) : void {

        foreach($params as $key => $value) {
            if($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params['key']);
                continue;
            }
        }

        $params['middlewares'] = $params['middlewares'] ?? [];

        $params['variables'] = [];

        $patternVar = '/{(.*?)}/';

        if(preg_match_all($patternVar, $route, $matches)) {
            $route = preg_replace($patternVar, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        $pattern = '/^' . str_replace('/', '\/', $route) . '$/' ;
        $this->routes[$pattern][$method] = $params;

    }

    /**
     * Método responsável por definir uma rota de GET.
     * @param string $route
     * @param array $params
     */
    public function get(string $route, $params = []) {
        $this->options($route);
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Método responsável por definir uma rota de POST.
     * @param string $route
     * @param array $params
     */
    public function post(string $route, $params = []) {
        $this->options($route);
        return $this->addRoute('POST', $route, $params);
    }

    private function options(string $route) {
        return $this->addRoute('OPTIONS', $route);
    }

    /**
     * Método responsável por definir uma rota de PUT.
     * @param string $route
     * @param array $params
     */
    public function put(string $route, $params = []) {
        $this->options($route);
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Método responsável por retornar a URI sem prefixo.
     * @return string
     */
    private function getUri() : string {

        $uri = $this->request->getUri();
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        return end($xUri);

    }

    /**
     * Método responsável por retornar os dados da rota atual.
     * @return array
     */
    private function getRoute() : array {

        $uri = $this->getUri();
        $httpMethod = $this->request->getHttpMethod();

        foreach($this->routes as $pattern => $methods) {

            if(preg_match($pattern, $uri, $matches)) {

                if(isset($methods[$httpMethod])) {  
                    unset($matches[0]);      
                    
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;
                    return $methods[$httpMethod];
                }

                 throw new MethodNotAllowedException('Método não permitido.', 405);
            }
        }

        throw new HttpNotFoundException('URL não encontrada.', 404);
    }


    /**
     * Método responsável por executar a rota atual
     * @return JsonResponse
     */
    public function run() : Response {

        try {

            $route = $this->getRoute();

            if($route['variables']['request']->httpMethod == "OPTIONS") {
                return new Response([], Response::OK);
            }
            
            if(!isset($route['controller'])) {
                throw new InvalidArgumentException(("A URL não pode ser processada."), 500);
            }

            $args = [];
            $reflection = new ReflectionFunction($route['controller']);

            foreach($reflection->getParameters() as $parameter){
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
                
            }

            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);
            
        }catch(Exception $e) {
            return new Response(['message' => $e->getMessage(), 'status' => false], $e->getCode());
        }

    }
}

