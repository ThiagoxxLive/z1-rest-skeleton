<?php

namespace App\Http;

class Response {

    public const OK = 200;
    public const CREATED = 201;
    public const NO_CONTENT = 204;
    public const BAD_REQUEST = 400;
    public const NOT_FOUND = 404;
    public const INTERNAL_SERVER_ERROR = 500;

    /**
     * Código do status http.
     * @var integer
     */
    private int $httpCode = 200;

    /**
     * Cabeçalhos da response.
     * @var array
     */
    private $headers = [];

    /**
     * Tipo do conteúdo a ser retornado.
     * @var string
     */
    private string $contentType = 'application/json';

    /**
     * Conteúdo do response.
     * @var [type]
     */
    private $content;


    /**
     * Construtor da classe.
     * @param mixed $content
     * @param integer $httpCode
     * @param string $contentType
     */
    public function __construct(mixed $content, int $httpCode, $contentType = 'application/json') {

        $this->content = $content;
        $this->httpCode = $httpCode;
        $this->setContentType($contentType);
    }

    /**
     * Método responsável por alterar o content type do response.
     * @param string $contentType
     * @return void
     */
    public function setContentType(string $contentType) : void {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
        $this->addHeader('X-Ninja', 'Z1Tec - Gestão em Tecnologia');
        $this->addHeader('X-Hosting', 'https://z1tec.com.br');
        $this->addHeader('X-Powered-By', "PHP/" . phpversion());
        $this->addHeader('Access-Control-Allow-Origin', '*');
        $this->addHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->addHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    }

    /**
     * Método responsável por adicionar os headers do response.
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addHeader(string $key, string $value) : void {
        $this->headers[$key] = $value;
    }

    /**
     * Método responsável por enviar os headers para o navegador.
     * @return void;
     */
    private function sendHeaders() : void {

        http_response_code($this->httpCode);

        foreach($this->headers as $key => $value) {
            header("{$key}:{$value}");
        }
    }

    /**
     * Método responsável por enviar a resposta ao usuário.
     * @return void
     */
    public function sendResponse() : void {

        $this->sendHeaders();

        switch($this->contentType) {
            case 'application/json':
                echo json_encode($this->content);
                break;
            case 'application/xml':
                echo $this->content;
                break;
        }
    }
   
}