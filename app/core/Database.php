<?php
class Database
{
  private $connection;

  public function __construct()
  {
    try {
      $config = require 'config/config.php';
      $dbConfig = $config['db'];

      $connect = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['dbname'];
      $this->connection = new PDO(
        $connect,
        $dbConfig['user'],
        $dbConfig['password'],
        $dbConfig['options'],
      );
    } catch (PDOException $e) {
      throw $e;
    }
  }

  public function query($sql)
  {
    try {
      return $this->connection->query($sql);
    } catch (PDOException $e) {
      // エラーハンドリング
      throw $e;
    }
  }

  public function prepare($sql)
  {
    try {
      return $this->connection->prepare($sql);
    } catch (PDOException $e) {
      // エラーハンドリング
      throw $e;
    }
  }

  public function execute($stmt, $parameters = [])
  {
    try {
      foreach ($parameters as $param => $value) {
        $param = ":" . $param;
        if ($value === null) {
          $stmt->bindValue($param, $value, PDO::PARAM_NULL);
        } else {
          $stmt->bindValue($param, $value);
        }
      }
      $stmt->execute();
    } catch (PDOException $e) {
      // エラーハンドリング
      throw $e;
    }
  }

  public function fetch($stmt)
  {
    try {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      // エラーハンドリング
      throw $e;
    }
  }

  public function fetchAll($stmt)
  {
    try {
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      // エラーハンドリング
      throw $e;
    }
  }
}
