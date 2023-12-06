<?php
class Database
{
  private $connection;

  public function __construct()
  {
    $config = require '../../config/config.php';
    $dbConfig = $config['db'];

    $this->connection = new PDO(
      "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}",
      $dbConfig['user'],
      $dbConfig['password'],
      $dbConfig['options'],
    );
  }

  public function query($sql)
  {
    return $this->connection->query($sql);
  }

  public function prepare($sql)
  {
    return $this->connection->prepare($sql);
  }

  public function execute($statement, $parameters = [])
  {
    return $statement->execute($parameters);
  }

  public function fetch($statement)
  {
    return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public function fetchAll($statement)
  {
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }
}
