<?php
// db/schemas/postgresql_schema.php

namespace Db\Schemas;

class PostgresqlSchema {
  public static function getCreationString(): string {
    return <<<SQL
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

CREATE TABLE IF NOT EXISTS pessoa (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    cpf VARCHAR(11) NOT NULL UNIQUE,
    nome VARCHAR(75) NOT NULL,
    email VARCHAR(75) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    data_nascimento DATE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS administrador (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nome VARCHAR(75) NOT NULL,
    email VARCHAR(75) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS funcao (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS vinculo (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    id_pessoa UUID NOT NULL,
    id_funcao INT NOT NULL,
    id_administrador UUID,
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_resposta TIMESTAMP NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pendente',
    justificativa TEXT,
    data_desligamento DATE,
    
    FOREIGN KEY (id_pessoa) REFERENCES pessoa(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_funcao) REFERENCES funcao(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_administrador) REFERENCES administrador(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS tipo_material (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    preco_kg DECIMAL(10,2) NOT NULL,
    pontos_kg INT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS ponto_coleta (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    cep VARCHAR(10) NOT NULL,
    endereco_completo TEXT NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    telefone VARCHAR(20),
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS coleta_residencial (
    id SERIAL PRIMARY KEY,
    id_ponto_coleta UUID NOT NULL,
    id_vinculo UUID,
    id_material INT NOT NULL,
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_realizacao TIMESTAMP NULL,
    quantidade_kg DECIMAL(10,2),
    descricao TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'pendente',

    FOREIGN KEY (id_ponto_coleta) REFERENCES ponto_coleta(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_vinculo) REFERENCES vinculo(id) ON DELETE SET NULL,
    FOREIGN KEY (id_material) REFERENCES tipo_material(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS entrega_presencial (
    id SERIAL PRIMARY KEY,
    id_vinculo UUID NOT NULL,
    id_material INT NOT NULL,
    data_entrega TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    quantidade_kg DECIMAL(10,2) NOT NULL,
    
    FOREIGN KEY (id_vinculo) REFERENCES vinculo(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_material) REFERENCES tipo_material(id) ON DELETE RESTRICT
);

CREATE INDEX idx_vinculo_pessoa ON vinculo(id_pessoa);
CREATE INDEX idx_vinculo_status ON vinculo(status);
CREATE INDEX idx_coleta_status ON coleta_residencial(status);
CREATE INDEX idx_coleta_vinculo ON coleta_residencial(id_vinculo);
CREATE INDEX idx_entrega_vinculo ON entrega_presencial(id_vinculo);
CREATE INDEX idx_pessoa_email ON pessoa(email);
CREATE INDEX idx_pessoa_ativo ON pessoa(ativo);
CREATE INDEX idx_pontocoleta_cep ON ponto_coleta(cep);
CREATE INDEX idx_pontocoleta_ativo ON ponto_coleta(ativo);
CREATE INDEX idx_pontocoleta_email ON ponto_coleta(email);
CREATE INDEX idx_funcao_ativo ON funcao(ativo);
CREATE INDEX idx_material_ativo ON tipo_material(ativo);
CREATE INDEX idx_administrador_email ON administrador(email);
SQL;
  }
}