<?php
class MajorCategoryModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  public function getAllMajorCategories()
  {
    $stmt = $this->db->prepare("SELECT * FROM MAJOR_CATEGORY");
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }
}
