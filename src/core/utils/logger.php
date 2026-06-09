<?php
declare(strict_types=1);

namespace Core\Utils;

class Logger {
  private static string $logFile = __DIR__ . '/../../logs/app.log';
  private static string $logLevel = 'all'; // all, info, warning, error
  
  public static function setLogLevel(string $level): void {
    $allowed = ['all', 'info', 'warning', 'error'];
    if (in_array($level, $allowed)) {
      self::$logLevel = $level;
    }
  }
  
  private static function shouldLog(string $level): bool {
    static $levels = ['all' => 0, 'info' => 1, 'warning' => 2, 'error' => 3];
    $current = $levels[self::$logLevel] ?? 0;
    $target = $levels[$level] ?? 0;
    return $target >= $current;
  }
  
  public static function all(string $message, array $context = []): void {
    if (self::shouldLog('all')) {
      self::write('ALL', $message, $context);
    }
  }
  
  public static function info(string $message, array $context = []): void {
    if (self::shouldLog('info')) {
      self::write('INFO', $message, $context);
    }
  }
  
  public static function warning(string $message, array $context = []): void {
    if (self::shouldLog('warning')) {
      self::write('WARNING', $message, $context);
    }
  }
  
  public static function error(string $message, array $context = []): void {
    if (self::shouldLog('error')) {
      self::write('ERROR', $message, $context);
    }
  }
  
  private static function write(string $level, string $message, array $context = []): void {
    $log = sprintf(
      "[%s] %s: %s %s\n",
      date('Y-m-d H:i:s'),
      $level,
      $message,
      !empty($context) ? json_encode($context) : ''
    );
    
    $logDir = dirname(self::$logFile);
    if (!is_dir($logDir)) {
      mkdir($logDir, 0755, true);
    }
    
    error_log($log, 3, self::$logFile);
  }
  
  public static function setLogFile(string $file): void {
    self::$logFile = $file;
  }
}