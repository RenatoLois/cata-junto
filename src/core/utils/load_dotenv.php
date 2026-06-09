<?php 
declare(strict_types=1);

namespace Core\Utils;

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dotenv\Dotenv;
use Exception;
use RuntimeException;

function load_dotenv(): array{
  try {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
    $dotenv->load();
    return [
      'supabase_url' => $_ENV['SUPABASE_URL'] ?? null,
      'supabase_key' => $_ENV['SUPABASE_KEY'] ?? null,
      'db_url'       => $_ENV['DB_URL']       ?? null,
      'db_host'      => $_ENV['DB_HOST']      ?? 'localhost',
      'db_port'      => $_ENV['DB_PORT']      ?? '3306',
      'db_name'      => $_ENV['DB_NAME']      ?? 'database',
      'db_key'       => $_ENV['DB_KEY']       ?? null,
      'db_user'      => $_ENV['DB_USER']      ?? 'root',
      'db_password'  => $_ENV['DB_PASSWORD']  ?? 'root',
      'app_mode'     => $_ENV['APP_MODE']     ?? 'debug',
      'app_env'      => $_ENV['APP_ENV']      ?? 'testing',
      'log_level'    => $_ENV['log_level']    ?? 'all',
    ];
  } catch(Exception $e) {
    throw new RuntimeException("Erro ao carregar variáveis de .env: " . $e->getMessage());
  }
}
