<?php
declare(strict_types=1);

namespace App\Models;

use Core\Base\BaseModel;

class ColetaResidencialModel extends BaseModel {
  protected array $fillable = [
    'id', 'id_ponto_coleta', 'id_vinculo', 'id_material',
    'data_solicitacao', 'data_realizacao', 'quantidade_kg',
    'descricao', 'status'
  ];
  
  protected array $hidden = [];
  
  protected array $casts = [
    'id' => 'int',
    'id_ponto_coleta' => 'string',
    'id_vinculo' => 'string',
    'id_material' => 'int',
    'data_solicitacao' => 'datetime',
    'data_realizacao' => 'datetime',
    'quantidade_kg' => 'float'
  ];
  
  public function isPending(): bool {
    return ($this->status ?? '') === 'pendente';
  }
  
  public function isCompleted(): bool {
    return ($this->status ?? '') === 'concluida';
  }
  
  public function isCanceled(): bool {
    return ($this->status ?? '') === 'cancelado';
  }
  
  public function complete(float $quantidade_kg, string $descricao = null): void {
    $this->attributes['status'] = 'concluida';
    $this->attributes['data_realizacao'] = date('Y-m-d H:i:s');
    $this->attributes['quantidade_kg'] = $quantidade_kg;
    $this->attributes['descricao'] = $descricao;
  }
  
  public function cancel(): void {
    $this->attributes['status'] = 'cancelado';
  }
}