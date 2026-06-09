<?php
declare(strict_types=1);

namespace App\Models;

use Core\Base\BaseModel;

class PessoaModel extends BaseModel {
  protected array $fillable = [
    'id', 'cpf', 'nome', 'email', 'telefone',
    'data_nascimento', 'senha_hash', 'ativo'
  ];
  
  protected array $hidden = ['senha_hash'];
  
  protected array $casts = [
    'id' => 'string',
    'ativo' => 'bool',
    'data_nascimento' => 'date'
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
  
  public function getIdade(): ?int {
    if (!$this->data_nascimento) {
      return null;
    }
    $hoje = new \DateTime();
    $nascimento = new \DateTime($this->data_nascimento);
    return $hoje->diff($nascimento)->y;
  }
  
  public function getCpfFormatado(): string {
    $cpf = $this->cpf ?? '';
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
  }
}