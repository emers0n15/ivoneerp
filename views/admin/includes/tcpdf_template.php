<?php
// Reusable TCPDF template for consistent styling
require_once(__DIR__ . '/../biblioteca/tcpdf.php');

class ThemedTCPDF extends TCPDF {
    protected $empresa;
    protected $titulo;

    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $empresa=[], $titulo='') {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        $this->empresa = $empresa ?: [];
        $this->titulo = $titulo ?: '';
        // Aumentar margem superior para evitar sobreposição do cabeçalho/título com a primeira tabela
        $this->SetMargins(10, 68, 10);
        $this->SetHeaderMargin(10);
        $this->SetFooterMargin(10);
        $this->SetAutoPageBreak(TRUE, 15);
    }

    public function Header() {
        $empresa = $this->empresa;
        // Logo (checar múltiplos caminhos relativos)
        if (!empty($empresa['img'])) {
            $candidates = [
                __DIR__ . '/../../../img/' . $empresa['img'], // htdocs/img
                __DIR__ . '/../../img/' . $empresa['img'],    // views/img (fallback)
                __DIR__ . '/../img/' . $empresa['img'],       // admin/img (fallback)
            ];
            foreach ($candidates as $candidate) {
                if (is_string($candidate) && file_exists($candidate)) {
                    $this->Image($candidate, 150, 10, 45);
                    break;
                }
            }
        }
        // Empresa
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(0, 64, 128);
        $this->SetXY(10, 10);
        $this->Cell(0, 10, ($empresa['nome'] ?? ''), 0, 1, 'L');
        
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(0, 0, 0);
        if (!empty($empresa['endereco'])) {
            $this->SetX(10);
            $this->Cell(0, 6, $empresa['endereco'], 0, 1, 'L');
        }
        $local = '';
        if (!empty($empresa['pais'])) $local .= $empresa['pais'];
        if (!empty($empresa['provincia'])) $local .= ($local ? ' - ' : '') . $empresa['provincia'];
        if ($local) {
            $this->SetX(10);
            $this->Cell(0, 6, $local, 0, 1, 'L');
        }
        $cont = '';
        if (!empty($empresa['nuit'])) $cont .= 'Nuit: ' . $empresa['nuit'];
        if (!empty($empresa['contacto'])) $cont .= ($cont ? ' | ' : '') . 'Tel: ' . $empresa['contacto'];
        if (!empty($empresa['email'])) $cont .= ($cont ? ' | ' : '') . 'Email: ' . $empresa['email'];
        if ($cont) {
            $this->SetX(10);
            $this->Cell(0, 6, $cont, 0, 1, 'L');
        }
        // Linha separadora
        $this->SetDrawColor(41, 76, 139);
        $this->Line(10, $this->GetY() + 2, 200, $this->GetY() + 2);
        $this->Ln(8);
        // Título (quebra automática em múltiplas linhas)
        if (!empty($this->titulo)) {
            $this->SetFont('helvetica', 'B', 14);
            $this->SetTextColor(0, 0, 0);
            // MultiCell usa a largura total disponível entre as margens para centralizar e quebrar
            $this->MultiCell(0, 8, $this->titulo, 0, 'C', false, 1);
            $this->Ln(1);
        }
        $this->SetFont('helvetica', 'I', 9);
        $this->Cell(0, 6, 'Data: ' . date('d/m/Y'), 0, 1, 'C');
        $this->Ln(4);
    }

    public function Footer() {
        $this->SetY(-20);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY() - 2, 200, $this->GetY() - 2);
        $this->Ln(3);
        $user = isset($_SESSION['nomeUsuario']) ? $_SESSION['nomeUsuario'] : 'Sistema';
        $this->Cell(0, 5, 'Gerado por: ' . $user, 0, 1);
        $this->Cell(0, 5, 'Data de geração: ' . date('d/m/Y H:i:s'), 0, 1);
        $this->SetFont('helvetica', 'I', 9);
        $this->Cell(0, 6, 'Farmacia BANDULA - Sistema de Gestão Farmacêutica', 0, 1, 'C');
        // Número de página (opcional abaixo)
        $this->SetY(-10);
        $this->Cell(0, 8, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

function tcpdf_table_header($pdf, $columns) {
    // $columns: [ [label, width, align(optional)], ... ]
    $pdf->SetFont('helvetica', 'B', 9);
    // Azul mais próximo do relatório de inventário
    $pdf->SetFillColor(41, 76, 139);
    $pdf->SetDrawColor(41, 76, 139);
    $pdf->SetTextColor(255, 255, 255);
    foreach ($columns as $col) {
        $label = $col[0];
        $w = $col[1];
        $align = isset($col[2]) ? $col[2] : 'C';
        $pdf->Cell($w, 8, $label, 1, 0, $align, 1);
    }
    $pdf->Ln();
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetTextColor(0, 0, 0);
    // Bordas discretas para o corpo
    $pdf->SetDrawColor(160, 160, 160);
}

function tcpdf_should_addpage_and_header($pdf, $thresholdY, $columns) {
    if ($pdf->GetY() > $thresholdY) {
        $pdf->AddPage();
        tcpdf_table_header($pdf, $columns);
    }
}

// Helper para zebra rows: retorna true/false para usar como parâmetro $fill das Cells
function tcpdf_row_fill_toggle($rowIndex, $isWarning = false) {
    if ($isWarning) {
        // Vermelho claro para avisos (ex.: stock abaixo do mínimo)
        return [true, [255, 200, 200]];
    }
    // Cinza muito claro alternado
    if ($rowIndex % 2 === 0) {
        return [true, [245, 245, 245]];
    }
    return [false, [255, 255, 255]];
}
