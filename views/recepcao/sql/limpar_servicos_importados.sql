-- RESET COMPLETO DE SERVIÇOS E TABELAS DE PREÇOS
--
-- Este script APAGA TODOS os serviços cadastrados e TODAS as tabelas de preços
-- (usados em seguradoras/empresas) para permitir uma reestruturação total.
--
-- PASSOS PARA USO (phpMyAdmin ou outro cliente MySQL):
-- 1. Faça BACKUP da base de dados antes de executar (muito importante)!
-- 2. Selecione a base de dados do sistema (por ex.: ivoneerp).
-- 3. Cole e execute TODO o conteúdo deste ficheiro.
--
-- OBS: Este script NÃO apaga categorias de serviços, nem documentos (faturas, VDS, etc.),
-- mas, se existir algum documento que ainda aponte para serviços apagados,
-- esses registos ficarão sem referência lógica.

SET FOREIGN_KEY_CHECKS = 0;

-- 1) Limpar preços específicos por serviço/tabela (tabela filha)
TRUNCATE TABLE tabela_precos_servicos;

-- 2) Limpar tabelas de preços por empresa (usar DELETE por causa da FK)
DELETE FROM tabelas_precos;

-- 3) Limpar ligações de serviços em documentos (faturas, VDS, DV, etc.)
--    As tabelas abaixo podem ou não existir, dependendo da versão do sistema.
--    Se alguma não existir, pode ignorar o erro dessa linha em particular.
DELETE FROM fa_servicos_fact_recepcao;
DELETE FROM vds_servicos_fact;
DELETE FROM dv_servicos_fact;
DELETE FROM fatura_servicos;

-- 4) Limpar todos os serviços cadastrados (base)
DELETE FROM servicos_clinica;

SET FOREIGN_KEY_CHECKS = 1;



