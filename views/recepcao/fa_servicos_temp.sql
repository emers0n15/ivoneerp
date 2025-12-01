-- =====================================================
-- TABELA TEMPORÁRIA PARA SERVIÇOS DE FATURAS (RECEPÇÃO)
-- =====================================================
-- Esta tabela armazena temporariamente os serviços
-- selecionados antes de criar a fatura final
-- =====================================================

USE `ivoneerp`;

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

