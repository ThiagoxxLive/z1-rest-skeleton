<?php

namespace App\Http;

use App\Validators\Form;
use Exception;

class Request {

    /**
     * Método HTTP da Requisição
     * @var string
     */
    public $httpMethod;

    /**
     * Rota da requisição
     * @var string
     */
    private $uri;

    /**
     * Parâmetros da URL ($_GET)
     * @var array
     */
    private $queryParams = [];

    /**
     * Variáveis recebidas no post da página
     * @var array
     */
    private $postVars = [];

    /**
     * Cabeçalho da requisição
     * @var array
     */
    private $headers = [];

    /**
     * Instância do router.
     * @var Router
     */
    private $router;

    /**     
     * @var string
     */
    private $token;

    /**
     * Construtor da classe.
     */

    public function __construct($router) {
        $this->queryParams = $_GET ?? [];
        $this->router = $router;
        $this->postVars = json_decode(file_get_contents("php://input"), true) ?? [];
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
    }


    /**
     * Método responsável por definir a URI.
     * @return void
     */
    private function setUri() {

        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        $xUri = explode('?', $this->uri);
        $this->uri = $xUri[0];

    }

    /**
     * Retorna o token do usuário logado.
     * @return string
     */
    public function getToken() : string {
        return str_replace("Bearer", "", $this->headers['Authorization']) ?? null;
    }


    /**
     * Método responsável por retornar o método HTTP da requisição.
     * @return string
     */
    public function getHttpMethod() : string {
        return $this->httpMethod;
    }

    /**
     * Retorna a instância de router.
     * @return Router
     */
    public function getRouter() : Router {
        return $this->router;
    }

    /**
     * Método responsável por retornar a URI da requisição.
     * @return string
     */
    public function getUri() : string {
        return $this->uri;
    }

    /**
     * Método responsável por retornar os Headers da requisição.
     * @return array
     */
    public function getHeaders() : array {
        return $this->headers;
    }

    /**
     * Método responsável por retornar os Headers da requisição.
     */
    public function getHeader(string $header) : string  | null{
        if(empty($this->headers[$header])) {
            return null;
        }

        return $this->headers[$header];
    }

    /**
     * Método responsável por retornar os Parâmetros da requisição.
     * @return array
     */
    public function getParams() : array {
        return $this->queryParams;
    }

    /**
     * Retorna um parâmetro especifico da query string.
     * @param string $key
     */
    public function query(string $key) : string | null {
        if(!empty($this->queryParams)) {
            return $this->queryParams[$key];
        }

        return null;
    }

    /**
     * Método responsável por retornar as variáveis POST da requisição.
     * @return array
     */
    public function getAttributes() : array {
        return $this->postVars;
    }

    /**
     * Método responsável por retornar apenas um item do array de POST da requisição.
     * @param string $key
     * @return string
     */
    public function get(string $key) : string {

        if(!array_key_exists($key, $this->postVars)) {
            throw new Exception("A chave do array informado não existe: {$key}", 500);
        }

        $key = strip_tags($key);
        $key = trim($key);

        return $this->postVars[$key];
    }

    public function validate(Request $request, array $keysToVerify) : bool {
        return Form::validate($request, $keysToVerify);
    }
}