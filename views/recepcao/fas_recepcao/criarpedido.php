<?php
session_start();
include_once '../../../conexao/index.php';
error_reporting(E_ALL);
date_default_timezone_set('Africa/Maputo');

$userID = $_SESSION['idUsuario'] ?? null;
$paciente_id = $_POST['paciente'] ?? null;
$empresa_id = $_POST['empresa_id'] ?? null;
$prazo = $_POST['prazo'] ?? '';
$metodo = $_POST['metodo'] ?? '';
$condicoes = $_POST['condicoes'] ?? '';
$apolice = $_POST['apolice'] ?? '';
$codigo1 = $_POST['codigo1'] ?? '';
$codigo2 = $_POST['codigo2'] ?? '';
$codigo3 = $_POST['codigo3'] ?? '';

if(!$paciente_id || !$userID) {
    echo 1; // Parâmetros não enviados
    exit;
}

// Buscar serviços da tabela temporária (filtrar por empresa se houver)
if($empresa_id && $empresa_id > 0) {
    $sql_temp = "SELECT * FROM fa_servicos_temp WHERE user = ? AND empresa_id = ?";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "ii", $userID, $empresa_id);
} else {
    $sql_temp = "SELECT * FROM fa_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
    $stmt = mysqli_prepare($db, $sql_temp);
    mysqli_stmt_bind_param($stmt, "i", $userID);
}
mysqli_stmt_execute($stmt);
$rs_temp = mysqli_stmt_get_result($stmt);

if(!$rs_temp || mysqli_num_rows($rs_temp) == 0) {
    echo 1; // Nenhum serviço selecionado
    exit;
}

// Calcular subtotal e total
$subtotal = 0;
$iva_total = 0;
while($servico_temp = mysqli_fetch_array($rs_temp)) {
    $subtotal += floatval($servico_temp['total']);
    // IVA pode ser calculado depois se necessário
}

$desconto = 0; // Desconto pode ser adicionado depois
$total = $subtotal - $desconto;
$iva_incluso = $iva_total; // IVA para serviços (geralmente 0 ou pode ser configurado)

// Buscar série fiscal (similar à farmácia)
$sql_serie = "SELECT MAX(ano_fiscal) as serie FROM serie_factura";
$rs_serie = mysqli_query($db, $sql_serie);
$serie = date('Y'); // Ano atual como padrão
if($rs_serie && mysqli_num_rows($rs_serie) > 0) {
    $dados_serie = mysqli_fetch_array($rs_serie);
    $serie = $dados_serie['serie'] ?? date('Y');
}

$year = date('Y');
$data_hora = date("Y-m-d H:i:s");
$data = date("Y-m-d");

// Verificar se o ano corresponde à série (só permite o ano atual)
if ($year == $serie) {
    // Buscar próximo número de documento
    $sql_max = "SELECT MAX(n_doc) as maxid FROM factura_recepcao WHERE serie = ?";
    $stmt_max = mysqli_prepare($db, $sql_max);
    mysqli_stmt_bind_param($stmt_max, "i", $serie);
    mysqli_stmt_execute($stmt_max);
    $rs_max = mysqli_stmt_get_result($stmt_max);
    
    $new_id = 1;
    if($rs_max && mysqli_num_rows($rs_max) > 0) {
        $dados_max = mysqli_fetch_array($rs_max);
        $max_id = $dados_max['maxid'] ?? 0;
        $new_id = $max_id + 1;
    }
    
    // Inserir fatura na tabela factura_recepcao (seguindo lógica da farmácia)
    $sql_fatura = "INSERT INTO factura_recepcao SET 
        n_doc = ?, 
        descricao = ?, 
        valor = ?, 
        iva = ?, 
        disconto = ?, 
        serie = ?, 
        prazo = ?, 
        metodo = ?, 
        condicoes = ?, 
        apolice = ?, 
        codigo1 = ?, 
        codigo2 = ?, 
        codigo3 = ?, 
        paciente = ?, 
        empresa_id = ?, 
        usuario = ?, 
        dataa = ?";
    
    $stmt_fatura = mysqli_prepare($db, $sql_fatura);
    $empresa_id_db = $empresa_id ? intval($empresa_id) : null;
    $apolice_db = $apolice ? $apolice : '';
    $codigo1_db = $codigo1 ? $codigo1 : '';
    $codigo2_db = $codigo2 ? $codigo2 : '';
    $codigo3_db = $codigo3 ? $codigo3 : '';
    
    // Para empresa_id NULL, usar 0 ou tratar separadamente
    if($empresa_id_db === null) {
        // Se empresa_id é NULL, usar 0 ou criar query separada
        $empresa_id_db = 0;
    }
    
    // Garantir tipos corretos
    $new_id = intval($new_id);
    $serie = intval($serie);
    $paciente_id = intval($paciente_id);
    $empresa_id_db = intval($empresa_id_db);
    $userID = intval($userID);
    $total = floatval($total);
    $iva_incluso = floatval($iva_incluso);
    $desconto = floatval($desconto);
    
    // Tipos: i(n_doc), s(descricao), d(valor), d(iva), d(disconto), i(serie), 
    //        s(prazo), s(metodo), s(condicoes), s(apolice), s(codigo1), s(codigo2), 
    //        s(codigo3), i(paciente), i(empresa_id), i(usuario), s(dataa)
    // Total: 17 parâmetros = "isdddisssssssiiis"
    mysqli_stmt_bind_param($stmt_fatura, "isdddisssssssiiis", 
        $new_id,           // i
        $data_hora,         // s
        $total,             // d
        $iva_incluso,       // d
        $desconto,          // d
        $serie,             // i
        $prazo,             // s
        $metodo,            // s
        $condicoes,         // s
        $apolice_db,        // s
        $codigo1_db,        // s
        $codigo2_db,        // s
        $codigo3_db,        // s
        $paciente_id,       // i
        $empresa_id_db,     // i
        $userID,            // i
        $data               // s
    );
    
    // Verificar se a tabela existe
    $check_table = "SHOW TABLES LIKE 'factura_recepcao'";
    $rs_check = mysqli_query($db, $check_table);
    if(!$rs_check || mysqli_num_rows($rs_check) == 0) {
        error_log("Tabela factura_recepcao não existe. Execute o script SQL para criar as tabelas.");
        echo 4; // Tabela não existe
        exit;
    }
    
    if(mysqli_stmt_execute($stmt_fatura)) {
        $fatura_id = mysqli_insert_id($db);
        
        if(!$fatura_id) {
            error_log("Erro ao obter ID da fatura inserida: " . mysqli_error($db));
            echo 2; // Erro ao criar fatura
            exit;
        }
        
        // Verificar se a tabela de serviços existe
        $check_table_serv = "SHOW TABLES LIKE 'fa_servicos_fact_recepcao'";
        $rs_check_serv = mysqli_query($db, $check_table_serv);
        if(!$rs_check_serv || mysqli_num_rows($rs_check_serv) == 0) {
            error_log("Tabela fa_servicos_fact_recepcao não existe. Execute o script SQL para criar as tabelas.");
            // Reverter a inserção da fatura
            $sql_delete_fatura = "DELETE FROM factura_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_fatura);
            mysqli_stmt_bind_param($stmt_del, "i", $fatura_id);
            mysqli_stmt_execute($stmt_del);
            echo 4; // Tabela não existe
            exit;
        }
        
        // Inserir serviços na tabela fa_servicos_fact_recepcao (seguindo lógica da farmácia)
        mysqli_data_seek($rs_temp, 0);
        $erro_servicos = false;
        while($servico_temp = mysqli_fetch_array($rs_temp)) {
            $servico_id = intval($servico_temp['servico']);
            $qtd = intval($servico_temp['qtd']);
            $preco = floatval($servico_temp['preco']);
            $total_item = floatval($servico_temp['total']);
            $iva_item = 0; // IVA para serviços (pode ser configurado depois)
            
            $sql_item = "INSERT INTO fa_servicos_fact_recepcao (servico, qtd, preco, iva, total, user, factura) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_item = mysqli_prepare($db, $sql_item);
            if(!$stmt_item) {
                error_log("Erro ao preparar statement para inserir serviço: " . mysqli_error($db));
                $erro_servicos = true;
                break;
            }
            mysqli_stmt_bind_param($stmt_item, "iidddii", $servico_id, $qtd, $preco, $iva_item, $total_item, $userID, $fatura_id);
            if(!mysqli_stmt_execute($stmt_item)) {
                error_log("Erro ao executar inserção de serviço: " . mysqli_error($db));
                $erro_servicos = true;
                break;
            }
        }
        
        if($erro_servicos) {
            // Reverter a inserção da fatura
            $sql_delete_fatura = "DELETE FROM factura_recepcao WHERE id = ?";
            $stmt_del = mysqli_prepare($db, $sql_delete_fatura);
            mysqli_stmt_bind_param($stmt_del, "i", $fatura_id);
            mysqli_stmt_execute($stmt_del);
            echo 2; // Erro ao criar fatura
            exit;
        }
        
        // Verificar se o caixa está aberto (para pagamentos imediatos)
        if($metodo && $metodo != 'fatura_empresa') {
            $data_hoje = date('Y-m-d');
            $sql_caixa = "SELECT * FROM caixa_recepcao WHERE data = ? AND status = 'aberto'";
            $stmt_caixa = mysqli_prepare($db, $sql_caixa);
            mysqli_stmt_bind_param($stmt_caixa, "s", $data_hoje);
            mysqli_stmt_execute($stmt_caixa);
            $rs_caixa = mysqli_stmt_get_result($stmt_caixa);
            
            if(!$rs_caixa || mysqli_num_rows($rs_caixa) == 0) {
                // Verificar se já existe caixa fechado para hoje
                $sql_check = "SELECT * FROM caixa_recepcao WHERE data = ?";
                $stmt_check = mysqli_prepare($db, $sql_check);
                mysqli_stmt_bind_param($stmt_check, "s", $data_hoje);
                mysqli_stmt_execute($stmt_check);
                $rs_check = mysqli_stmt_get_result($stmt_check);
                
                if(!$rs_check || mysqli_num_rows($rs_check) == 0) {
                    // Criar caixa se não existir
                    $sql_criar_caixa = "INSERT INTO caixa_recepcao (data, status, usuario_abertura, data_abertura) 
                                       VALUES (?, 'aberto', ?, NOW())";
                    $stmt_criar = mysqli_prepare($db, $sql_criar_caixa);
                    mysqli_stmt_bind_param($stmt_criar, "si", $data_hoje, $userID);
                    mysqli_stmt_execute($stmt_criar);
                } else {
                    // Reabrir caixa se estiver fechado
                    $sql_reabrir = "UPDATE caixa_recepcao SET status = 'aberto' WHERE data = ?";
                    $stmt_reabrir = mysqli_prepare($db, $sql_reabrir);
                    mysqli_stmt_bind_param($stmt_reabrir, "s", $data_hoje);
                    mysqli_stmt_execute($stmt_reabrir);
                }
            }
        }
        
        // Limpar tabela temporária
        if($empresa_id && $empresa_id > 0) {
            $sql_delete = "DELETE FROM fa_servicos_temp WHERE user = ? AND empresa_id = ?";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "ii", $userID, $empresa_id);
        } else {
            $sql_delete = "DELETE FROM fa_servicos_temp WHERE user = ? AND (empresa_id IS NULL OR empresa_id = 0)";
            $stmt_delete = mysqli_prepare($db, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, "i", $userID);
        }
        mysqli_stmt_execute($stmt_delete);
        
        echo $fatura_id;
    } else {
        error_log("Erro ao executar inserção de fatura: " . mysqli_error($db));
        echo 2; // Erro ao criar fatura
    }
} else {
    // Ano não corresponde à série - retornar mensagem específica
    echo 3; // Ano fiscal não corresponde ao ano atual
}
?>

