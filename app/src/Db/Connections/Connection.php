<?php 

namespace App\Db\Connections;

use App\Exceptions\HttpNotFoundException;
use App\Utils\Logger\Logger;
use Exception;
use PDO;
use PDOStatement;

class Connection {

    private string $dbName;
    private $connection;
    private bool $result;

    public function getResult() : bool {
        return $this->result;
    }

    public function __construct(string $dbName) {
        $this->dbName = $dbName;
    }

    /**
     * Retorna a conexão.
     * @return PDO
     */
    public function getConnection() : PDO {

        try {
            
            $this->connection = new PDO('mysql:host='.getenv('DB_HOST').';dbname='.$this->dbName, getenv('DB_USER'), getenv('DB_PASS')); 
            return $this->connection;

        }catch(Exception $e) {
            Logger::error($e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Restorna o resultado da query.
     * @param string $sql
     * @param array $whereFields
     * @return PDOStatement
     */
    public function executeQuery(string $sql, array $whereFields = [], $fetchMode = PDO::FETCH_ASSOC) : PDOStatement  {

        try {

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->setFetchMode($fetchMode);

            if(!empty($whereFields)) {

                foreach($whereFields as $key => $field) {

                    $value = $field;
                    
                    if($key == 'limit' || $key == 'offset') {
                        $value = (int) $field;
                    }
                    
                    $stmt->bindValue(":{$key}", $value, (is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
                }
            }
            
            $stmt->execute();

            if($stmt->rowCount() == 0) {
                throw new HttpNotFoundException("Nenhum dado foi encontrado.", 404);
            }
            
            return $stmt;

        }catch(Exception $e) {
            throw new Exception($e->getMessage(), 404);
        }

    }

    /**
     * Retorna todos os resultados da tabela.
     * @param string $table
     * @param string $limit
     * @return PDOStatement
     */ 
    public function execute(string $table, string $limit = "0") : PDOStatement {

        $sql = "SELECT * FROM {$table}";

        if($limit > 0) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $stmt;
    }

    public function create(string $table, array $data) : int {

        $columns = implode(",",array_keys($data));
        $binds = array_keys($data);

        $binds = array_map(function($bind){
            return ":{$bind}";
        },$binds);

        $bind = implode(",", $binds);
        $sql = "INSERT INTO {$table}($columns) VALUES($bind)";

        try {

            $stmt = $this->getConnection()->prepare($sql);
            $stmt->execute($data);
            $this->result = true;
            return $this->connection->lastInsertId();

        }catch(Exception $e) {
            $this->result = false;
            throw new Exception($e->getMessage(), 500);
        }

        $this->result = false;
        return 0;

    }

    public function update(string $table, array $data, array $whereFields) {

    $columns = array_map(function($column, $value) {
        return "$column = :$column";
    }, array_keys($data), array_values($data));

    $columns = implode(", ", $columns);
    $whereColumns = array_keys($whereFields);
    $whereValues = array_values($whereFields);

    $where = "WHERE {$whereColumns[0]} = :{$whereColumns[0]}";

    $sql = "UPDATE $table SET $columns $where";

    try {
        $stmt = $this->getConnection()->prepare($sql);

        foreach($data as $column => $value) {
            $stmt->bindValue(":$column", $value);
        }

        $stmt->bindValue(":{$whereColumns[0]}", $whereValues[0]);
        $stmt->execute();
    } catch(Exception $e) {
        throw new Exception($e->getMessage(), 500);
    }
}

}