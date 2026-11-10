-- Tema (empresa) + abordagem de mensagem (lead)
-- Idempotente onde possível

SET @db := DATABASE();

-- theme_mode
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'empresas' AND COLUMN_NAME = 'theme_mode'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE empresas ADD COLUMN theme_mode ENUM(''light'',''dark'') NOT NULL DEFAULT ''dark'' AFTER ativo',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- color_palette
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'empresas' AND COLUMN_NAME = 'color_palette'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE empresas ADD COLUMN color_palette VARCHAR(32) NOT NULL DEFAULT ''rose'' AFTER theme_mode',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- abordagem_tipo
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'leads' AND COLUMN_NAME = 'abordagem_tipo'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE leads ADD COLUMN abordagem_tipo ENUM(''descoberta'',''pergunta_personalizada'',''case_sucesso'') DEFAULT NULL AFTER observacoes',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- abordagem_texto
SET @exists := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'leads' AND COLUMN_NAME = 'abordagem_texto'
);
SET @sql := IF(@exists = 0,
  'ALTER TABLE leads ADD COLUMN abordagem_texto TEXT DEFAULT NULL AFTER abordagem_tipo',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

INSERT INTO gatilhos (empresa_id, categoria, etapa_funil, titulo, texto)
SELECT NULL, 'Pergunta', 'novo_lead', 'Pergunta personalizada',
       'Vi que sua empresa tem crescido — o que mais tem atrapalhado o time comercial neste momento?'
WHERE NOT EXISTS (
    SELECT 1 FROM gatilhos g WHERE g.titulo = 'Pergunta personalizada' AND g.etapa_funil = 'novo_lead'
);
