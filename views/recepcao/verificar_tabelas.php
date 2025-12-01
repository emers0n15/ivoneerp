<?php
/**
 * Script de Verificação de Tabelas do Módulo de Recepção
 * Execute este arquivo para verificar se todas as tabelas necessárias foram criadas
 */

session_start();
include '../../conexao/index.php';

$tabelas_necessarias = [
    'empresas_seguros',
    'tabelas_precos',
    'tabela_precos_servicos',
    'pacientes',
    'paciente_empresa_historico',
    'servicos_clinica',
    'faturas_atendimento',
    'fatura_servicos',
    'pagamentos_recepcao',
    'historico_atendimentos',
    'caixa_recepcao',
    'auditoria_recepcao'
];

$tabelas_existentes = [];
$tabelas_faltando = [];

foreach ($tabelas_necessarias as $tabela) {
    $sql = "SHOW TABLES LIKE '$tabela'";
    $result = mysqli_query($db, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $tabelas_existentes[] = $tabela;
    } else {
        $tabelas_faltando[] = $tabela;
    }
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Tabelas - Recepção</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #3D5DFF;
            margin-bottom: 30px;
        }
        .status {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            margin-top: 20px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 8px;
            margin: 5px 0;
            background: #f8f9fa;
            border-left: 3px solid #3D5DFF;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #3D5DFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #2d4aef;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verificação de Tabelas do Módulo de Recepção</h1>
        
        <?php if (empty($tabelas_faltando)): ?>
            <div class="status success">
                <strong>✓ Todas as tabelas foram criadas com sucesso!</strong>
                <p>Todas as <?php echo count($tabelas_existentes); ?> tabelas necessárias estão presentes no banco de dados.</p>
            </div>
        <?php else: ?>
            <div class="status error">
                <strong>✗ Algumas tabelas estão faltando!</strong>
                <p>Foram encontradas <?php echo count($tabelas_existentes); ?> de <?php echo count($tabelas_necessarias); ?> tabelas.</p>
            </div>
            
            <h3>Tabelas Faltando (<?php echo count($tabelas_faltando); ?>):</h3>
            <ul>
                <?php foreach ($tabelas_faltando as $tabela): ?>
                    <li><?php echo htmlspecialchars($tabela); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <?php if (!empty($tabelas_existentes)): ?>
            <h3>Tabelas Existentes (<?php echo count($tabelas_existentes); ?>):</h3>
            <ul>
                <?php foreach ($tabelas_existentes as $tabela): ?>
                    <li style="border-left-color: #28a745;">✓ <?php echo htmlspecialchars($tabela); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <?php if (!empty($tabelas_faltando)): ?>
            <div class="status info">
                <strong>Como resolver:</strong>
                <ol>
                    <li>Abra o phpMyAdmin</li>
                    <li>Selecione o banco de dados <strong>ivoneerp</strong></li>
                    <li>Vá na aba "SQL"</li>
                    <li>Abra o arquivo <code>views/recepcao/recepcao.sql</code></li>
                    <li>Cole todo o conteúdo e execute</li>
                    <li>Recarregue esta página para verificar novamente</li>
                </ol>
            </div>
        <?php endif; ?>
        
        <a href="dashboard.php" class="btn">Voltar ao Dashboard</a>
    </div>
</body>
</html>

