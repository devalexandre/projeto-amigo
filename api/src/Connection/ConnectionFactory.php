<?php

 namespace WebService\Connection;

 use PDO;
 use Exception;
 use \WebService\Configuration\Configuration;

 final class ConnectionFactory {
   private $connection;

   public function __construct(string $connection) {
     $databases = Configuration::read('databases');
     $config = $databases[$connection];
     $this->connect($config);
   }

   public function getConnection() {
     return $this->connection;
   }

  private function connect(array $config) {
    try {
      $dsn = $this->getDsn($config);
      $this->connection = new PDO($dsn, $config['user'], $config['password']);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    } catch (PDOException $ex) {
      echo 'Houve um erro: '.$ex->getMessage();
      exit;
    }
  }

  private function getDsn(array $config) {
    if ($config['database'] === 'mysql') {
      $dsn = 'mysql:host='.$config['host'].';port='.$config['port'].';dbname='.$config['dbname'].';charset='.$config['charset'].';';
    } elseif ($config['database'] === 'firebird') {
      $dsn = 'firebird:dbname='.$config['host'].':'.$config['dbname'].';charset='.$config['charset'].';';
    } else {
      throw new Exception('Conexão não encontrada', 1);
    }
    return $dsn;
  }
 }
?>
