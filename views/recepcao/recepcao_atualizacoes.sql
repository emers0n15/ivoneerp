-- =====================================================
-- SCRIPT DE ATUALIZAÇÕES - MÓDULO DE RECEPÇÃO
-- Arquivo: recepcao_atualizacoes.sql
-- Descrição: Script contendo apenas as atualizações e alterações
-- Data: 2025-01-XX
-- =====================================================
-- 
-- INSTRUÇÕES:
-- 1. Este arquivo contém APENAS as atualizações (novas tabelas e colunas)
-- 2. Execute este script se você já tem uma instalação anterior
-- 3. Para instalação nova, use recepcao.sql
-- 4. Execute no phpMyAdmin na aba "SQL"
-- =====================================================

-- Selecionar o banco de dados
USE `ivoneerp`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- =====================================================
-- TABELAS PARA FATURAS DA RECEPÇÃO (seguindo lógica da farmácia)
-- =====================================================

-- Tabela principal de faturas da recepção (similar a factura da farmácia)
CREATE TABLE IF NOT EXISTS `factura_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_doc` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `disconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `serie` int(11) NOT NULL,
  `prazo` varchar(255) NOT NULL,
  `metodo` varchar(255) NOT NULL,
  `condicoes` varchar(255) NOT NULL,
  `apolice` varchar(255) DEFAULT NULL,
  `codigo1` varchar(255) DEFAULT NULL,
  `codigo2` varchar(255) DEFAULT NULL,
  `codigo3` varchar(255) DEFAULT NULL,
  `paciente` int(11) NOT NULL COMMENT 'ID do paciente (similar a cliente na farmácia)',
  `empresa_id` int(11) DEFAULT NULL COMMENT 'ID da empresa/seguro',
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_paciente` (`paciente`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_serie` (`serie`),
  KEY `idx_n_doc` (`n_doc`),
  CONSTRAINT `fk_factura_recepcao_paciente` FOREIGN KEY (`paciente`) REFERENCES `pacientes`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_factura_recepcao_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela de serviços das faturas (similar a fa_artigos_fact da farmácia)
CREATE TABLE IF NOT EXISTS `fa_servicos_fact_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL COMMENT 'ID do serviço (similar a artigo na farmácia)',
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `factura` int(11) NOT NULL COMMENT 'ID da fatura (factura_recepcao.id)',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_servico` (`servico`),
  KEY `idx_factura` (`factura`),
  KEY `idx_user` (`user`),
  CONSTRAINT `fk_fa_servicos_fact_servico` FOREIGN KEY (`servico`) REFERENCES `servicos_clinica`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_fa_servicos_fact_factura` FOREIGN KEY (`factura`) REFERENCES `factura_recepcao`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- NOVAS TABELAS
-- =====================================================

-- Tabela: empresas_seguros
CREATE TABLE IF NOT EXISTS `empresas_seguros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `nuit` varchar(50) DEFAULT NULL,
  `contacto` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `tabela_precos_id` int(11) DEFAULT NULL,
  `contrato` varchar(255) DEFAULT NULL,
  `data_inicio_contrato` date DEFAULT NULL,
  `data_fim_contrato` date DEFAULT NULL,
  `desconto_geral` decimal(5,2) DEFAULT 0.00 COMMENT 'Desconto percentual geral',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_criacao` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_nome` (`nome`),
  KEY `idx_nuit` (`nuit`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela: tabelas_precos
CREATE TABLE IF NOT EXISTS `tabelas_precos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `validade_inicio` date DEFAULT NULL,
  `validade_fim` date DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_criacao` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_ativo` (`ativo`),
  CONSTRAINT `fk_tabela_precos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela: tabela_precos_servicos
CREATE TABLE IF NOT EXISTS `tabela_precos_servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tabela_precos_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `desconto_percentual` decimal(5,2) DEFAULT 0.00,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tabela_servico` (`tabela_precos_id`, `servico_id`),
  KEY `idx_servico` (`servico_id`),
  CONSTRAINT `fk_tabela_precos_servicos_tabela` FOREIGN KEY (`tabela_precos_id`) REFERENCES `tabelas_precos`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tabela_precos_servicos_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos_clinica`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela: paciente_empresa_historico
CREATE TABLE IF NOT EXISTS `paciente_empresa_historico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `usuario_registo` int(11) DEFAULT NULL,
  `data_registo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_data` (`data_inicio`),
  CONSTRAINT `fk_hist_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- ATUALIZAÇÕES EM TABELAS EXISTENTES
-- =====================================================

-- Adicionar coluna empresa_id na tabela pacientes (se não existir)
SET @dbname = DATABASE();
SET @tablename = "pacientes";
SET @columnname = "empresa_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " int(11) DEFAULT NULL COMMENT 'Empresa/Seguro associado' AFTER provincia")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adicionar índice para empresa_id na tabela pacientes (se não existir)
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = 'idx_empresa')
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD KEY idx_empresa (", @columnname, ")")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adicionar foreign key para empresa_id na tabela pacientes (se não existir)
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (constraint_name = 'fk_pacientes_empresa')
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD CONSTRAINT fk_pacientes_empresa FOREIGN KEY (", @columnname, ") REFERENCES empresas_seguros(id) ON DELETE SET NULL ON UPDATE CASCADE")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Atualizar enum de status na tabela faturas_atendimento para incluir 'parcial' e 'vencido'
SET @tablename = "faturas_atendimento";
SET @columnname = "status";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
      AND (column_type LIKE '%parcial%')
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " MODIFY COLUMN ", @columnname, " enum('pendente','parcial','paga','vencido','cancelada') NOT NULL DEFAULT 'pendente'")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adicionar coluna valor_pago na tabela faturas_atendimento (se não existir)
SET @columnname = "valor_pago";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total já pago (para pagamentos parciais)' AFTER total")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adicionar coluna empresa_id na tabela faturas_atendimento (se não existir)
SET @columnname = "empresa_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " int(11) DEFAULT NULL COMMENT 'Empresa para cobrança posterior' AFTER paciente_id")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adicionar índice para empresa_id na tabela faturas_atendimento (se não existir)
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = 'idx_empresa')
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD KEY idx_empresa (", @columnname, ")")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adicionar foreign key para empresa_id na tabela faturas_atendimento (se não existir)
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (constraint_name = 'fk_faturas_empresa')
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD CONSTRAINT fk_faturas_empresa FOREIGN KEY (", @columnname, ") REFERENCES empresas_seguros(id) ON DELETE SET NULL ON UPDATE CASCADE")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adicionar coluna tipo_documento na tabela faturas_atendimento (se não existir)
SET @columnname = "tipo_documento";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " enum('fatura','vds','cotacao') NOT NULL DEFAULT 'fatura' AFTER empresa_id")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adicionar índice para tipo_documento na tabela faturas_atendimento (se não existir)
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = 'idx_tipo')
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD KEY idx_tipo (", @columnname, ")")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- DADOS INICIAIS (apenas se não existirem)
-- =====================================================

-- Inserir serviços padrão (apenas se a tabela servicos_clinica existir e estiver vazia)
INSERT IGNORE INTO `servicos_clinica` (`codigo`, `nome`, `descricao`, `preco`, `categoria`) VALUES
('CONS-GERAL', 'Consulta Geral', 'Consulta médica geral', 1200.00, 'Consulta'),
('CONS-ESPEC', 'Consulta Especializada', 'Consulta com especialista', 2000.00, 'Consulta'),
('EXAME-SANGUE', 'Exame de Sangue', 'Análise de sangue completa', 1500.00, 'Exame'),
('EXAME-URINA', 'Exame de Urina', 'Análise de urina', 800.00, 'Exame'),
('PROC-CURATIVO', 'Curativo', 'Aplicação de curativo', 500.00, 'Procedimento');

-- =====================================================
-- TABELA TEMPORÁRIA PARA SERVIÇOS DE FATURAS (RECEPÇÃO)
-- =====================================================
CREATE TABLE IF NOT EXISTS `fa_servicos_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL COMMENT 'ID do serviço da tabela servicos_clinica',
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL COMMENT 'Preço unitário do serviço',
  `total` decimal(10,2) NOT NULL COMMENT 'Preço total (preco * qtd)',
  `user` int(11) NOT NULL COMMENT 'ID do usuário que está criando a fatura',
  `empresa_id` int(11) DEFAULT NULL COMMENT 'ID da empresa selecionada',
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user`),
  KEY `idx_servico` (`servico`),
  KEY `idx_empresa` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- ATUALIZAÇÃO: Adicionar suporte a factura_recepcao em pagamentos_recepcao
-- =====================================================

-- Verificar se a coluna já existe antes de adicionar
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'pagamentos_recepcao' 
AND COLUMN_NAME = 'factura_recepcao_id';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `pagamentos_recepcao` 
     ADD COLUMN `factura_recepcao_id` int(11) DEFAULT NULL COMMENT ''ID da fatura do novo sistema (factura_recepcao)'' AFTER `fatura_id`,
     ADD KEY `idx_factura_recepcao` (`factura_recepcao_id`)',
    'SELECT ''Coluna factura_recepcao_id já existe'' AS message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- FIM DAS ATUALIZAÇÕES
-- =====================================================
-- 
-- RESUMO DAS ATUALIZAÇÕES:
-- 
-- NOVAS TABELAS:
-- 1. empresas_seguros - Cadastro de empresas/seguros
-- 2. tabelas_precos - Tabelas de preços por empresa
-- 3. tabela_precos_servicos - Preços de serviços por tabela
-- 4. paciente_empresa_historico - Histórico de associações paciente-empresa
-- 
-- COLUNAS ADICIONADAS:
-- 1. pacientes.empresa_id - Associação com empresa/seguro
-- 2. faturas_atendimento.empresa_id - Empresa para cobrança
-- 3. faturas_atendimento.valor_pago - Suporte a pagamentos parciais
-- 4. faturas_atendimento.tipo_documento - Tipo de documento (fatura, vds, cotacao)
-- 
-- ALTERAÇÕES:
-- 1. faturas_atendimento.status - Adicionados valores 'parcial' e 'vencido'
-- 
-- =====================================================

