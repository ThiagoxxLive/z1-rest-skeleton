<?php

namespace App\Services\Example;

use App\Http\Request;
use App\Repositories\Example\ExampleRepository;

class ExampleService {

    private ExampleRepository $repository;

    public function __construct(ExampleRepository $repository) {
        $this->repository = $repository;         
    }

    private function getRepository() : ExampleRepository {
        return $this->repository;
    }

    /**
     * @return array
     */
    public function findAll() : array {

        //Retorna os dados da query do banco de dados.
        return $this->getRepository()->findAll();
    }

    public function create(Request $request) : array {

        //Captura todos os dados do form da request.
        $data = $request->getAttributes();

        //Captura o ID do registro inserido.
        $id = $this->getRepository()->create($data);

        //Retorna os dados da requisição e o ID.
        return array_merge(['id' => $id], $data);
    }

    public function update(int $id, Request $request) {

        //Captura todos os dados do form da request.
        $data = $request->getAttributes();

        //Retorna nulo, conforme HTTP 204.
        return $this->getRepository()->update($id,$data);
    }
}

