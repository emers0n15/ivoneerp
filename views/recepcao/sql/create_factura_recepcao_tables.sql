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

