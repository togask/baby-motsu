<?php
class ColorModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function getAllColors()
  {
    $stmt = $this->db->prepare("SELECT * FROM COLOR");
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }
}