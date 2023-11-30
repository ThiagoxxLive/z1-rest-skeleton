<?php

namespace App\Repositories\Example;

use App\Db\Connections\Connection;

class ExampleRepository {

    private Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;        
    }

    private function getConnection() : Connection {
        return $this->connection;
    }
    
    public function findAll() : array {

        $sql = "SELECT * FROM tb_example";
        return $this->getConnection()->executeQuery($sql)->fetchAll();
    }

    public function create(array $data) : int {
        return $this->getConnection()->create("tb_example", $data);
    }

    public function update (int $id, array $data) {

        return $this->getConnection()->update("tb_example", $data, [
            'id' => $id
        ]);

    }
}

