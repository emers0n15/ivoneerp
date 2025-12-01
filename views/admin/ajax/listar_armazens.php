<?php
session_start();
include_once '../../../conexao/index.php';

// Definir a codificação UTF-8 para garantir que os caracteres especiais sejam tratados corretamente
mysqli_set_charset($db, "utf8");

if (!isset($_SESSION['idUsuario'])) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado']);
    exit;
}

// Configuração para DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? mysqli_real_escape_string($db, $_POST['search']['value']) : '';

// Consulta para contar o total de registros
$sqlCount = "SELECT COUNT(*) as total FROM armazem";
$resultCount = mysqli_query($db, $sqlCount);
$rowCount = mysqli_fetch_assoc($resultCount);
$totalRecords = $rowCount['total'];

// Consulta para contar registros filtrados
$sqlFilteredCount = "SELECT COUNT(*) as total FROM armazem";
if (!empty($search)) {
    $sqlFilteredCount .= " WHERE nome LIKE '%$search%' OR endereco LIKE '%$search%' OR telefone LIKE '%$search%'";
}
$resultFilteredCount = mysqli_query($db, $sqlFilteredCount);
$rowFilteredCount = mysqli_fetch_assoc($resultFilteredCount);
$totalRecordsFiltered = $rowFilteredCount['total'];

// Consulta principal para obter os dados
$sql = "SELECT a.*, u.nome as responsavel_nome 
        FROM armazem a
        LEFT JOIN users u ON a.responsavel = u.id";

if (!empty($search)) {
    $sql .= " WHERE a.nome LIKE '%$search%' OR a.endereco LIKE '%$search%' OR a.telefone LIKE '%$search%'";
}

$sql .= " ORDER BY a.id DESC LIMIT $start, $length";

$resultado = mysqli_query($db, $sql);

$data = [];
if ($resultado && mysqli_num_rows($resultado) > 0) {
    while ($armazem = mysqli_fetch_assoc($resultado)) {
        $estado_badge = $armazem['estado'] == 'ativo' ? 
            '<span class="badge badge-success">Ativo</span>' : 
            '<span class="badge badge-danger">Inativo</span>';
        
        $acoes = '<div class="dropdown dropdown-action">
                    <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="armazem_detalhes.php?id='.$armazem['id'].'"><i class="fa fa-eye m-r-5"></i> Detalhes</a>
                        <a class="dropdown-item editarArmazem" href="#" data-id="'.$armazem['id'].'"><i class="fa fa-pencil m-r-5"></i> Editar</a>
                        <a class="dropdown-item gerenciarStockArmazem" href="#" data-id="'.$armazem['id'].'" data-nome="'.$armazem['nome'].'"><i class="fa fa-cubes m-r-5"></i> Gerenciar Stock</a>';
        
        if ($armazem['estado'] == 'ativo') {
            $acoes .= '<a class="dropdown-item alterarEstadoArmazem" href="#" data-id="'.$armazem['id'].'" data-estado="inativo"><i class="fa fa-ban m-r-5"></i> Inativar</a>';
        } else {
            $acoes .= '<a class="dropdown-item alterarEstadoArmazem" href="#" data-id="'.$armazem['id'].'" data-estado="ativo"><i class="fa fa-check m-r-5"></i> Ativar</a>';
        }
        
        $acoes .= '</div></div>';
        
        $data[] = [
            $armazem['id'],
            $armazem['nome'],
            $armazem['endereco'] ?? '',
            $armazem['telefone'] ?? '',
            $armazem['responsavel_nome'] ?? 'Não definido',
            $estado_badge,
            $acoes
        ];
    }
}

// Formatação da resposta para o DataTables
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFiltered,
    "data" => $data
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>
