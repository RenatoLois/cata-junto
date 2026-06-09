<?php
declare(strict_types=1);

namespace App\Models;

use Core\Base\BaseModel;

class PontoColetaModel extends BaseModel {
  protected array $fillable = [
    'id', 'cep', 'endereco_completo', 'email',
    'senha_hash', 'telefone', 'data_registro', 'ativo'
  ];
  
  protected array $hidden = ['senha_hash'];
  
  protected array $casts = [
    'id' => 'string',
    'ativo' => 'bool',
    'data_registro' => 'datetime'
  ];
  
  public function verifyPassword(string $senha): bool {
    return password_verify($senha, $this->attributes['senha_hash'] ?? '');
  }
  
  public function setSenha(string $senha): void {
    $this->attributes['senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
  }
  
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