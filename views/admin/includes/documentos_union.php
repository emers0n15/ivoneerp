<?php
/**
 * Retorna o SQL base (UNION) com todos os documentos suportados.
 *
 * Cada SELECT deve possuir exatamente as mesmas colunas:
 * - tipo: identificador curto do documento
 * - registro_id: ID na tabela original
 * - documento: código legível (ex: FA#2025/10)
 * - cliente: nome do cliente/fornecedor associado
 * - total: valor numérico do documento
 * - status: estado textual padronizado
 * - data_emissao: data/hora em formato datetime
 * - utilizador: nome do utilizador responsável
 * - rota: ficheiro responsável por gerar o PDF
 * - parametro: nome do parâmetro GET esperado pelo PDF
 */
if (!function_exists('getDocumentosUnionSql')) {
    function getDocumentosUnionSql(): string
    {
        return <<<SQL
SELECT 'factura' AS tipo,
       f.id AS registro_id,
       CONCAT('FA#', f.serie, '/', f.n_doc) AS documento,
       COALESCE(CONCAT(cli.nome, ' ', cli.apelido), 'Cliente não identificado') AS cliente,
       f.valor AS total,
       CASE WHEN f.statuss = 1 THEN 'paga' ELSE 'pendente' END AS status,
       CONCAT(f.dataa, ' 00:00:00') AS data_emissao,
       COALESCE(u.nome, '-') AS utilizador,
       'fa_pdf.php' AS rota,
       'id_fatura' AS parametro
FROM factura f
LEFT JOIN clientes cli ON cli.id = f.cliente
LEFT JOIN users u ON u.id = f.usuario

UNION ALL

SELECT 'nota_credito' AS tipo,
       nc.id AS registro_id,
       CONCAT('NC#', nc.serie, '/', nc.n_doc) AS documento,
       COALESCE(CONCAT(cli.nome, ' ', cli.apelido), 'Cliente não identificado') AS cliente,
       CAST(nc.valor AS DECIMAL(10,2)) AS total,
       'emitido' AS status,
       DATE_FORMAT(nc.data, '%Y-%m-%d %H:%i:%s') AS data_emissao,
       COALESCE(u.nome, '-') AS utilizador,
       'nc_pdf.php' AS rota,
       'id_nc' AS parametro
FROM nota_de_credito nc
LEFT JOIN clientes cli ON cli.id = nc.cliente
LEFT JOIN users u ON u.id = nc.user

UNION ALL

SELECT 'nota_debito' AS tipo,
       nd.id AS registro_id,
       CONCAT('ND#', nd.serie, '/', nd.n_doc) AS documento,
       COALESCE(CONCAT(cli.nome, ' ', cli.apelido), 'Cliente não identificado') AS cliente,
       CAST(nd.valor AS DECIMAL(10,2)) AS total,
       'emitido' AS status,
       DATE_FORMAT(nd.data, '%Y-%m-%d %H:%i:%s') AS data_emissao,
       COALESCE(u.nome, '-') AS utilizador,
       'nd_pdf.php' AS rota,
       'id_nd' AS parametro
FROM nota_debito nd
LEFT JOIN clientes cli ON cli.id = nd.cliente
LEFT JOIN users u ON u.id = nd.usuario

UNION ALL

SELECT 'cotacao' AS tipo,
       ct.id AS registro_id,
       CONCAT('CT#', ct.serie, '/', ct.n_doc) AS documento,
       COALESCE(CONCAT(cli.nome, ' ', cli.apelido), 'Cliente não identificado') AS cliente,
       ct.valor AS total,
       'aberta' AS status,
       DATE_FORMAT(ct.data, '%Y-%m-%d %H:%i:%s') AS data_emissao,
       COALESCE(u.nome, '-') AS utilizador,
       'ct_pdf.php' AS rota,
       'id_ct' AS parametro
FROM cotacao ct
LEFT JOIN clientes cli ON cli.id = ct.cliente
LEFT JOIN users u ON u.id = ct.usuario

UNION ALL

SELECT 'recibo' AS tipo,
       rc.id AS registro_id,
       CONCAT('RC#', rc.serie, '/', rc.n_doc) AS documento,
       COALESCE(CONCAT(cli.nome, ' ', cli.apelido), 'Cliente não identificado') AS cliente,
       rc.valor AS total,
       'emitido' AS status,
       DATE_FORMAT(rc.data, '%Y-%m-%d %H:%i:%s') AS data_emissao,
       COALESCE(u.nome, '-') AS utilizador,
       'rc_pdf.php' AS rota,
       'id_rc' AS parametro
FROM recibo rc
LEFT JOIN clientes cli ON cli.id = rc.cliente
LEFT JOIN users u ON u.id = rc.user

UNION ALL

SELECT 'venda_dinheiro' AS tipo,
       p.idpedido AS registro_id,
       CONCAT('VD#', p.serie, '/', p.n_doc) AS documento,
       COALESCE(CONCAT(cli.nome, ' ', cli.apelido), 'Cliente não identificado') AS cliente,
       p.pagamentopedido AS total,
       CASE WHEN p.devolucao > 0 THEN 'devolvido' ELSE 'emitido' END AS status,
       DATE_FORMAT(p.data, '%Y-%m-%d %H:%i:%s') AS data_emissao,
       COALESCE(u.nome, '-') AS utilizador,
       'vd_pdf.php' AS rota,
       'id_vd' AS parametro
FROM pedido p
LEFT JOIN clientes cli ON cli.id = p.clientepedido
LEFT JOIN users u ON u.id = p.userpedido

UNION ALL

SELECT 'devolucao' AS tipo,
       dv.id AS registro_id,
       CONCAT('DV#', dv.serie, '/', dv.n_doc) AS documento,
       COALESCE(CONCAT(cli.nome, ' ', cli.apelido), 'Cliente não identificado') AS cliente,
       dv.valor AS total,
       'emitido' AS status,
       DATE_FORMAT(dv.data, '%Y-%m-%d %H:%i:%s') AS data_emissao,
       COALESCE(u.nome, '-') AS utilizador,
       'dv_pdf.php' AS rota,
       'id_dv' AS parametro
FROM devolucao dv
LEFT JOIN clientes cli ON cli.id = dv.idcliente
LEFT JOIN users u ON u.id = dv.iduser

UNION ALL

SELECT 'ordem_compra' AS tipo,
       oc.id AS registro_id,
       CONCAT('OC#', oc.serie, '/', oc.n_doc) AS documento,
       COALESCE(forn.nome, 'Fornecedor não identificado') AS cliente,
       oc.valor AS total,
       'emitido' AS status,
       DATE_FORMAT(oc.data, '%Y-%m-%d %H:%i:%s') AS data_emissao,
       COALESCE(u.nome, '-') AS utilizador,
       'oc_pdf.php' AS rota,
       'id_oc' AS parametro
FROM ordem_compra oc
LEFT JOIN fornecedores forn ON forn.id = oc.fornecedor
LEFT JOIN users u ON u.id = oc.user
SQL;
    }
}

