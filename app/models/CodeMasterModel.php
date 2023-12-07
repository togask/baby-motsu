<?php
class CodeMasterModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  /**
   * すべてのコードマスタデータを取得する
   * @return array コードマスタデータの配列
   */
  public function getAllCodeMasterData()
  {
    $stmt = $this->db->prepare("SELECT * FROM CODE_MASTER");
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }
}
