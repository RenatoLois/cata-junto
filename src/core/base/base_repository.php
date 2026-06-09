<?php
declare(strict_types=1);

namespace Core\Base;

use Db\Database;

abstract class BaseRepository {
  protected Database $db;
  protected string $table;
  
  public function __construct(Database $db) {
    $this->db = $db;
  }
  
  public function findAll(): array {
    return $this->db->select($this->table);
  }
  
  public function findBypk($pk): ?array {
    return $this->db->selectBypk($this->table, $pk);
  }
  
  public function findWhere(array $where): array {
    return $this->db->select($this->table, $where);
  }
  
  public function create(array $data): array {
    return $this->db->insert($this->table, $data);
  }
  
  public function update($pk, array $data): array {
    return $this->db->update($this->table, $pk, $data);
  }
  
  public function delete($pk): bool {
    return $this->db->delete($this->table, $pk);
  }
}