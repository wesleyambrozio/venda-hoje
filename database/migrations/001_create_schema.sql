-- Venda Hoje CRM - Migration Completa
-- MySQL 8.x

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS venda_hoje CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE venda_hoje;

-- Planos de assinatura
CREATE TABLE IF NOT EXISTS planos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco_mensal DECIMAL(10,2) NOT NULL DEFAULT 0,
    max_usuarios INT UNSIGNED NOT NULL DEFAULT 5,
    max_leads INT UNSIGNED NOT NULL DEFAULT 500,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Empresas (multi-tenant)
CREATE TABLE IF NOT EXISTS empresas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    cnpj VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    telefone VARCHAR(20) DEFAULT NULL,
    plano_id INT UNSIGNED DEFAULT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (plano_id) REFERENCES planos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Assinaturas
CREATE TABLE IF NOT EXISTS assinaturas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT UNSIGNED NOT NULL,
    plano_id INT UNSIGNED NOT NULL,
    status ENUM('ativa','cancelada','suspensa','trial') NOT NULL DEFAULT 'trial',
    data_inicio DATE NOT NULL,
    data_fim DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    FOREIGN KEY (plano_id) REFERENCES planos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT UNSIGNED NOT NULL,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    perfil ENUM('sdr','closer','gestor','admin_saas') NOT NULL DEFAULT 'sdr',
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuario_email_empresa (email, empresa_id),
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Leads
CREATE TABLE IF NOT EXISTS leads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT UNSIGNED NOT NULL,
    nome VARCHAR(200) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    telefone VARCHAR(20) DEFAULT NULL,
    empresa_nome VARCHAR(200) DEFAULT NULL,
    origem VARCHAR(100) DEFAULT NULL,
    etapa_funil ENUM(
        'novo_lead','em_contato','qualificado','videochamada_agendada',
        'videochamada_realizada','proposta_enviada','negociacao','fechado','perdido'
    ) NOT NULL DEFAULT 'novo_lead',
    sdr_id INT UNSIGNED DEFAULT NULL,
    closer_id INT UNSIGNED DEFAULT NULL,
    valor_estimado DECIMAL(12,2) DEFAULT NULL,
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE,
    FOREIGN KEY (sdr_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (closer_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_leads_etapa (etapa_funil),
    INDEX idx_leads_empresa (empresa_id)
) ENGINE=InnoDB;

-- Etapas do caminho (trilha de atendimento)
CREATE TABLE IF NOT EXISTS etapas_caminho (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT UNSIGNED DEFAULT NULL,
    etapa_funil ENUM(
        'novo_lead','em_contato','qualificado','videochamada_agendada',
        'videochamada_realizada','proposta_enviada','negociacao','fechado','perdido'
    ) NOT NULL,
    ordem INT UNSIGNED NOT NULL DEFAULT 0,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Atendimentos
CREATE TABLE IF NOT EXISTS atendimentos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    canal ENUM('telefone','whatsapp','email','videochamada','presencial','outro') NOT NULL DEFAULT 'telefone',
    resultado ENUM('concluido','follow_up','nao_atendeu','cancelado') NOT NULL DEFAULT 'concluido',
    resumo TEXT,
    etapa_anterior VARCHAR(50) DEFAULT NULL,
    etapa_nova VARCHAR(50) DEFAULT NULL,
    encerrado TINYINT(1) NOT NULL DEFAULT 0,
    data_atendimento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_atendimentos_lead (lead_id)
) ENGINE=InnoDB;

-- Etapas marcadas no atendimento
CREATE TABLE IF NOT EXISTS atendimento_etapas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    atendimento_id INT UNSIGNED NOT NULL,
    etapa_caminho_id INT UNSIGNED NOT NULL,
    concluida TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (atendimento_id) REFERENCES atendimentos(id) ON DELETE CASCADE,
    FOREIGN KEY (etapa_caminho_id) REFERENCES etapas_caminho(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Gatilhos
CREATE TABLE IF NOT EXISTS gatilhos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT UNSIGNED DEFAULT NULL,
    categoria VARCHAR(100) NOT NULL,
    etapa_funil VARCHAR(50) DEFAULT NULL,
    titulo VARCHAR(200) NOT NULL,
    texto TEXT NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Favoritos de gatilhos por usuário
CREATE TABLE IF NOT EXISTS gatilho_favoritos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    gatilho_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_favorito (usuario_id, gatilho_id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (gatilho_id) REFERENCES gatilhos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Gatilhos usados no atendimento
CREATE TABLE IF NOT EXISTS atendimento_gatilhos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    atendimento_id INT UNSIGNED NOT NULL,
    gatilho_id INT UNSIGNED NOT NULL,
    funcionou TINYINT(1) DEFAULT NULL,
    observacao TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (atendimento_id) REFERENCES atendimentos(id) ON DELETE CASCADE,
    FOREIGN KEY (gatilho_id) REFERENCES gatilhos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Critérios de diagnóstico (13 critérios em 5 blocos)
CREATE TABLE IF NOT EXISTS criterios_diagnostico (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT UNSIGNED DEFAULT NULL,
    bloco VARCHAR(100) NOT NULL,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    peso TINYINT UNSIGNED NOT NULL DEFAULT 5,
    ordem INT UNSIGNED NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Opções de resposta por critério (valor 1-5)
CREATE TABLE IF NOT EXISTS criterio_opcoes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    criterio_id INT UNSIGNED NOT NULL,
    valor TINYINT UNSIGNED NOT NULL,
    rotulo VARCHAR(100) NOT NULL,
    pergunta_sugerida TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (criterio_id) REFERENCES criterios_diagnostico(id) ON DELETE CASCADE,
    UNIQUE KEY uk_criterio_valor (criterio_id, valor)
) ENGINE=InnoDB;

-- Diagnósticos realizados
CREATE TABLE IF NOT EXISTS diagnosticos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    score_final DECIMAL(5,2) NOT NULL DEFAULT 0,
    classificacao ENUM('quente','morno','frio') NOT NULL DEFAULT 'frio',
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_diagnosticos_lead (lead_id)
) ENGINE=InnoDB;

-- Respostas do diagnóstico
CREATE TABLE IF NOT EXISTS diagnostico_respostas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    diagnostico_id INT UNSIGNED NOT NULL,
    criterio_id INT UNSIGNED NOT NULL,
    valor TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (diagnostico_id) REFERENCES diagnosticos(id) ON DELETE CASCADE,
    FOREIGN KEY (criterio_id) REFERENCES criterios_diagnostico(id) ON DELETE CASCADE,
    UNIQUE KEY uk_diag_criterio (diagnostico_id, criterio_id)
) ENGINE=InnoDB;

-- Follow-ups
CREATE TABLE IF NOT EXISTS followups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lead_id INT UNSIGNED NOT NULL,
    atendimento_id INT UNSIGNED DEFAULT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    data_retorno DATETIME NOT NULL,
    gatilho_sugerido_id INT UNSIGNED DEFAULT NULL,
    gatilho_funcionou TINYINT(1) DEFAULT NULL,
    status ENUM('pendente','concluido','cancelado') NOT NULL DEFAULT 'pendente',
    observacao TEXT,
    alerta_enviado TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (atendimento_id) REFERENCES atendimentos(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (gatilho_sugerido_id) REFERENCES gatilhos(id) ON DELETE SET NULL,
    INDEX idx_followups_data (data_retorno, status)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
