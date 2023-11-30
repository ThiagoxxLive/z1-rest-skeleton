<?php

namespace App\Db;

class Pagination {

    /** 
     * Número máximo de registros por página.
     * @var int
     */
    private $limit;

    /** 
     * Quantidade total de resultados do banco de dados.
     * @var int
     */
    private $results;

    /**
     * Quantidade de páginas
     * @var integer
     */
    private $pages;

    /**
     * Página atual.
     * @var int
     */
    private $currentPage;

    /**
     * Método construtor da classe.
     * @param integer $results
     * @param integer $currentPage
     * @param integer $limit
     */
    public function __construct(int $results, int $currentPage = 1, int $limit = 10) {
        $this->results = $results;
        $this->limit = $limit;
        $this->currentPage = (is_numeric($currentPage) && $currentPage > 0) ? $currentPage : 1;
        $this->calculate();
    }

    /**
     * Calcula o total de páginas e verificar se não excede o número de paginas.
     * @return void
     */
    private function calculate() {
        $this->pages = $this->results > 0 ? ceil($this->results / $this->limit) : 1;

        $this->currentPage = $this->currentPage <= $this->pages ? $this->currentPage : $this->pages;
    }


    /**
     * Método responsável por retornar o OFFSET da paginação.
     * @return string
     */
    public function getLimit() : string {

        $offset = ($this->limit * ($this->currentPage - 1));
        return "{$offset},{$this->limit}";
    }
}

