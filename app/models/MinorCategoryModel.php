<?php
class MinorCategoryModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  public function getAllMinorCategories()
  {
    $stmt = $this->db->prepare("SELECT * FROM MINOR_CATEGORY");
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }
}
