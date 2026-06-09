<?php
declare(strict_types=1);

namespace App\Models;

use Core\Base\BaseModel;

class EntregaPresencialModel extends BaseModel {
  protected array $fillable = [
    'id', 'id_vinculo', 'id_material', 'data_entrega', 'quantidade_kg'
  ];
  
  protected array $hidden = [];
  
  protected array $casts = [
    'id' => 'int',
    'id_vinculo' => 'string',
    'id_material' => 'int',
    'data_entrega' => 'datetime',
    'quantidade_kg' => 'float'
  ];
  
  public function getValorTotal(float $preco_kg): float {
    return $preco_kg * $this->quantidade_kg;
  }
  
  public function getPontosTotal(int $pontos_kg): int {
    return (int) ($pontos_kg * $this->quantidade_kg);
  }
}