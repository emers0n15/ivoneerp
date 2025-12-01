<?php
// Iniciar buffer de saída para evitar qualquer output indesejado
ob_start();

session_start();
include '../../conexao/index.php';

// Verificar se o usuário tem permissão de recepção
if($_SESSION['categoriaUsuario'] != "recepcao"){
	header("location:../admin/");
	exit;
}

if(!isset($_GET['id'])){
	header("location:faturas.php");
	exit;
}

$fatura_id = intval($_GET['id']);

// Verificar se a tabela factura_recepcao existe
$check_table = "SHOW TABLES LIKE 'factura_recepcao'";
$table_exists = mysqli_query($db, $check_table);
$use_new_table = ($table_exists && mysqli_num_rows($table_exists) > 0);

if($use_new_table) {
    // Usar nova tabela factura_recepcao
    // Primeiro verificar se a coluna factura_recepcao_id existe em pagamentos_recepcao
    $check_col = "SHOW COLUMNS FROM pagamentos_recepcao LIKE 'factura_recepcao_id'";
    $col_exists = mysqli_query($db, $check_col);
    $has_factura_recepcao_id = ($col_exists && mysqli_num_rows($col_exists) > 0);
    
    if($has_factura_recepcao_id) {
        $sql = "SELECT f.*, p.nome, p.apelido, p.numero_processo, p.contacto, p.documento_tipo, p.documento_numero,
                e.nome as empresa_nome, e.nuit as empresa_nuit, e.contacto as empresa_contacto, 
                e.email as empresa_email, e.endereco as empresa_endereco,
                COALESCE((SELECT SUM(valor_pago) FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id), 0) as total_pago,
                (SELECT metodo_pagamento FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id ORDER BY data_pagamento DESC LIMIT 1) as metodo_pagamento,
                (SELECT referencia_pagamento FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id ORDER BY data_pagamento DESC LIMIT 1) as referencia_pagamento,
                (SELECT data_pagamento FROM pagamentos_recepcao WHERE factura_recepcao_id = f.id ORDER BY data_pagamento DESC LIMIT 1) as data_pagamento
                FROM factura_recepcao f 
                INNER JOIN pacientes p ON f.paciente = p.id 
                LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
                WHERE f.id = $fatura_id";
    } else {
        // Se a coluna não existe, usar apenas factura_recepcao sem subquery de pagamentos
        $sql = "SELECT f.*, p.nome, p.apelido, p.numero_processo, p.contacto, p.documento_tipo, p.documento_numero,
                e.nome as empresa_nome, e.nuit as empresa_nuit, e.contacto as empresa_contacto, 
                e.email as empresa_email, e.endereco as empresa_endereco,
                0 as total_pago,
                NULL as metodo_pagamento,
                NULL as referencia_pagamento,
                NULL as data_pagamento
                FROM factura_recepcao f 
                INNER JOIN pacientes p ON f.paciente = p.id 
                LEFT JOIN empresas_seguros e ON f.empresa_id = e.id
                WHERE f.id = $fatura_id";
    }
    
    $rs = mysqli_query($db, $sql);
    if(!$rs) {
        error_log("Erro na query de fatura: " . mysqli_error($db));
        header("location:faturas.php?msg=erro_query");
        exit;
    }
    
    $fatura = mysqli_fetch_array($rs);
    
    if(!$fatura) {
        header("location:faturas.php?msg=fatura_nao_encontrada");
        exit;
    }
    
    // Se não tem factura_recepcao_id, buscar pagamentos pela fatura_id antiga
    if(!$has_factura_recepcao_id) {
        $sql_pag = "SELECT SUM(valor_pago) as total, 
                           MAX(metodo_pagamento) as metodo_pagamento,
                           MAX(referencia_pagamento) as referencia_pagamento,
                           MAX(data_pagamento) as data_pagamento
                    FROM pagamentos_recepcao 
                    WHERE fatura_id = $fatura_id";
        $rs_pag = mysqli_query($db, $sql_pag);
        if($rs_pag && mysqli_num_rows($rs_pag) > 0) {
            $pag_data = mysqli_fetch_array($rs_pag);
            $fatura['total_pago'] = floatval($pag_data['total'] ?? 0);
            $fatura['metodo_pagamento'] = $pag_data['metodo_pagamento'] ?? null;
            $fatura['referencia_pagamento'] = $pag_data['referencia_pagamento'] ?? null;
            $fatura['data_pagamento'] = $pag_data['data_pagamento'] ?? null;
        } else {
            // Garantir que os campos existam mesmo se não houver pagamentos
            $fatura['total_pago'] = $fatura['total_pago'] ?? 0;
            $fatura['metodo_pagamento'] = $fatura['metodo_pagamento'] ?? null;
            $fatura['referencia_pagamento'] = $fatura['referencia_pagamento'] ?? null;
            $fatura['data_pagamento'] = $fatura['data_pagamento'] ?? null;
        }
    }
    
    // Buscar serviços
    $sql_servicos = "SELECT fs.*, s.nome as servico_nome 
                     FROM fa_servicos_fact_recepcao fs 
                     INNER JOIN servicos_clinica s ON fs.servico = s.id 
                     WHERE fs.factura = $fatura_id";
    $rs_servicos = mysqli_query($db, $sql_servicos);
    if(!$rs_servicos) {
        error_log("Erro na query de serviços: " . mysqli_error($db));
        $rs_servicos = false; // Continuar mesmo sem serviços
    }
    
    // Ajustar campos para compatibilidade
    $fatura['numero_fatura'] = "FA#" . $fatura['serie'] . "/" . $fatura['n_doc'];
    // Calcular subtotal (valor total + desconto, pois desconto já foi aplicado)
    $desconto = floatval($fatura['disconto'] ?? 0);
    $valor_total = floatval($fatura['valor']);
    $fatura['subtotal'] = $valor_total + $desconto; // Subtotal antes do desconto
    $fatura['desconto'] = $desconto;
    $fatura['total'] = $valor_total;
    $fatura['valor_pago'] = floatval($fatura['total_pago'] ?? 0);
} else {
    // Fallback para tabela antiga
    $sql = "SELECT f.*, p.nome, p.apelido, p.numero_processo, p.contacto, p.documento_tipo, p.documento_numero,
            pr.valor_pago, pr.metodo_pagamento, pr.referencia_pagamento, pr.data_pagamento
            FROM faturas_atendimento f 
            INNER JOIN pacientes p ON f.paciente_id = p.id 
            LEFT JOIN pagamentos_recepcao pr ON f.id = pr.fatura_id
            WHERE f.id = $fatura_id AND f.status = 'paga'";
    $rs = mysqli_query($db, $sql);
    if(!$rs) {
        error_log("Erro na query de fatura (antiga): " . mysqli_error($db));
        header("location:faturas.php?msg=erro_query");
        exit;
    }
    
    $fatura = mysqli_fetch_array($rs);
    
    if(!$fatura){
        header("location:faturas.php?msg=fatura_nao_encontrada");
        exit;
    }
    
    // Buscar serviços
    $sql_servicos = "SELECT fs.*, s.nome as servico_nome 
                     FROM fatura_servicos fs 
                     INNER JOIN servicos_clinica s ON fs.servico_id = s.id 
                     WHERE fs.fatura_id = $fatura_id";
    $rs_servicos = mysqli_query($db, $sql_servicos);
    if(!$rs_servicos) {
        error_log("Erro na query de serviços (antiga): " . mysqli_error($db));
        $rs_servicos = false; // Continuar mesmo sem serviços
    }
}

// Verificar qual caminho do FPDF existe
if(file_exists('../../fpdf/fpdf.php')) {
    require_once('../../fpdf/fpdf.php');
} elseif(file_exists('../../../fpdf/fpdf.php')) {
    require_once('../../../fpdf/fpdf.php');
} else {
    die('Erro: Biblioteca FPDF não encontrada. Por favor, verifique a instalação.');
}

// Buscar dados da empresa principal para o logo
$sql_empresa = "SELECT * FROM empresa LIMIT 1";
$rs_empresa = mysqli_query($db, $sql_empresa);
$empresa_principal = null;
if($rs_empresa && mysqli_num_rows($rs_empresa) > 0) {
    $empresa_principal = mysqli_fetch_array($rs_empresa);
}

class PDF extends FPDF {
    private $empresa_principal;
    
    function __construct($empresa_principal = null) {
        parent::__construct();
        $this->empresa_principal = $empresa_principal;
    }
    
    function Header() {
        $this->SetFont('Arial','B',16);
        $this->SetY(10);
        $this->Cell(0,10,'RECIBO DE PAGAMENTO',0,1,'C');
        $this->SetFont('Arial','',10);
        if($this->empresa_principal && !empty($this->empresa_principal['nome'])) {
            $this->Cell(0,5,$this->empresa_principal['nome'],0,1,'C');
        } else {
            $this->Cell(0,5,'Clinica - Sistema de Gestao Clinica',0,1,'C');
        }
        
        // Logo da empresa no canto superior direito (se existir)
        if($this->empresa_principal && !empty($this->empresa_principal['img'])) {
            // Tentar múltiplos caminhos possíveis
            $logo_paths = [
                '../../img/' . $this->empresa_principal['img'],
                '../../img/config/' . $this->empresa_principal['img'],
                '../../../img/' . $this->empresa_principal['img'],
                '../../../img/config/' . $this->empresa_principal['img']
            ];
            
            $logo_found = false;
            foreach($logo_paths as $logo_path) {
                if(file_exists($logo_path)) {
                    // Posicionar no canto superior direito (largura da página - margem - largura da imagem)
                    // Y = 25 para ficar um pouco abaixo do título
                    $this->Image($logo_path, 170, 25, 30, 0);
                    $logo_found = true;
                    break;
                }
            }
            
            // Se não encontrou, tentar apenas o nome do arquivo (pode estar em outro diretório)
            if(!$logo_found && file_exists($this->empresa_principal['img'])) {
                $this->Image($this->empresa_principal['img'], 170, 25, 30, 0);
            }
        }
        
        $this->Ln(5);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

$pdf = new PDF($empresa_principal);
$pdf->AliasNbPages();
$pdf->AddPage();

// Dados do recibo
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Numero do Recibo: ' . $fatura['numero_fatura'],0,1);
$pdf->Cell(0,5,'Data de Emissao: ' . date('d/m/Y H:i:s'),0,1);
$pdf->Ln(5);

// Dados da Empresa/Seguradora (se houver)
if(!empty($fatura['empresa_nome'])) {
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,5,'DADOS DA EMPRESA/SEGURADORA',0,1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,5,'Nome: ' . $fatura['empresa_nome'],0,1);
    if(!empty($fatura['empresa_nuit'])) {
        $pdf->Cell(0,5,'NUIT: ' . $fatura['empresa_nuit'],0,1);
    }
    if(!empty($fatura['empresa_endereco'])) {
        $pdf->Cell(0,5,'Endereco: ' . $fatura['empresa_endereco'],0,1);
    }
    if(!empty($fatura['empresa_contacto'])) {
        $pdf->Cell(0,5,'Contacto: ' . $fatura['empresa_contacto'],0,1);
    }
    if(!empty($fatura['empresa_email'])) {
        $pdf->Cell(0,5,'Email: ' . $fatura['empresa_email'],0,1);
    }
    $pdf->Ln(5);
}

// Dados do paciente
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'DADOS DO PACIENTE',0,1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Nome: ' . $fatura['nome'] . ' ' . $fatura['apelido'],0,1);
$pdf->Cell(0,5,'Numero de Processo: ' . $fatura['numero_processo'],0,1);
if($fatura['documento_numero']) {
    $pdf->Cell(0,5,'Documento: ' . $fatura['documento_tipo'] . ' - ' . $fatura['documento_numero'],0,1);
}
if(isset($fatura['contacto']) && $fatura['contacto']) {
    $pdf->Cell(0,5,'Contacto: ' . $fatura['contacto'],0,1);
}
$pdf->Ln(5);

// Servicos
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'SERVICOS REALIZADOS',0,1);
$pdf->SetFont('Arial','',10);

$pdf->SetFillColor(200,200,200);
$pdf->Cell(100,7,'Servico',1,0,'L',true);
$pdf->Cell(30,7,'Quantidade',1,0,'C',true);
$pdf->Cell(30,7,'Preco Unit.',1,0,'R',true);
$pdf->Cell(30,7,'Subtotal',1,1,'R',true);

$pdf->SetFillColor(255,255,255);
if($rs_servicos && mysqli_num_rows($rs_servicos) > 0) {
    while($servico = mysqli_fetch_array($rs_servicos)) {
        $qtd = $servico['qtd'] ?? $servico['quantidade'] ?? 1;
        $preco_unit = $servico['preco'] ?? $servico['preco_unitario'] ?? 0;
        $subtotal = $servico['total'] ?? $servico['subtotal'] ?? ($qtd * $preco_unit);
        
        $pdf->Cell(100,7,$servico['servico_nome'],1,0,'L');
        $pdf->Cell(30,7,$qtd,1,0,'C');
        $pdf->Cell(30,7,number_format($preco_unit, 2, ',', '.') . ' MT',1,0,'R');
        $pdf->Cell(30,7,number_format($subtotal, 2, ',', '.') . ' MT',1,1,'R');
    }
} else {
    $pdf->Cell(190,7,'Nenhum serviço encontrado',1,1,'C');
}

$pdf->Ln(2);
$pdf->SetFont('Arial','B',10);
$pdf->Cell(160,7,'Subtotal:',0,0,'R');
$pdf->Cell(30,7,number_format($fatura['subtotal'], 2, ',', '.') . ' MT',1,1,'R');

if($fatura['desconto'] > 0) {
    $pdf->Cell(160,7,'Desconto:',0,0,'R');
    $pdf->Cell(30,7,'-' . number_format($fatura['desconto'], 2, ',', '.') . ' MT',1,1,'R');
}

$pdf->SetFont('Arial','B',12);
$pdf->Cell(160,10,'TOTAL PAGO:',0,0,'R');
$pdf->Cell(30,10,number_format($fatura['total'], 2, ',', '.') . ' MT',1,1,'R',true);
$pdf->Ln(5);

// Dados do pagamento
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,5,'DADOS DO PAGAMENTO',0,1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'Valor Pago: ' . number_format($fatura['valor_pago'], 2, ',', '.') . ' MT',0,1);
if($fatura['metodo_pagamento']) {
    $metodo = str_replace('_', ' ', $fatura['metodo_pagamento']);
    $pdf->Cell(0,5,'Metodo de Pagamento: ' . ucfirst($metodo),0,1);
}
if($fatura['referencia_pagamento']) {
    $pdf->Cell(0,5,'Referencia: ' . $fatura['referencia_pagamento'],0,1);
}
if($fatura['data_pagamento']) {
    $pdf->Cell(0,5,'Data do Pagamento: ' . date('d/m/Y H:i', strtotime($fatura['data_pagamento'])),0,1);
}
$pdf->Ln(10);

// Assinatura
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,'_________________________________',0,1,'C');
$pdf->Cell(0,5,'Assinatura do Recepcionista',0,1,'C');

// Garantir que o PDF abra no navegador (inline) ao invés de baixar
// 'I' = Inline (abre no navegador em nova aba)
// 'D' = Download (força download)
// Limpar qualquer buffer de saída antes de enviar o PDF
ob_end_clean();
$pdf->Output('recibo_' . $fatura['numero_fatura'] . '.pdf', 'I');
?>

