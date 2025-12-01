-- Tabelas temporárias para documentos da recepção

-- Tabela temporária para VDS (Venda a Dinheiro/Serviço)
CREATE TABLE IF NOT EXISTS `vds_servicos_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `user` (`user`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela temporária para CT (Cotação)
CREATE TABLE IF NOT EXISTS `ct_servicos_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `user` (`user`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela temporária para NC (Nota de Crédito)
CREATE TABLE IF NOT EXISTS `nc_servicos_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `user` (`user`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela temporária para ND (Nota de Débito)
CREATE TABLE IF NOT EXISTS `nd_servicos_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `user` (`user`),
  KEY `empresa_id` (`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabelas permanentes para documentos da recepção

-- Tabela para VDS (Venda a Dinheiro/Serviço)
CREATE TABLE IF NOT EXISTS `venda_dinheiro_servico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_doc` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `metodo` varchar(50) NOT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paciente` (`paciente`),
  KEY `empresa_id` (`empresa_id`),
  KEY `usuario` (`usuario`),
  KEY `serie` (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de serviços para VDS
CREATE TABLE IF NOT EXISTS `vds_servicos_fact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `vds_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `vds_id` (`vds_id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para CT (Cotação)
CREATE TABLE IF NOT EXISTS `cotacao_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_doc` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `prazo` date DEFAULT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paciente` (`paciente`),
  KEY `empresa_id` (`empresa_id`),
  KEY `usuario` (`usuario`),
  KEY `serie` (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de serviços para CT
CREATE TABLE IF NOT EXISTS `ct_servicos_fact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `cotacao_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `cotacao_id` (`cotacao_id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para NC (Nota de Crédito)
CREATE TABLE IF NOT EXISTS `nota_credito_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_doc` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `factura_recepcao_id` (`factura_recepcao_id`),
  KEY `paciente` (`paciente`),
  KEY `empresa_id` (`empresa_id`),
  KEY `usuario` (`usuario`),
  KEY `serie` (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de serviços para NC
CREATE TABLE IF NOT EXISTS `nc_servicos_fact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `nota_credito_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `nota_credito_id` (`nota_credito_id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para ND (Nota de Débito)
CREATE TABLE IF NOT EXISTS `nota_debito_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_doc` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `factura_recepcao_id` (`factura_recepcao_id`),
  KEY `paciente` (`paciente`),
  KEY `empresa_id` (`empresa_id`),
  KEY `usuario` (`usuario`),
  KEY `serie` (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de serviços para ND
CREATE TABLE IF NOT EXISTS `nd_servicos_fact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `nota_debito_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `nota_debito_id` (`nota_debito_id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para RC (Recibo)
CREATE TABLE IF NOT EXISTS `recibo_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_doc` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `metodo` varchar(50) NOT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `paciente` (`paciente`),
  KEY `empresa_id` (`empresa_id`),
  KEY `usuario` (`usuario`),
  KEY `serie` (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela temporária para RC (Recibo)
CREATE TABLE IF NOT EXISTS `rc_faturas_temp_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `factura_recepcao_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_recepcao_id` (`factura_recepcao_id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de ligação entre Recibos e Faturas
CREATE TABLE IF NOT EXISTS `recibo_factura_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recibo_id` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `recibo_id` (`recibo_id`),
  KEY `factura_recepcao_id` (`factura_recepcao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela temporária para DV (Devolução)
CREATE TABLE IF NOT EXISTS `dv_servicos_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL DEFAULT 1,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `factura_recepcao_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `user` (`user`),
  KEY `empresa_id` (`empresa_id`),
  KEY `factura_recepcao_id` (`factura_recepcao_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela para DV (Devolução)
CREATE TABLE IF NOT EXISTS `devolucao_recepcao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `n_doc` int(11) NOT NULL,
  `factura_recepcao_id` int(11) NOT NULL,
  `paciente` int(11) NOT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `motivo` text DEFAULT NULL,
  `metodo` varchar(50) NOT NULL,
  `serie` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `dataa` date NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `factura_recepcao_id` (`factura_recepcao_id`),
  KEY `paciente` (`paciente`),
  KEY `empresa_id` (`empresa_id`),
  KEY `usuario` (`usuario`),
  KEY `serie` (`serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de serviços para DV
CREATE TABLE IF NOT EXISTS `dv_servicos_fact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `servico` int(11) NOT NULL,
  `qtd` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `user` int(11) NOT NULL,
  `devolucao_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `servico` (`servico`),
  KEY `devolucao_id` (`devolucao_id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

