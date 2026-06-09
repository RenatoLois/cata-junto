<?php
declare(strict_types=1);

namespace Core\Base;

abstract class BaseAdapter {
  protected array $config = [];

  abstract public function connect(array $config): void;
  abstract public function disconnect(): void;
	
  abstract public function insert(string $table, array $data): array;
  abstract public function select(string $table, array $where = []): array;
  abstract public function selectById(string $table, int $id): ?array;
  abstract public function update(string $table, int $id, array $data): array;
  abstract public function delete(string $table, int $id): bool;
  abstract public function join(string $mainTable, string $joinTable, string $foreignKey, string $select = '*'): array;
  
  public function setConfig(array $config): void {
    $this->config = $config;
  }
  
  public function getConfig(): array {
    return $this->config;
  }
}