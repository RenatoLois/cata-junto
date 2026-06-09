<?php
declare(strict_types=1);

namespace App\Models;

use Core\Base\BaseModel;

class FuncaoModel extends BaseModel {
  protected array $fillable = [
    'id', 'nome', 'ativo'
  ];
  
  protected array $hidden = [];
  
  protected array $casts = [
    'id' => 'int',
    'ativo' => 'bool'
  ];
  
  public function isActive(): bool {
    return (bool) ($this->attributes['ativo'] ?? true);
  }
  
  public function activate(): void {
    $this->attributes['ativo'] = true;
  }
  
  public function deactivate(): void {
    $this->attributes['ativo'] = false;
  }
}