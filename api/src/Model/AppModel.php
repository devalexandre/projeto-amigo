<?php

namespace WebService\Model;

use PDO;
use WebService\Connection\ConnectionFactory;

class AppModel
{
    private $connection;

    public function __construct($strNameConnection = 'mysql')
    {
        $this->setConnection($strNameConnection);
    }

    public function setConnection(string $strNameConnection)
    {
        $objConnectionFactory = new ConnectionFactory($strNameConnection);
        $this->connection = $objConnectionFactory->getConnection();
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function execute(string $sql, $arrayParameters = null)
    {
        try {
            $statement = $this->connection->prepare($sql);
            if (is_null($arrayParameters)) {
                $statement->execute();
            } else {
                $statement->execute($arrayParameters);
            }
            return $statement;
        } catch (PDOException $e) {
            echo 'Ocorreu um erro ao executar a consulta. Erro: '.$e->getMessage();
            exit;
        }
    }

    public function select(string $sql, $arrayParameters = null)
    {
        $statement = $this->execute($sql, $arrayParameters);
        $arrayResult = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($arrayResult) > 0) {
            return $arrayResult;
        }
        return false;
    }

    public function verificaObj($arr)
    {
        if (is_object($arr)) {
            foreach ($arr as $key => $value) {
                if ((empty($value) && $value !== '0' && $value !== 0) || $value == null || $value == "" || $value == undefined) {
                    return true;
                    break;
                }
            }
            return false;
        }
    }
}
