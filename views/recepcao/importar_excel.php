<?php
/**
 * Script de Importação de Dados de Excel
 * 
 * Este script permite importar procedimentos e preços de ficheiros Excel
 * para as tabelas servicos_clinica e tabela_precos_servicos
 * 
 * REQUISITOS:
 * - Biblioteca PHPSpreadsheet instalada via Composer
 * - Ou converter Excel para CSV primeiro
 * 
 * USO:
 * 1. Instalar PHPSpreadsheet: composer require phpoffice/phpspreadsheet
 * 2. Fazer upload dos ficheiros Excel ou converter para CSV
 * 3. Acessar esta página via navegador
 */

session_start();
if(!isset($_SESSION['idUsuario'])){
    header("location:../../");
    exit;
}

if($_SESSION['categoriaUsuario'] != "recepcao"){
    header("location:../admin/");
    exit;
}

include '../../conexao/index.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$userID = $_SESSION['idUsuario'];

// Verificar se PHPSpreadsheet está disponível
$phpspreadsheet_available = false;
if(file_exists(__DIR__ . '/../../vendor/autoload.php')){
    require_once __DIR__ . '/../../vendor/autoload.php';
    $phpspreadsheet_available = class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet');
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Dados de Excel</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            background: #f9f9f9;
        }
        .upload-area:hover {
            border-color: #007bff;
            background: #f0f8ff;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        .error-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>📥 Importar Dados de Excel</h2>
        
        <?php if(!$phpspreadsheet_available): ?>
            <div class="warning-box">
                <h5>⚠️ Biblioteca PHPSpreadsheet não encontrada</h5>
                <p>Para importar ficheiros Excel (.xlsx), é necessário instalar a biblioteca PHPSpreadsheet.</p>
                <p><strong>Opção 1 - Instalar PHPSpreadsheet:</strong></p>
                <pre style="background: #fff; padding: 10px; border-radius: 5px;">cd C:\xampp\htdocs\ivoneerp
composer require phpoffice/phpspreadsheet</pre>
                
                <p><strong>Opção 2 - Converter para CSV:</strong></p>
                <p>Pode converter os ficheiros Excel para CSV e usar o importador CSV abaixo.</p>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <h5>ℹ️ Informações Importantes</h5>
            <ul>
                <li>Os ficheiros Excel devem conter colunas: <strong>Código, Nome, Categoria, Preço</strong></li>
                <li>Pode importar serviços e preços por empresa</li>
                <li>Serviços duplicados (mesmo código) serão atualizados, não duplicados</li>
                <li>Selecione a empresa antes de importar preços específicos</li>
            </ul>
        </div>

        <!-- Formulário de Upload -->
        <div class="card mt-4">
            <div class="card-header">
                <h4>Upload de Ficheiro</h4>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data" action="processar_importacao.php">
                    <div class="form-group">
                        <label for="tipo_importacao">Tipo de Importação:</label>
                        <select class="form-control" id="tipo_importacao" name="tipo_importacao" required>
                            <option value="servicos">Apenas Serviços/Procedimentos</option>
                            <option value="precos">Preços por Empresa</option>
                            <option value="ambos">Serviços e Preços</option>
                        </select>
                    </div>

                    <div class="form-group" id="empresa_select_group" style="display: none;">
                        <label for="empresa_id">Empresa/Seguradora:</label>
                        <select class="form-control" id="empresa_id" name="empresa_id">
                            <option value="">Selecione a empresa...</option>
                            <?php
                            $sql_empresas = "SELECT id, nome FROM empresas_seguros WHERE ativo = 1 ORDER BY nome";
                            $rs_empresas = mysqli_query($db, $sql_empresas);
                            if($rs_empresas){
                                while($emp = mysqli_fetch_array($rs_empresas)){
                                    echo "<option value=\"{$emp['id']}\">{$emp['nome']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ficheiro a Importar:</label>
                        <div class="upload-area">
                            <input type="file" name="arquivo" accept=".xlsx,.xls,.csv" required class="form-control-file">
                            <p class="mt-3 text-muted">
                                Formatos aceites: .xlsx, .xls, .csv<br>
                                <strong>Formato esperado do CSV:</strong><br>
                                <small>Ordem;Sector;Nome do Procedimento;Preço</small><br>
                                Ficheiros disponíveis:
                                <ul class="text-left mt-2">
                                    <li>Cópia de Vulcan Prices - Final to providers.xlsx</li>
                                    <li>Clínica Médica Monte Sinai fidelidade para arranjos.csv</li>
                                </ul>
                            </p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Configuração de Colunas:</label>
                        <small class="form-text text-muted">Mapeie as colunas do seu Excel para os campos do sistema</small>
                        <div class="row mt-2">
                            <div class="col-md-3">
                                <label>Coluna do Código:</label>
                                <select class="form-control" name="col_codigo">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Coluna do Nome:</label>
                                <select class="form-control" name="col_nome">
                                    <option value="A">A</option>
                                    <option value="B" selected>B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Coluna da Categoria:</label>
                                <select class="form-control" name="col_categoria">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C" selected>C</option>
                                    <option value="D">D</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Coluna do Preço:</label>
                                <select class="form-control" name="col_preco">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D" selected>D</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="pular_cabecalho" value="1" checked>
                            Primeira linha é cabeçalho (pular)
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-upload"></i> Importar Dados
                    </button>
                    <a href="servicos_clinica.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>

        <!-- Instruções -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>📖 Instruções de Uso</h5>
            </div>
            <div class="card-body">
                <h6>Passo 1: Preparar o Ficheiro</h6>
                <p>Certifique-se de que o ficheiro Excel tem as seguintes colunas (pode estar em qualquer ordem):</p>
                <ul>
                    <li><strong>Código</strong> - Código único do procedimento</li>
                    <li><strong>Nome</strong> - Nome do procedimento</li>
                    <li><strong>Categoria</strong> - Categoria do procedimento</li>
                    <li><strong>Preço</strong> - Preço do procedimento</li>
                </ul>

                <h6 class="mt-3">Passo 2: Selecionar Tipo de Importação</h6>
                <ul>
                    <li><strong>Apenas Serviços:</strong> Importa apenas os procedimentos para a tabela geral</li>
                    <li><strong>Preços por Empresa:</strong> Importa preços específicos para uma empresa selecionada</li>
                    <li><strong>Serviços e Preços:</strong> Importa ambos de uma vez</li>
                </ul>

                <h6 class="mt-3">Passo 3: Mapear Colunas</h6>
                <p>Indique qual coluna do Excel corresponde a cada campo (A, B, C, D, etc.)</p>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#tipo_importacao').change(function(){
                var tipo = $(this).val();
                if(tipo === 'precos' || tipo === 'ambos'){
                    $('#empresa_select_group').show();
                    $('#empresa_id').prop('required', true);
                } else {
                    $('#empresa_select_group').hide();
                    $('#empresa_id').prop('required', false);
                }
            });
        });
    </script>
</body>
</html>

