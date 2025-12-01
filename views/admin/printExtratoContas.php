<?php
session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("location:../");
    exit; // Adicionar exit após o redirecionamento
}
include_once '../../conexao/index.php';

// Validar e Sanitizar entradas GET
$serie = isset($_GET['serie']) ? mysqli_real_escape_string($db, $_GET['serie']) : '';
$data1_raw = isset($_GET['data1']) ? $_GET['data1'] : '';
$data2_raw = isset($_GET['data2']) ? $_GET['data2'] : '';

// É uma boa prática validar o formato das datas (ex: YYYY-MM-DD)
// Para este exemplo, assumimos que o formato é correto.
if (empty($data1_raw) || empty($data2_raw) || empty($serie)) {
    die("Erro: Parâmetros 'serie', 'data1' ou 'data2' não fornecidos ou inválidos.");
}

$data1 = mysqli_real_escape_string($db, $data1_raw) . " 00:00:00";
$data2 = mysqli_real_escape_string($db, $data2_raw) . " 23:59:00";

// Incluir template unificado do TCPDF
require_once 'includes/tcpdf_template.php';

// Dados da Empresa para cabeçalho/rodapé
$sql_empresa = "SELECT nome, endereco, nuit, contacto, email, pais, provincia, img FROM empresa LIMIT 1";
$rs_empresa = mysqli_query($db, $sql_empresa);
if (!$rs_empresa) {
    die("Erro ao buscar dados da empresa: " . mysqli_error($db));
}
$dados_empresa = mysqli_fetch_assoc($rs_empresa);

// Criar documento com cabeçalho/rodapé unificados
$titulo = 'EXTRATO DE CONTAS - ' . date("d/m/Y", strtotime($data1_raw)) . ' a ' . date("d/m/Y", strtotime($data2_raw));
$pdf = new ThemedTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, $dados_empresa, $titulo);
$pdf->SetCreator('IvoneERP');
$pdf->SetTitle('Extrato de Contas');
$pdf->AddPage();

// Cabeçalho e título são geridos pelo template

// -----------------------------------------------------------------------------
// INICIO - TRATANDO COM O RELATORIO DE FATURAS (FA)
// -----------------------------------------------------------------------------
$pdf->SetFont('helvetica', 'B', 11); // Fonte um pouco maior para o título da seção
$pdf->Cell(0, 10, 'FA - Facturas', 0, 1);
$pdf->SetLineWidth(0.1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - $pdf->GetX(), $pdf->GetY()); // Linha completa
$pdf->Ln(2);

// Cabeçalho da Tabela de Faturas
$pdf->SetFont('helvetica', 'B', 8);
$header_y_pos = $pdf->GetY();
$pdf->Cell(45, 7, 'Descrição', 0, 0); // Ajustar largura da coluna Descrição
$pdf->Cell(25, 7, 'Valor', 0, 0, 'R'); // Alinhar à direita
$pdf->Cell(25, 7, 'IVA', 0, 0, 'R'); // Alinhar à direita
$pdf->Cell(35, 7, 'Data de Emissão', 0, 0, 'C'); // Centralizar
$pdf->Cell(35, 7, 'Data de Pagamento', 0, 0, 'C'); // Centralizar
$pdf->Ln(8);

$totalFA = 0;
$totalNC = 0;
$totalIVAA_faturas = 0; // IVA acumulado das Faturas

$pdf->SetFont('helvetica', '', 8);

// Consulta Otimizada para Faturas e Notas de Crédito associadas
$sql_faturas_detalhes = "
    SELECT
        c.id AS cliente_id,
        c.nome AS cliente_nome,
        c.apelido AS cliente_apelido,
        f.id AS fatura_id_db,
        f.serie AS fatura_serie,
        f.valor AS fatura_valor,
        f.iva AS fatura_iva,
        DATE_FORMAT(f.data, '%d/%m/%Y') AS fatura_data_emissao,
        DATE_FORMAT(f.prazo, '%d/%m/%Y') AS fatura_data_prazo,
        f.nota_credito AS fatura_nota_credito_id,
        nc.id AS nc_id_db,
        nc.serie AS nc_serie,
        nc.n_doc AS nc_n_doc,
        nc.valor AS nc_valor,
        DATE_FORMAT(nc.data, '%d/%m/%Y') AS nc_data
    FROM
        factura f
    JOIN
        clientes c ON f.cliente = c.id
    LEFT JOIN
        nota_de_credito nc ON f.nota_credito = nc.id
    WHERE
        f.data BETWEEN '$data1' AND '$data2'
        AND f.serie = '$serie'
    ORDER BY
        c.nome, c.apelido, f.data;
";

$rs_faturas_detalhes = mysqli_query($db, $sql_faturas_detalhes);
if (!$rs_faturas_detalhes) {
    // Em produção, logar o erro em vez de usar die()
    error_log("Erro na consulta de faturas: " . mysqli_error($db));
    $pdf->Cell(0, 10, "Erro ao processar dados das faturas.", 0, 1);
    // Continuar para outras seções ou parar, dependendo da criticidade
} else {
    $current_cliente_id_fa = null;
    while ($fatura_item = mysqli_fetch_assoc($rs_faturas_detalhes)) {
        if ($pdf->GetY() > ($pdf->GetPageHeight() - 30)) { // Verificar se precisa de nova página (margem inferior de 30mm)
            $pdf->AddPage();
            // Re-imprimir cabeçalho da tabela na nova página se necessário
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->setY($header_y_pos - 5); //Ajustar se necessário
            $pdf->Cell(45, 7, 'Descrição', 0, 0);
            $pdf->Cell(25, 7, 'Valor', 0, 0, 'R');
            $pdf->Cell(25, 7, 'IVA', 0, 0, 'R');
            $pdf->Cell(35, 7, 'Data de Emissão', 0, 0, 'C');
            $pdf->Cell(35, 7, 'Data de Pagamento', 0, 0, 'C');
            $pdf->Ln(8);
            $pdf->SetFont('helvetica', '', 8);
        }

        if ($current_cliente_id_fa !== $fatura_item['cliente_id']) {
            if ($current_cliente_id_fa !== null) {
                $pdf->Ln(1); // Pequeno espaço entre clientes
            }
            $pdf->SetFont('helvetica', 'B', 8);
            // Linha de separação para o cliente
            $pdf->Cell(190, 0.1, '', 'T', 1); // Linha acima do nome do cliente
            $pdf->Cell(35, 7, 'Entidade/Fornecedor:', 0, 0); // Ajustar largura
            $pdf->Cell(0, 7, $fatura_item['cliente_nome'] . " " . $fatura_item['cliente_apelido'], 0, 1);
            $current_cliente_id_fa = $fatura_item['cliente_id'];
            $pdf->SetFont('helvetica', '', 8);
        }

        // Fatura
        $pdf->Cell(45, 6, "FA#" . $fatura_item['fatura_serie'] . "/" . $fatura_item['fatura_id_db'], 0, 0);
        $pdf->Cell(25, 6, number_format($fatura_item['fatura_valor'], 2, ',', '.'), 0, 0, 'R');
        $pdf->Cell(25, 6, number_format($fatura_item['fatura_iva'], 2, ',', '.'), 0, 0, 'R');
        $pdf->Cell(35, 6, $fatura_item['fatura_data_emissao'], 0, 0, 'C');
        $pdf->Cell(35, 6, $fatura_item['fatura_data_prazo'], 0, 0, 'C');
        $pdf->Ln(6);

        $totalFA += $fatura_item['fatura_valor'];
        $totalIVAA_faturas += $fatura_item['fatura_iva'];

        // Nota de Crédito associada
        if ($fatura_item['nc_id_db'] !== null) {
            $pdf->Cell(10, 6, '', 0, 0); // Indentação para NC
            $pdf->Cell(35, 6, "NC#" . $fatura_item['nc_serie'] . "/" . $fatura_item['nc_n_doc'], 0, 0);
            $pdf->Cell(25, 6, number_format($fatura_item['nc_valor'], 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell(25, 6, number_format(0, 2, ',', '.'), 0, 0, 'R'); // IVA da NC (originalmente 0.00)
            $pdf->Cell(35, 6, '', 0, 0, 'C'); // Data de emissão da NC (originalmente vazia)
            $pdf->Cell(35, 6, $fatura_item['nc_data'], 0, 0, 'C'); // Data da NC
            $pdf->Ln(6);
            $totalNC += $fatura_item['nc_valor'];
        }
    }
}
$pdf->SetLineWidth(0.1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - $pdf->GetX(), $pdf->GetY());
$pdf->Ln(1);

// Totais Faturas
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 7, 'Totais (FA)', 0, 1, 'R');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(155, 7, 'Total FAs: ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalFA, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(155, 7, 'Total NCs: ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalNC, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(155, 7, 'Total IVA (Faturas): ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalIVAA_faturas, 2, ',', '.'), 0, 1, 'R');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(155, 7, 'Saldo (FAs - NCs): ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalFA - $totalNC, 2, ',', '.'), 0, 1, 'R');
$pdf->Ln(5);
// -----------------------------------------------------------------------------
// FIM - TRATANDO COM O RELATORIO DE FATURAS (FA)
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// INICIO - TRATANDO COM O RELATORIO DE VENDAS A DINHEIRO (VD)
// -----------------------------------------------------------------------------
$pdf->AddPage(); // Adicionar nova página para Vendas a Dinheiro
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 10, 'VD - Vendas a Dinheiro', 0, 1);
$pdf->SetLineWidth(0.1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - $pdf->GetX(), $pdf->GetY());
$pdf->Ln(2);

// Cabeçalho da Tabela de Vendas a Dinheiro
$pdf->SetFont('helvetica', 'B', 8);
$header_vd_y_pos = $pdf->GetY();
$pdf->Cell(40, 7, 'Descrição', 0, 0);
$pdf->Cell(25, 7, 'Valor', 0, 0, 'R');
$pdf->Cell(25, 7, 'IVA', 0, 0, 'R');
$pdf->Cell(40, 7, 'Método Pag.', 0, 0, 'C');
$pdf->Cell(30, 7, 'Data Emissão', 0, 0, 'C');
$pdf->Ln(8);

$totalVD = 0;
$totalDV = 0; // Devoluções de Vendas
$totalIVAVD_vendas = 0; // IVA acumulado das Vendas a Dinheiro

$pdf->SetFont('helvetica', '', 8);

/*
NOTA DE OTIMIZAÇÃO PARA VENDAS A DINHEIRO (VD):
A estrutura original tinha um loop para clientes, depois um loop para pedidos,
e dentro do loop de pedidos, um loop para entregas e outro para devoluções.
Para otimizar, você pode:
1. Buscar todos os pedidos com informações do cliente e método de pagamento.
2. Dentro do loop de cada pedido, fazer uma consulta separada para buscar suas entregas (itens).
3. E outra consulta para a devolução associada (se houver).

Consulta principal para Pedidos:
$sql_pedidos_detalhes = "
    SELECT
        c.id AS cliente_id,
        c.nome AS cliente_nome,
        c.apelido AS cliente_apelido,
        p.idpedido,
        p.serie AS pedido_serie,
        p.n_doc AS pedido_n_doc,
        p.pagamentopedido AS pedido_valor,
        p.iva AS pedido_iva,
        DATE_FORMAT(p.data, '%d/%m/%Y') AS pedido_data_emissao,
        mp.descricao AS metodo_pagamento_descricao,
        p.devolucao AS pedido_devolucao_id
    FROM
        pedido p
    JOIN
        clientes c ON p.clientepedido = c.id
    LEFT JOIN
        metodo_pagamento mp ON p.modo = mp.id
    WHERE
        p.data BETWEEN '$data1' AND '$data2'
        -- AND p.serie = '$serie_para_vd' -- Se houver filtro de série específico para VD
    ORDER BY
        c.nome, c.apelido, p.data;
";
// Loop através de $rs_pedidos_detalhes
// Dentro do loop:
//   $id_pedido_atual = $pedido_item['idpedido'];
//   Consulta para Entregas (itens do pedido):
//   $sql_entregas = "
//       SELECT
//           e.qtdentrega,
//           e.precoentrega,
//           e.iva AS entrega_iva_valor,
//           e.totalentrega,
//           pr.nomeproduto,
//           pr.iva AS produto_iva_percentagem
//       FROM
//           entrega e
//       JOIN
//           produto pr ON e.produtoentrega = pr.idproduto
//       WHERE
//           e.pedidoentrega = '$id_pedido_atual';
//   ";
//   // Loop para exibir entregas
//
//   $id_devolucao_atual = $pedido_item['pedido_devolucao_id'];
//   Consulta para Devolução (se $id_devolucao_atual não for nulo):
//   $sql_devolucao = "
//       SELECT
//           d.serie AS dev_serie,
//           d.n_doc AS dev_n_doc,
//           d.valor AS dev_valor,
//           DATE_FORMAT(d.data, '%d/%m/%Y') AS dev_data
//           -- ,mp_dev.descricao AS dev_metodo_pagamento -- Se devolução tem seu próprio método
//       FROM
//           devolucao d
//       -- LEFT JOIN metodo_pagamento mp_dev ON d.modo = mp_dev.id
//       WHERE
//           d.id = '$id_devolucao_atual';
//   ";
//   // Exibir dados da devolução
*/

// Implementação simplificada para manter o foco, mas a otimização acima é recomendada.
// O código original com N+1 queries será lento. A seguir é apenas um placeholder da estrutura.
$sql_clientes_vd = "SELECT DISTINCT(clientepedido) as cl_id FROM `pedido` WHERE data BETWEEN '$data1' AND '$data2'";
$rs_clientes_vd = mysqli_query($db, $sql_clientes_vd);
if (!$rs_clientes_vd) {
    error_log("Erro na consulta de clientes VD: " . mysqli_error($db));
} else {
    $current_cliente_id_vd = null;
    while ($cliente_vd_data = mysqli_fetch_assoc($rs_clientes_vd)) {
        $cl_id = $cliente_vd_data['cl_id'];
        $sql_cliente_info = "SELECT nome, apelido FROM `clientes` WHERE id = '$cl_id'";
        $rs_cliente_info = mysqli_query($db, $sql_cliente_info);
        $dados_cliente_info = mysqli_fetch_assoc($rs_cliente_info);

        if ($pdf->GetY() > ($pdf->GetPageHeight() - 60)) { // Margem maior para cabeçalho + itens
             $pdf->AddPage();
             // Re-imprimir cabeçalho da tabela
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->setY($header_vd_y_pos - 5);
            $pdf->Cell(40, 7, 'Descrição', 0, 0);
            $pdf->Cell(25, 7, 'Valor', 0, 0, 'R');
            $pdf->Cell(25, 7, 'IVA', 0, 0, 'R');
            $pdf->Cell(40, 7, 'Método Pag.', 0, 0, 'C');
            $pdf->Cell(30, 7, 'Data Emissão', 0, 0, 'C');
            $pdf->Ln(8);
            $pdf->SetFont('helvetica', '', 8);
        }

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(190, 0.1, '', 'T', 1);
        $pdf->Cell(35, 7, 'Entidade/Cliente:', 0, 0);
        $pdf->Cell(0, 7, $dados_cliente_info['nome'] . " " . $dados_cliente_info['apelido'], 0, 1);
        $pdf->SetFont('helvetica', '', 8);

        $sql_pedidos_cliente = "SELECT p.idpedido, p.serie, p.n_doc, p.pagamentopedido, p.iva AS pedido_iva, DATE_FORMAT(p.data, '%d/%m/%Y') AS data_emissao, mp.descricao AS modo_pagamento, p.devolucao 
                                FROM pedido p 
                                LEFT JOIN metodo_pagamento mp ON p.modo = mp.id
                                WHERE p.clientepedido = '$cl_id' AND p.data BETWEEN '$data1' AND '$data2' ORDER BY p.data";
        $rs_pedidos_cliente = mysqli_query($db, $sql_pedidos_cliente);
        while ($pedido_data = mysqli_fetch_assoc($rs_pedidos_cliente)) {
            $pdf->Cell(40, 6, "VD#" . $pedido_data['serie'] . "/" . $pedido_data['n_doc'], 0, 0);
            $pdf->Cell(25, 6, number_format($pedido_data['pagamentopedido'], 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell(25, 6, number_format($pedido_data['pedido_iva'], 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell(40, 6, $pedido_data['modo_pagamento'], 0, 0, 'C');
            $pdf->Cell(30, 6, $pedido_data['data_emissao'], 0, 0, 'C');
            $pdf->Ln(6);

            $totalVD += $pedido_data['pagamentopedido'];
            $totalIVAVD_vendas += $pedido_data['pedido_iva'];

            // Itens da Venda (Entregas) - AINDA PRECISA DE OTIMIZAÇÃO SIGNIFICATIVA
            // Esta parte é um grande gargalo se houver muitos itens.
            // Idealmente, buscar todos os itens de todos os pedidos de uma vez e agrupar em PHP.
            $id_pedido_atual = $pedido_data['idpedido'];
            $sql_entregas = "SELECT e.qtdentrega, e.precoentrega, e.iva AS entrega_iva_valor, e.totalentrega, pr.nomeproduto, pr.iva AS produto_iva_percentagem
                             FROM entrega e
                             JOIN produto pr ON e.produtoentrega = pr.idproduto
                             WHERE e.pedidoentrega = '$id_pedido_atual'";
            $rs_entregas = mysqli_query($db, $sql_entregas);
            if(mysqli_num_rows($rs_entregas) > 0) {
                $pdf->SetFont('helvetica', 'I', 7); // Fonte menor para itens
                 while ($entrega_item = mysqli_fetch_assoc($rs_entregas)) {
                    $pdf->Cell(10, 5, '', 0, 0); // Indent
                    $pdf->Cell(70, 5, $entrega_item['nomeproduto'] . " (Qtd: " . $entrega_item['qtdentrega'] . ", P.U: " . number_format($entrega_item['precoentrega'],2,',','.') . ")", 0,0);
                    $pdf->Cell(30, 5, "IVA: " . number_format($entrega_item['entrega_iva_valor'],2,',','.'), 0,0,'R');
                    $pdf->Cell(30, 5, "Total: " . number_format($entrega_item['totalentrega'],2,',','.'), 0,1,'R');
                 }
                $pdf->SetFont('helvetica', '', 8); // Restaurar fonte
            }


            // Devolução associada - AINDA PRECISA DE OTIMIZAÇÃO
            if (!empty($pedido_data['devolucao'])) {
                $id_devolucao = $pedido_data['devolucao'];
                $sql_devolucao_vd = "SELECT serie, n_doc, valor, DATE_FORMAT(data, '%d/%m/%Y') AS data_devolucao FROM devolucao WHERE id = '$id_devolucao'";
                $rs_devolucao_vd = mysqli_query($db, $sql_devolucao_vd);
                if ($devolucao_vd_data = mysqli_fetch_assoc($rs_devolucao_vd)) {
                    $pdf->Cell(10, 6, '', 0, 0); // Indentação para DV
                    $pdf->Cell(30, 6, "DV#" . $devolucao_vd_data['serie'] . "/" . $devolucao_vd_data['n_doc'], 0, 0);
                    $pdf->Cell(25, 6, number_format($devolucao_vd_data['valor'], 2, ',', '.'), 0, 0, 'R');
                    $pdf->Cell(25, 6, number_format(0, 2, ',', '.'), 0, 0, 'R'); // IVA da DV
                    $pdf->Cell(40, 6, '', 0, 0, 'C'); // Método Pag. da DV
                    $pdf->Cell(30, 6, $devolucao_vd_data['data_devolucao'], 0, 0, 'C');
                    $pdf->Ln(6);
                    $totalDV += $devolucao_vd_data['valor'];
                }
            }
        }
    }
}
$pdf->SetLineWidth(0.1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - $pdf->GetX(), $pdf->GetY());
$pdf->Ln(1);

// Totais Vendas a Dinheiro
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 7, 'Totais (VD)', 0, 1, 'R');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(155, 7, 'Total VDs: ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalVD, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(155, 7, 'Total DVs: ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalDV, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(155, 7, 'Total IVA (Vendas Dinheiro): ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalIVAVD_vendas, 2, ',', '.'), 0, 1, 'R');
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(155, 7, 'Saldo (VDs - DVs): ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalVD - $totalDV, 2, ',', '.'), 0, 1, 'R');
$pdf->Ln(5);
// -----------------------------------------------------------------------------
// FIM - TRATANDO COM O RELATORIO DE VENDAS A DINHEIRO (VD)
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// INICIO - TRATANDO COM O RELATORIO DE ORDENS DE COMPRA (OC)
// -----------------------------------------------------------------------------
$pdf->AddPage(); // Adicionar nova página para Ordens de Compra
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 10, 'OC - Ordem de Compra', 0, 1);
$pdf->SetLineWidth(0.1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - $pdf->GetX(), $pdf->GetY());
$pdf->Ln(2);

// Cabeçalho da Tabela de Ordens de Compra
$pdf->SetFont('helvetica', 'B', 8);
$header_oc_y_pos = $pdf->GetY();
$pdf->Cell(45, 7, 'Descrição', 0, 0);
$pdf->Cell(30, 7, 'Valor', 0, 0, 'R');
$pdf->Cell(30, 7, 'IVA', 0, 0, 'R');
$pdf->Cell(30, 7, 'Total', 0, 0, 'R');
$pdf->Cell(35, 7, 'Data Emissão', 0, 0, 'C');
$pdf->Ln(8);

$totalOC = 0;
$totalIVAOC_compras = 0; // IVA acumulado das Ordens de Compra

$pdf->SetFont('helvetica', '', 8);

/*
NOTA DE OTIMIZAÇÃO PARA ORDENS DE COMPRA (OC):
Similar às faturas, buscar todas as ordens de compra com informações do fornecedor de uma vez.

Consulta Otimizada para Ordens de Compra:
$sql_oc_detalhes = "
    SELECT
        forn.id AS fornecedor_id,
        forn.nome AS fornecedor_nome,
        oc.id AS oc_id_db,
        oc.serie AS oc_serie,
        oc.n_doc AS oc_n_doc,
        oc.valor AS oc_valor,
        oc.iva AS oc_iva,
        DATE_FORMAT(oc.data, '%d/%m/%Y') AS oc_data_emissao
    FROM
        ordem_compra oc
    JOIN
        fornecedor forn ON oc.fornecedor = forn.id
    WHERE
        oc.data BETWEEN '$data1' AND '$data2'
        -- AND oc.serie = '$serie_para_oc' -- Se houver filtro de série específico para OC
    ORDER BY
        forn.nome, oc.data;
";
// Loop através de $rs_oc_detalhes, similar à seção de faturas, agrupando por fornecedor.
*/

// Implementação simplificada (AINDA PRECISA DE OTIMIZAÇÃO COMPLETA)
$sql_fornecedores_oc = "SELECT DISTINCT(fornecedor) as forn_id FROM `ordem_compra` WHERE data BETWEEN '$data1' AND '$data2'";
$rs_fornecedores_oc = mysqli_query($db, $sql_fornecedores_oc);
if (!$rs_fornecedores_oc) {
    error_log("Erro na consulta de fornecedores OC: " . mysqli_error($db));
} else {
    $current_fornecedor_id_oc = null;
    while ($fornecedor_oc_data = mysqli_fetch_assoc($rs_fornecedores_oc)) {
        $forn_id = $fornecedor_oc_data['forn_id'];
        $sql_fornecedor_info = "SELECT nome FROM `fornecedor` WHERE id = '$forn_id'";
        $rs_fornecedor_info = mysqli_query($db, $sql_fornecedor_info);
        $dados_fornecedor_info = mysqli_fetch_assoc($rs_fornecedor_info);

        if ($pdf->GetY() > ($pdf->GetPageHeight() - 30)) {
             $pdf->AddPage();
             // Re-imprimir cabeçalho da tabela
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->setY($header_oc_y_pos - 5);
            $pdf->Cell(45, 7, 'Descrição', 0, 0);
            $pdf->Cell(30, 7, 'Valor', 0, 0, 'R');
            $pdf->Cell(30, 7, 'IVA', 0, 0, 'R');
            $pdf->Cell(30, 7, 'Total', 0, 0, 'R');
            $pdf->Cell(35, 7, 'Data Emissão', 0, 0, 'C');
            $pdf->Ln(8);
            $pdf->SetFont('helvetica', '', 8);
        }

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(190, 0.1, '', 'T', 1);
        $pdf->Cell(35, 7, 'Entidade/Fornecedor:', 0, 0);
        $pdf->Cell(0, 7, $dados_fornecedor_info['nome'], 0, 1);
        $pdf->SetFont('helvetica', '', 8);

        $sql_ocs_fornecedor = "SELECT serie, n_doc, valor, iva, DATE_FORMAT(data, '%d/%m/%Y') AS data_emissao 
                               FROM ordem_compra 
                               WHERE fornecedor = '$forn_id' AND data BETWEEN '$data1' AND '$data2' ORDER BY data";
        $rs_ocs_fornecedor = mysqli_query($db, $sql_ocs_fornecedor);
        while ($oc_data = mysqli_fetch_assoc($rs_ocs_fornecedor)) {
            $total_oc_item = $oc_data['valor'] + $oc_data['iva'];
            $pdf->Cell(45, 6, "OC#" . $oc_data['serie'] . "/" . $oc_data['n_doc'], 0, 0);
            $pdf->Cell(30, 6, number_format($oc_data['valor'], 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell(30, 6, number_format($oc_data['iva'], 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell(30, 6, number_format($total_oc_item, 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell(35, 6, $oc_data['data_emissao'], 0, 0, 'C');
            $pdf->Ln(6);

            $totalOC += $oc_data['valor'];
            $totalIVAOC_compras += $oc_data['iva'];
        }
    }
}
$pdf->SetLineWidth(0.1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - $pdf->GetX(), $pdf->GetY());
$pdf->Ln(1);

// Totais Ordens de Compra
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 7, 'Totais (OC)', 0, 1, 'R');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(155, 7, 'Total OCs: ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalOC, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell(155, 7, 'Total IVA (Ordens Compra): ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($totalIVAOC_compras, 2, ',', '.'), 0, 1, 'R');
$pdf->SetFont('helvetica', 'B', 10);
$soma_total_oc = $totalOC + $totalIVAOC_compras;
$pdf->Cell(155, 7, 'Total Geral (OCs + IVA): ', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($soma_total_oc, 2, ',', '.'), 0, 1, 'R');
$pdf->Ln(5);
// -----------------------------------------------------------------------------
// FIM - TRATANDO COM O RELATORIO DE ORDENS DE COMPRA (OC)
// -----------------------------------------------------------------------------


// -----------------------------------------------------------------------------
// MAPA DE IMPOSTOS
// -----------------------------------------------------------------------------
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Mapa de Impostos', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetLineWidth(0.1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - $pdf->GetX(), $pdf->GetY());
$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 10);
$col1_width = 140;
$col2_width = 50;

$pdf->Cell($col1_width, 7, 'Total Faturas (FA): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($totalFA, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell($col1_width, 7, 'Total Notas de Crédito (NC): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($totalNC, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell($col1_width, 7, 'Total Vendas Dinheiro (VD): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($totalVD, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell($col1_width, 7, 'Total Devoluções (DV): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($totalDV, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell($col1_width, 7, 'Total Ordem de Compra (OC): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($totalOC, 2, ',', '.'), 0, 1, 'R');
$pdf->Ln(3);
$pdf->Cell($col1_width, 7, 'Total IVA (Faturas): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($totalIVAA_faturas, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell($col1_width, 7, 'Total IVA (Vendas Dinheiro): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($totalIVAVD_vendas, 2, ',', '.'), 0, 1, 'R');
$pdf->Cell($col1_width, 7, 'Total IVA (Ordem de Compra): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($totalIVAOC_compras, 2, ',', '.'), 0, 1, 'R');
$pdf->Ln(3);
$pdf->SetFont('helvetica', 'B', 10);
$diferenca_iva = ($totalIVAA_faturas + $totalIVAVD_vendas) - $totalIVAOC_compras;
$pdf->Cell($col1_width, 7, 'Diferença de IVA (IVA Liquidado - IVA Suportado): ', 0, 0, 'L');
$pdf->Cell($col2_width, 7, number_format($diferenca_iva, 2, ',', '.'), 0, 1, 'R');

$pdf->Ln(5);
$pdf->SetLineWidth(0.1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetPageWidth() - $pdf->GetX(), $pdf->GetY());
$pdf->Ln(2);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 7, 'Documento Processado por Computador / iVone ERP', 0, 1, 'C');


// Output do PDF
// Limpar qualquer saída anterior (buffering) antes de enviar o PDF
if (ob_get_length()) ob_end_clean();

$pdf->Output('Relatorio_Extrato_Contas.pdf', 'I'); // 'I' para inline, 'D' para download

// Fechar conexão e liberar recursos
if (isset($rs_empresa)) mysqli_free_result($rs_empresa);
if (isset($rs_faturas_detalhes)) mysqli_free_result($rs_faturas_detalhes);
if (isset($rs_clientes_vd)) mysqli_free_result($rs_clientes_vd);
// ... liberar outros resultsets ...
mysqli_close($db);
exit; // Terminar o script após enviar o PDF

?>