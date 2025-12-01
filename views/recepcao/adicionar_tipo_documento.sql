-- =====================================================
-- ADICIONAR COLUNA tipo_documento
-- Execute este script no phpMyAdmin se a coluna não existir
-- =====================================================

USE `ivoneerp`;

-- Adicionar coluna tipo_documento na tabela faturas_atendimento
ALTER TABLE `faturas_atendimento` 
ADD COLUMN `tipo_documento` enum('fatura','vds','cotacao') NOT NULL DEFAULT 'fatura' AFTER `empresa_id`;

-- Adicionar índice para tipo_documento
ALTER TABLE `faturas_atendimento` 
ADD KEY `idx_tipo` (`tipo_documento`);

