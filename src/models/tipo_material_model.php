<?php
declare(strict_types=1);

namespace App\Models;

use Core\Base\BaseModel;

class TipoMaterialModel extends BaseModel {
  protected array $fillable = [
    'id', 'nome', 'preco_kg', 'pontos_kg', 'ativo'
  ];
  
  protected array $hidden = [];
  
  protected array $casts = [
    'id' => 'int',
    'preco_kg' => 'float',
    'pontos_kg' => 'int',
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
  
  public function getValorTotal(float $quantidade_kg): float {
    return $this->preco_kg * $quantidade_kg;
  }
  
  public function getPontosTotal(float $quantidade_kg): int {
    return (int) ($this->pontos_kg * $quantidade_kg);
  }
}