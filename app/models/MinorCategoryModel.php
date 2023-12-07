<?php
class MinorCategoryModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function getAllMinorCategories()
  {
    $stmt = $this->db->prepare("SELECT * FROM MINOR_CATEGORY");
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }
}
