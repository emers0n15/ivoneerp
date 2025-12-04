-- =====================================================
-- TABELA: categorias_servicos
-- Descrição: Categorias de serviços/procedimentos (nada hardcoded!)
-- =====================================================
CREATE TABLE IF NOT EXISTS `categorias_servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_criacao` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`),
  KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- DADOS INICIAIS: categorias_servicos
-- Categorias padrão (pode ser editado/deletado depois)
-- =====================================================
INSERT IGNORE INTO `categorias_servicos` (`nome`, `descricao`, `ativo`, `usuario_criacao`) VALUES
('Consulta', 'Consultas médicas gerais e especializadas', 1, NULL),
('Exame', 'Exames laboratoriais e de diagnóstico', 1, NULL),
('Procedimento', 'Procedimentos médicos diversos', 1, NULL),
('Internamento', 'Serviços de internamento', 1, NULL),
('Cirurgia', 'Procedimentos cirúrgicos', 1, NULL),
('Tratamento', 'Tratamentos médicos', 1, NULL),
('Outros', 'Outros serviços', 1, NULL);

-- =====================================================
-- ATUALIZAÇÃO: Modificar servicos_clinica para referenciar categorias
-- =====================================================
-- Adicionar campo categoria_id se ainda não existir
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'servicos_clinica' 
AND COLUMN_NAME = 'categoria_id';

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `servicos_clinica` 
     ADD COLUMN `categoria_id` int(11) DEFAULT NULL AFTER `categoria`,
     ADD KEY `idx_categoria_id` (`categoria_id`),
     ADD CONSTRAINT `fk_servicos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_servicos`(`id`) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "Coluna categoria_id já existe" AS mensagem');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Migrar dados existentes (se houver categoria como texto, associar ao categoria_id)
-- Nota: Isso mantém compatibilidade com dados existentes


