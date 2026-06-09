<?php
declare(strict_types=1);

namespace Db\Adapters;

require "../../vendor/autoload.php";
require "../../core/base/BaseAdapter.php";
require "../../utils/load_dotenv.php";
require "../../core/utils/Logger.php";

use Core\Base\BaseAdapter;
use Core\Utils\Logger;
use PDO;
use PDOException;
use Exception;
use RuntimeException;

class PostgresqlAdapter extends BaseAdapter {
  private array $env_vars;
  private ?PDO $pdo = null;

  public function __construct() {
    try {
      $this->env_vars = load_dotenv();
      Logger::setLogLevel($this->env_vars['log_level'] ?? 'all');
      
      $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s",
        $this->env_vars["db_host"] ?? 'localhost',
        $this->env_vars["db_port"] ?? '5432',
        $this->env_vars["db_name"] ?? 'postgres'
      );
      
      $this->pdo = new PDO(
        $dsn,
        $this->env_vars["db_user"] ?? 'postgres',
        $this->env_vars["db_password"] ?? '',
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false
        ]
      );
      
      Logger::all("PgsqlAdapter inicializado", ['database' => $this->env_vars["db_name"]]);
    } catch (PDOException $e) {
      Logger::error("Erro ao conectar ao PostgreSQL", ['error' => $e->getMessage()]);
      throw new RuntimeException("Erro ao conectar ao PostgreSQL: " . $e->getMessage());
    } catch (Exception $e) {
      Logger::error("Erro ao iniciar PgsqlAdapter", ['error' => $e->getMessage()]);
      throw new RuntimeException("Erro ao iniciar o banco de dados: " . $e->getMessage());
    }
  }

  public function connect(array $config): void {
    if ($config) {
      $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s",
        $config['host'] ?? $this->env_vars["db_host"] ?? 'localhost',
        $config['port'] ?? $this->env_vars["db_port"] ?? '5432',
        $config['database'] ?? $this->env_vars["db_name"] ?? 'postgres'
      );
      
      $this->pdo = new PDO(
        $dsn,
        $config['user'] ?? $this->env_vars["db_user"] ?? 'postgres',
        $config['password'] ?? $this->env_vars["db_password"] ?? '',
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false
        ]
      );
      
      Logger::info("PostgreSQL conectado via config", ['database' => $config['database'] ?? 'unknown']);
    }
  }

  public function disconnect(): void {
    $this->pdo = null;
    Logger::all("PostgreSQL desconectado");
  }

  public function insert(string $table, array $data): array {
    Logger::all("Insert em {$table}", $data);
    
    $columns = implode(', ', array_keys($data));
    $placeholders = ':' . implode(', :', array_keys($data));
    
    $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders}) RETURNING id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($data);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $result['id'] ?? null;
    
    $result = $this->selectById($table, $id);
    
    Logger::info("Insert concluído em {$table}", ['id' => $id]);
    
    return $result;
  }

  public function select(string $table, array $where = []): array {
    Logger::all("Select em {$table}", ['where' => $where]);
    
    $sql = "SELECT * FROM {$table}";
    $params = [];
    
    if (!empty($where)) {
      $conditions = [];
      foreach ($where as $key => $value) {
        $conditions[] = "{$key} = :{$key}";
        $params[$key] = $value;
      }
      $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    
    $result = $stmt->fetchAll();
    
    Logger::all("Select retornou " . count($result) . " registros de {$table}");
    
    return $result;
  }

  public function selectById(string $table, $id): ?array {
    Logger::all("SelectById em {$table}", ['id' => $id]);
    
    $result = $this->select($table, ['id' => $id]);
    
    if (!$result[0]) {
      Logger::warning("Registro nao encontrado", ['table' => $table, 'id' => $id]);
    }
    
    return $result[0] ?? null;
  }

  public function update(string $table, $id, array $data): array {
    Logger::all("Update em {$table}", ['id' => $id, 'data' => $data]);
    
    $sets = [];
    foreach ($data as $key => $value) {
      $sets[] = "{$key} = :{$key}";
    }
    
    $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE id = :id";
    $data['id'] = $id;
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($data);
    
    $result = $this->selectById($table, $id);
    
    Logger::info("Update concluído em {$table}", ['id' => $id]);
    
    return $result;
  }

  public function delete(string $table, $id): bool {
    Logger::warning("Delete em {$table}", ['id' => $id]);
    
    $sql = "DELETE FROM {$table} WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $success = $stmt->execute(['id' => $id]);
    
    if ($success) {
      Logger::info("Delete concluído em {$table}", ['id' => $id]);
    } else {
      Logger::error("Delete falhou em {$table}", ['id' => $id]);
    }
    
    return $success;
  }

  public function join(string $mainTable, string $joinTable, string $foreignKey, string $select = '*'): array {
    Logger::all("Join entre {$mainTable} e {$joinTable}", [
      'foreignKey' => $foreignKey,
      'select' => $select
    ]);
    
    $on = "{$mainTable}.{$foreignKey} = {$joinTable}.id";
    $sql = "SELECT {$select} FROM {$mainTable} 
            INNER JOIN {$joinTable} ON {$on}";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    
    $result = $stmt->fetchAll();
    
    Logger::all("Join retornou " . count($result) . " registros");
    
    return $result;
  }

  public function getPdo(): ?PDO {
    return $this->pdo;
  }
}