<?php
declare(strict_types=1);

namespace App\Models;

use Core\Base\BaseModel;

class VinculoModel extends BaseModel {
  protected array $fillable = [
    'id', 'id_pessoa', 'id_funcao', 'id_administrador',
    'data_solicitacao', 'data_resposta', 'status',
    'justificativa', 'data_desligamento'
  ];
  
  protected array $hidden = [];
  
  protected array $casts = [
    'id' => 'string',
    'id_pessoa' => 'string',
    'id_funcao' => 'int',
    'id_administrador' => 'string',
    'data_solicitacao' => 'datetime',
    'data_resposta' => 'datetime',
    'data_desligamento' => 'date'
  ];
  
  public function isPending(): bool {
    return ($this->status ?? '') === 'pendente';
  }
  
  public function isApproved(): bool {
    return ($this->status ?? '') === 'aprovado';
  }
  
  public function isRejected(): bool {
    return ($this->status ?? '') === 'recusado';
  }
  
  public function isCanceled(): bool {
    return ($this->status ?? '') === 'cancelado';
  }
  
  public function approve(int $id_administrador, ?string $justificativa = null): void {
    $this->attributes['status'] = 'aprovado';
    $this->attributes['data_resposta'] = date('Y-m-d H:i:s');
    $this->attributes['id_administrador'] = $id_administrador;
    $this->attributes['justificativa'] = $justificativa;
  }
  
  public function reject(int $id_administrador, string $justificativa): void {
    $this->attributes['status'] = 'recusado';
    $this->attributes['data_resposta'] = date('Y-m-d H:i:s');
    $this->attributes['id_administrador'] = $id_administrador;
    $this->attributes['justificativa'] = $justificativa;
  }
  
  public function cancel(string $justificativa): void {
    $this->attributes['status'] = 'cancelado';
    $this->attributes['justificativa'] = $justificativa;
  }
  
  public function desligar(string $data_desligamento): void {
    $this->attributes['data_desligamento'] = $data_desligamento;
  }
  
  public function isAtivo(): bool {
    return $this->isApproved() && ($this->attributes['data_desligamento'] ?? null) === null;
  }
}