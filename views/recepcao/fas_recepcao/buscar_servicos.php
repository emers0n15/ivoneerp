<?php
session_start();
include_once '../../../conexao/index.php';
header('Content-Type: application/json');

$userID = $_SESSION['idUsuario'] ?? null;
$empresa_id = isset($_POST['empresa_id']) && $_POST['empresa_id'] != '' ? intval($_POST['empresa_id']) : null;

// Buscar tabela de preços da empresa (se houver)
$tabela_precos_id = null;
$desconto_geral = 0;
if($empresa_id && $empresa_id > 0) {
    $sql_empresa = "SELECT tabela_precos_id, desconto_geral FROM empresas_seguros WHERE id = ?";
    $stmt = mysqli_prepare($db, $sql_empresa);
    mysqli_stmt_bind_param($stmt, "i", $empresa_id);
    mysqli_stmt_execute($stmt);
    $rs_empresa = mysqli_stmt_get_result($stmt);
    if($rs_empresa && mysqli_num_rows($rs_empresa) > 0) {
        $empresa_data = mysqli_fetch_array($rs_empresa);
        $tabela_precos_id = $empresa_data['tabela_precos_id'] ?? null;
        $desconto_geral = floatval($empresa_data['desconto_geral'] ?? 0);
    }
}

// Buscar serviços
$sql_servicos = "SELECT * FROM servicos_clinica WHERE ativo = 1 ORDER BY categoria, nome";
$rs_servicos = mysqli_query($db, $sql_servicos);

$servicos = [];
if(!$rs_servicos) {
    echo json_encode(['servicos' => [], 'erro' => 'Erro ao buscar serviços: ' . mysqli_error($db)]);
    exit;
}

$total_servicos = mysqli_num_rows($rs_servicos);
if($total_servicos > 0) {
    while ($servico = mysqli_fetch_array($rs_servicos)) {
        $servico_id = intval($servico['id']);
        $preco_final = floatval($servico['preco']);
        
        // Buscar preço contratado se houver tabela de preços
        if($tabela_precos_id) {
            $sql_preco = "SELECT preco, desconto_percentual FROM tabela_precos_servicos 
                         WHERE tabela_precos_id = ? AND servico_id = ?";
            $stmt = mysqli_prepare($db, $sql_preco);
            mysqli_stmt_bind_param($stmt, "ii", $tabela_precos_id, $servico_id);
            mysqli_stmt_execute($stmt);
            $rs_preco = mysqli_stmt_get_result($stmt);
            
            if($rs_preco && mysqli_num_rows($rs_preco) > 0) {
                $preco_data = mysqli_fetch_array($rs_preco);
                $preco_final = floatval($preco_data['preco']);
                // Aplicar desconto se houver
                if(isset($preco_data['desconto_percentual']) && $preco_data['desconto_percentual'] > 0) {
                    $preco_final = $preco_final * (1 - floatval($preco_data['desconto_percentual']) / 100);
                }
            } else {
                // Aplicar desconto geral se houver
                if($desconto_geral > 0) {
                    $preco_final = $preco_final * (1 - $desconto_geral / 100);
                }
            }
        }
        
        $servicos[] = [
            'id' => $servico_id,
            'nome' => $servico['nome'] ?? 'Sem nome',
            'categoria' => $servico['categoria'] ?? 'Geral',
            'preco' => $preco_final,
            'preco_formatado' => number_format($preco_final, 2, ',', '.')
        ];
    }
}

echo json_encode([
    'servicos' => $servicos,
    'total' => $total_servicos,
    'empresa_id' => $empresa_id
]);
?>

