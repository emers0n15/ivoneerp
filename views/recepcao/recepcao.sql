-- =====================================================
-- SISTEMA DE GESTÃO DA CLÍNICA - MÓDULO DE RECEPÇÃO
-- Arquivo: recepcao.sql
-- Descrição: Estrutura completa do banco de dados para o módulo de recepção
-- Data de Criação: 2025-01-XX
-- Última Atualização: 2025-01-XX
-- Versão: 1.0
-- =====================================================
-- 
-- INSTRUÇÕES DE INSTALAÇÃO:
-- 1. Abra o phpMyAdmin
-- 2. Selecione o banco de dados "ivoneerp"
-- 3. Vá na aba "SQL"
-- 4. Cole todo este conteúdo e execute
-- 5. Verifique se todas as 12 tabelas foram criadas
-- 
-- IMPORTANTE: 
-- - Este arquivo contém a estrutura COMPLETA do banco de dados
-- - Para atualizações em instalações existentes, use: recepcao_atualizacoes.sql
-- - Este arquivo deve ser atualizado sempre que houver mudanças na estrutura
-- =====================================================

-- Selecionar o banco de dados
USE `ivoneerp`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- =====================================================
-- TABELA: empresas_seguros
-- Descrição: Empresas/Seguros com planos corporativos
-- =====================================================
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

-- =====================================================
-- TABELA: tabelas_precos
-- Descrição: Tabelas de preços por empresa
-- =====================================================
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

-- =====================================================
-- TABELA: tabela_precos_servicos
-- Descrição: Preços de serviços por tabela de preços
-- =====================================================
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

-- =====================================================
-- TABELA: pacientes
-- Descrição: Armazena dados dos pacientes da clínica
-- =====================================================
CREATE TABLE IF NOT EXISTS `pacientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_processo` varchar(50) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `apelido` varchar(255) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `sexo` enum('M','F','Outro') DEFAULT NULL,
  `documento_tipo` varchar(50) DEFAULT NULL,
  `documento_numero` varchar(100) DEFAULT NULL,
  `contacto` varchar(20) DEFAULT NULL,
  `contacto_alternativo` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `cidade` varchar(255) DEFAULT NULL,
  `provincia` varchar(255) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL COMMENT 'Empresa/Seguro associado',
  `observacoes` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_registo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_registo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_processo` (`numero_processo`),
  KEY `idx_nome` (`nome`, `apelido`),
  KEY `idx_documento` (`documento_numero`),
  KEY `idx_contacto` (`contacto`),
  KEY `idx_empresa` (`empresa_id`),
  CONSTRAINT `fk_pacientes_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABELA: paciente_empresa_historico
-- Descrição: Histórico de associações paciente-empresa
-- =====================================================
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
-- TABELA: servicos_clinica
-- Descrição: Tipos de serviços oferecidos pela clínica
-- =====================================================
CREATE TABLE IF NOT EXISTS `servicos_clinica` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `categoria` varchar(100) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_criacao` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `idx_categoria` (`categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- DADOS INICIAIS: servicos_clinica
-- Serviços padrão do sistema
-- =====================================================
INSERT IGNORE INTO `servicos_clinica` (`codigo`, `nome`, `descricao`, `preco`, `categoria`) VALUES
('CONS-GERAL', 'Consulta Geral', 'Consulta médica geral', 1200.00, 'Consulta'),
('CONS-ESPEC', 'Consulta Especializada', 'Consulta com especialista', 2000.00, 'Consulta'),
('EXAME-SANGUE', 'Exame de Sangue', 'Análise de sangue completa', 1500.00, 'Exame'),
('EXAME-URINA', 'Exame de Urina', 'Análise de urina', 800.00, 'Exame'),
('PROC-CURATIVO', 'Curativo', 'Aplicação de curativo', 500.00, 'Procedimento');

-- =====================================================
-- TABELA: faturas_atendimento
-- Descrição: Faturas geradas para atendimentos
-- =====================================================
CREATE TABLE IF NOT EXISTS `faturas_atendimento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_fatura` varchar(50) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL COMMENT 'Empresa para cobrança posterior',
  `tipo_documento` enum('fatura','vds','cotacao') NOT NULL DEFAULT 'fatura',
  `data_atendimento` date NOT NULL,
  `hora_atendimento` time DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL COMMENT 'Para faturas corporativas',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `desconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_pago` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total já pago (para pagamentos parciais)',
  `status` enum('pendente','parcial','paga','vencido','cancelada') NOT NULL DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `usuario_criacao` int(11) NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_cancelamento` int(11) DEFAULT NULL,
  `data_cancelamento` timestamp NULL DEFAULT NULL,
  `motivo_cancelamento` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_fatura` (`numero_fatura`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_empresa` (`empresa_id`),
  KEY `idx_status` (`status`),
  KEY `idx_data` (`data_atendimento`),
  KEY `idx_tipo` (`tipo_documento`),
  CONSTRAINT `fk_faturas_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_faturas_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas_seguros`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABELA: fatura_servicos
-- Descrição: Serviços incluídos em cada fatura
-- =====================================================
CREATE TABLE IF NOT EXISTS `fatura_servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fatura_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fatura` (`fatura_id`),
  KEY `idx_servico` (`servico_id`),
  CONSTRAINT `fk_fatura_servicos_fatura` FOREIGN KEY (`fatura_id`) REFERENCES `faturas_atendimento`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_fatura_servicos_servico` FOREIGN KEY (`servico_id`) REFERENCES `servicos_clinica`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABELA: pagamentos_recepcao
-- Descrição: Registro de pagamentos das faturas (suporta múltiplos pagamentos)
-- =====================================================
CREATE TABLE IF NOT EXISTS `pagamentos_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fatura_id` int(11) NOT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `metodo_pagamento` enum('dinheiro','m-pesa','emola','pos','transferencia','fatura_empresa') NOT NULL,
  `referencia_pagamento` varchar(255) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `usuario` int(11) NOT NULL,
  `data_pagamento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_fatura` (`fatura_id`),
  KEY `idx_data` (`data_pagamento`),
  KEY `idx_metodo` (`metodo_pagamento`),
  CONSTRAINT `fk_pagamentos_fatura` FOREIGN KEY (`fatura_id`) REFERENCES `faturas_atendimento`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABELA: auditoria_recepcao
-- Descrição: Logs de auditoria de ações críticas
-- =====================================================
CREATE TABLE IF NOT EXISTS `auditoria_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `acao` varchar(100) NOT NULL,
  `tabela` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `dados_anteriores` text DEFAULT NULL,
  `dados_novos` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `data_acao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`usuario_id`),
  KEY `idx_acao` (`acao`),
  KEY `idx_data` (`data_acao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABELA: historico_atendimentos
-- Descrição: Histórico de todos os atendimentos realizados
-- =====================================================
CREATE TABLE IF NOT EXISTS `historico_atendimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(11) NOT NULL,
  `fatura_id` int(11) DEFAULT NULL,
  `tipo_atendimento` varchar(100) DEFAULT NULL,
  `servicos_realizados` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `data_atendimento` date NOT NULL,
  `usuario_registo` int(11) DEFAULT NULL,
  `data_registo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_paciente` (`paciente_id`),
  KEY `idx_fatura` (`fatura_id`),
  KEY `idx_data` (`data_atendimento`),
  CONSTRAINT `fk_historico_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_historico_fatura` FOREIGN KEY (`fatura_id`) REFERENCES `faturas_atendimento`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- TABELA: caixa_recepcao
-- Descrição: Controle diário de caixa da recepção
-- =====================================================
CREATE TABLE IF NOT EXISTS `caixa_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `valor_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_entradas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_dinheiro` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_mpesa` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_emola` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_pos` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_saidas` decimal(10,2) NOT NULL DEFAULT 0.00,
  `saldo_final` decimal(10,2) NOT NULL DEFAULT 0.00,
  `observacoes` text DEFAULT NULL,
  `usuario_abertura` int(11) DEFAULT NULL,
  `usuario_fechamento` int(11) DEFAULT NULL,
  `data_abertura` timestamp NULL DEFAULT NULL,
  `data_fechamento` timestamp NULL DEFAULT NULL,
  `status` enum('aberto','fechado') NOT NULL DEFAULT 'aberto',
  PRIMARY KEY (`id`),
  UNIQUE KEY `data` (`data`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- FIM DA INSTALAÇÃO
-- =====================================================
-- 
-- =====================================================
-- LISTA DE TABELAS CRIADAS (12 tabelas)
-- =====================================================
-- 1. empresas_seguros - Cadastro de empresas/seguros
-- 2. tabelas_precos - Tabelas de preços por empresa
-- 3. tabela_precos_servicos - Preços de serviços por tabela
-- 4. pacientes - Dados dos pacientes da clínica
-- 5. paciente_empresa_historico - Histórico de associações paciente-empresa
-- 6. servicos_clinica - Serviços oferecidos pela clínica
-- 7. faturas_atendimento - Faturas geradas para atendimentos
-- 8. fatura_servicos - Serviços incluídos em cada fatura
-- 9. pagamentos_recepcao - Registro de pagamentos das faturas
-- 10. historico_atendimentos - Histórico de todos os atendimentos
-- 11. caixa_recepcao - Controle diário de caixa da recepção
-- 12. auditoria_recepcao - Logs de auditoria de ações críticas
-- 
-- =====================================================
-- ESTRUTURA DAS TABELAS PRINCIPAIS
-- =====================================================
-- 
-- pacientes:
--   - empresa_id (FK -> empresas_seguros.id) - Associação com empresa/seguro
--   - numero_processo (UNIQUE) - Número único do processo
--   - status: pendente, parcial, paga, vencido, cancelada
-- 
-- faturas_atendimento:
--   - empresa_id (FK -> empresas_seguros.id) - Empresa para cobrança
--   - valor_pago - Total já pago (suporte a pagamentos parciais)
--   - status: pendente, parcial, paga, vencido, cancelada
-- 
-- empresas_seguros:
--   - tabela_precos_id - Referência à tabela de preços da empresa
--   - desconto_geral - Desconto percentual geral
-- 
-- =====================================================
-- ARQUIVOS RELACIONADOS
-- =====================================================
-- - recepcao.sql (este arquivo) - Instalação completa
-- - recepcao_atualizacoes.sql - Apenas atualizações para instalações existentes
-- - verificar_tabelas.php - Script para verificar se todas as tabelas foram criadas
-- 
-- IMPORTANTE: Sempre atualize este arquivo quando houver
-- mudanças na estrutura do banco de dados.
-- =====================================================
