<?php
class MajorCategoryModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function getAllMajorCategories()
  {
    $stmt = $this->db->prepare("SELECT * FROM MAJOR_CATEGORY");
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }
}
