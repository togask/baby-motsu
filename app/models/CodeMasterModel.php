<?php
class CodeMasterModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  /**
   * すべてのコードマスタデータを取得する
   * @return array コードマスタデータの配列
   */
  public function getAllCodeMasterData()
  {
    $stmt = $this->db->prepare("SELECT * FROM CODE_MASTER");
    $this->db->execute($stmt);
    $results = $this->db->fetchAll($stmt);

    foreach ($results as $key => $value) {
      $results[$key]['isChecked'] = false;
    }

    return $results;
  }
}
