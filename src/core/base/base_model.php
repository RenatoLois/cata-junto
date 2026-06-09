<?php
declare(strict_types=1);

namespace Core\Base;

abstract class BaseModel {
  protected array $attributes = [];
  protected array $fillable = [];
  protected array $hidden = [];
  protected array $casts = [];
  
  public function __construct(array $attributes = []) {
    $this->fill($attributes);
  }
  
  public function fill(array $attributes): void {
    foreach ($attributes as $key => $value) {
      if (in_array($key, $this->fillable)) {
        $this->attributes[$key] = $this->cast($key, $value);
      }
    }
  }
  
  protected function cast(string $key, $value) {
    if (isset($this->casts[$key])) {
      return match($this->casts[$key]) {
        'int' => (int) $value,
        'float' => (float) $value,
        'string' => (string) $value,
        'bool' => (bool) $value,
        'array' => (array) $value,
        'json' => json_decode($value, true),
        'date' => date('Y-m-d', strtotime($value)),
        'datetime' => date('Y-m-d H:i:s', strtotime($value)),
        default => $value
      };
    }
    return $value;
  }
  
  public function __get(string $name) {
    return $this->attributes[$name] ?? null;
  }
  
  public function __set(string $name, $value): void {
    if (in_array($name, $this->fillable)) {
      $this->attributes[$name] = $this->cast($name, $value);
    }
  }
  
  public function __isset(string $name): bool {
    return isset($this->attributes[$name]);
  }
  
  public function toArray(): array {
    $data = $this->attributes;
    
    foreach ($this->hidden as $key) {
      unset($data[$key]);
    }
    
    return $data;
  }
  
  public function toJson(): string {
    return json_encode($this->toArray());
  }
  
  public function getAttributes(): array {
    return $this->attributes;
  }
  
  public function getFillable(): array {
    return $this->fillable;
  }
}