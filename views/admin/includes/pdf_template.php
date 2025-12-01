<?php
// Reusable PDF template helpers for tFPDF
// Usage: require_once 'includes/pdf_template.php';

function pdf_add_company_header($pdf, $db, $titulo = '') {
    // Logo + Empresa + Linha + Título + Data
    $sql_empresa = "SELECT * FROM empresa LIMIT 1";
    $rs_empresa = mysqli_query($db, $sql_empresa);

    // Top spacing
    $pdf->SetY(10);

    if ($rs_empresa && ($dados = mysqli_fetch_array($rs_empresa))) {
        if (!empty($dados['img'])) {
            $logo_path = '../../img/' . $dados['img'];
            if (file_exists($logo_path)) {
                $pdf->Image($logo_path, 150, 10, 45, 0);
            }
        }
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->Cell(0, 10, $dados['nome'], 0, 1, 'L');

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        if (!empty($dados['endereco'])) {
            $pdf->Cell(0, 6, $dados['endereco'], 0, 1, 'L');
        }
        $local = '';
        if (!empty($dados['pais'])) { $local .= $dados['pais']; }
        if (!empty($dados['provincia'])) { $local .= ($local ? ' - ' : '') . $dados['provincia']; }
        if ($local) { $pdf->Cell(0, 6, $local, 0, 1, 'L'); }

        $cont = '';
        if (!empty($dados['nuit'])) { $cont .= 'Nuit: ' . $dados['nuit']; }
        if (!empty($dados['contacto'])) { $cont .= ($cont ? ' | ' : '') . 'Tel: ' . $dados['contacto']; }
        if (!empty($dados['email'])) { $cont .= ($cont ? ' | ' : '') . 'Email: ' . $dados['email']; }
        if ($cont) { $pdf->Cell(0, 6, $cont, 0, 1, 'L'); }

        // Linha separadora
        $pdf->SetLineWidth(0.3);
        $pdf->SetDrawColor(0, 0, 128);
        $pdf->Line(10, $pdf->GetY() + 2, 200, $pdf->GetY() + 2);
        $pdf->Ln(8);
    }

    if ($titulo) {
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, $titulo, 0, 1, 'C');
        $pdf->Ln(3);
    }

    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 6, 'Data: ' . date('d/m/Y'), 0, 1, 'C');
    $pdf->Ln(5);
}

function pdf_table_header($pdf, $columns) {
    // $columns: [ [label, width], ... ]
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(70, 130, 180);
    $pdf->SetTextColor(255, 255, 255);
    foreach ($columns as $col) {
        $label = $col[0];
        $w = $col[1];
        $pdf->Cell($w, 8, $label, 1, 0, 'C', true);
    }
    $pdf->Ln();
    // Reset text for body
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0, 0, 0);
}

function pdf_add_footer($pdf) {
    $pdf->SetY(-30);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->SetLineWidth(0.1);
    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Line(10, $pdf->GetY() - 2, 200, $pdf->GetY() - 2);
    $pdf->Ln(3);
    $pdf->Cell(0, 5, 'Gerado por: ' . ($_SESSION['nomeUsuario'] ?? 'Sistema'), 0, 1);
    $pdf->Cell(0, 5, 'Data de geração: ' . date('d/m/Y H:i:s'), 0, 1);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 6, 'Farmacia BANDULA - Sistema de Gestão Farmacêutica', 0, 1, 'C');
}

function pdf_should_addpage_and_header($pdf, $thresholdY, $columns) {
    if ($pdf->GetY() > $thresholdY) {
        $pdf->AddPage();
        pdf_table_header($pdf, $columns);
    }
}
