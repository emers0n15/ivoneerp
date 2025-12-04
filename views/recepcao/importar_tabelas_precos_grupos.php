<?php
/**
 * Importador central das 4 TABELAS DE PREÇOS em grupos:
 *
 * 1) Tabela Fidelidade (base Monte Sinai Seguradoras/Empresas)
 *    - Ficheiro: Clínica Médica Monte Sinai fidelidade para arranjos(Recuperado Automaticamente).csv
 *    - Empresas (grupo Fidelidade):
 *        - Fidelidade            -> usa a mesma tabela, mas com desconto_geral = 10%
 *        - Agência de zambeze
 *        - INSS
 *        - Indico seguros
 *        - Mais Vida
 *        - Oraclemed
 *
 * 2) Tabela Vulcan
 *    - Ficheiro: Lista de preço final Vulcan.atualizada.csv
 *    - Empresas (grupo Vulcan):
 *        - Vulcan
 *        - Med plus
 *        - Momentun
 *
 * 3) Tabela Empresas
 *    - Ficheiro: Clínica Médica Monte Sinai-empresas.csv
 *    - Empresas (grupo Empresas):
 *        - MLT, TTL, Ifs(vulcan, montaengil), Montaengil,
 *          Fulao, Taiana, Vivo consultantes, MedHealth,
 *          Máximo, Salvador construções, Run power,
 *          Henner, Better care, Cohima
 *
 * 4) Tabela Particular
 *    - Ficheiro: Particulares Tabela de precos.csv
 *    - Empresa:
 *        - Particular
 *
 * OBS:
 * - Este script PODE ser executado várias vezes; ele tenta reaproveitar empresas,
 *   tabelas de preços e serviços já existentes.
 * - Os caminhos dos ficheiros estão fixos conforme especificado pelo utilizador.
 */

session_start();
if (!isset($_SESSION['idUsuario'])) {
    header("location:../../");
    exit;
}

if ($_SESSION['categoriaUsuario'] != "recepcao") {
    header("location:../admin/");
    exit;
}

include '../../conexao/index.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$userID = (int)$_SESSION['idUsuario'];

// Caminhos dos ficheiros (Windows)
$PATH_FIDELIDADE  = 'C:\\Users\\LOSE\\Desktop\\Clínica Médica Monte Sinai fidelidade para arranjos(Recuperado Automaticamente).csv';
$PATH_VULCAN      = 'C:\\Users\\LOSE\\Desktop\\Lista de preço final Vulcan.atualizada.csv';
$PATH_EMPRESAS    = 'C:\\Users\\LOSE\\Desktop\\Clínica Médica Monte Sinai-empresas.csv';
$PATH_PARTICULAR  = 'C:\\Users\\LOSE\\Desktop\\Particulares Tabela de precos.csv';

// ========================================================================
// HELPERS DE BANCO / EMPRESA / TABELA
// ========================================================================

function normalizarPrecoPt($str) {
    $str = trim($str);
    if ($str === '' || preg_match('/[A-Za-z#]/', $str)) {
        return false;
    }
    $str = str_replace(['"', "'", ' '], '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace(',', '.', $str);
    if ($str === '' || !is_numeric($str)) {
        return false;
    }
    $v = (float)$str;
    return $v > 0 ? $v : false;
}

function slugCodigo($categoria, $nome, $prefix = 'SV') {
    $base = preg_replace('/[^A-Za-z0-9]/', '', strtoupper(substr($categoria, 0, 4) . substr($nome, 0, 20)));
    if ($base === '') {
        $base = 'GEN';
    }
    return $prefix . '-' . substr($base, 0, 20);
}

function obterOuCriarEmpresa(mysqli $db, $nome, $userID, $descontoGeral = 0.0) {
    $nome = trim($nome);
    if ($nome === '') return null;

    $nomeEsc = mysqli_real_escape_string($db, $nome);
    $sql  = "SELECT id, desconto_geral FROM empresas_seguros WHERE nome = '$nomeEsc' LIMIT 1";
    $rs   = mysqli_query($db, $sql);
    if ($rs && mysqli_num_rows($rs) > 0) {
        $row = mysqli_fetch_assoc($rs);
        $id  = (int)$row['id'];
        // Atualizar desconto_geral se for diferente do desejado
        $descontoAtual = (float)$row['desconto_geral'];
        if (abs($descontoAtual - $descontoGeral) > 0.01) {
            $dg = number_format($descontoGeral, 2, '.', '');
            mysqli_query($db, "UPDATE empresas_seguros SET desconto_geral = $dg WHERE id = $id");
        }
        return $id;
    }

    $dg = number_format($descontoGeral, 2, '.', '');
    $sqlIns = "
        INSERT INTO empresas_seguros (nome, desconto_geral, ativo, usuario_criacao)
        VALUES ('$nomeEsc', $dg, 1, $userID)
    ";
    if (!mysqli_query($db, $sqlIns)) {
        return null;
    }
    return mysqli_insert_id($db);
}

function obterOuCriarTabelaGrupo(mysqli $db, $empresaIdBase, $nomeTabela, $descricao, $userID) {
    $nomeEsc = mysqli_real_escape_string($db, $nomeTabela);
    $sql     = "SELECT id FROM tabelas_precos WHERE empresa_id = $empresaIdBase AND nome = '$nomeEsc' LIMIT 1";
    $rs      = mysqli_query($db, $sql);
    if ($rs && mysqli_num_rows($rs) > 0) {
        $row = mysqli_fetch_assoc($rs);
        return (int)$row['id'];
    }

    $descEsc = mysqli_real_escape_string($db, $descricao);
    $sqlIns  = "
        INSERT INTO tabelas_precos (empresa_id, nome, descricao, ativo, usuario_criacao)
        VALUES ($empresaIdBase, '$nomeEsc', '$descEsc', 1, $userID)
    ";
    if (!mysqli_query($db, $sqlIns)) {
        return null;
    }
    return mysqli_insert_id($db);
}

function vincularEmpresasATabela(mysqli $db, array $empresaIds, $tabelaPrecosId) {
    $tabelaPrecosId = (int)$tabelaPrecosId;
    foreach ($empresaIds as $id) {
        $id = (int)$id;
        mysqli_query($db, "UPDATE empresas_seguros SET tabela_precos_id = $tabelaPrecosId WHERE id = $id");
    }
}

function obterOuCriarServico(mysqli $db, $categoria, $nome, $precoBase, $userID, $codigoPrefixo) {
    $categoria = trim($categoria);
    $nome      = trim($nome);
    if ($nome === '') return null;

    $catEsc  = mysqli_real_escape_string($db, $categoria);
    $nomeEsc = mysqli_real_escape_string($db, $nome);

    // Tentar encontrar serviço pelo par (nome, categoria)
    $sql = "SELECT id, codigo FROM servicos_clinica WHERE nome = '$nomeEsc' AND categoria = '$catEsc' LIMIT 1";
    $rs  = mysqli_query($db, $sql);
    if ($rs && mysqli_num_rows($rs) > 0) {
        $row = mysqli_fetch_assoc($rs);
        $id  = (int)$row['id'];
        // Atualizar preço base para manter coerência
        $preco = number_format($precoBase, 2, '.', '');
        mysqli_query($db, "UPDATE servicos_clinica SET preco = $preco, ativo = 1 WHERE id = $id");
        return $id;
    }

    $codigo = slugCodigo($categoria, $nome, $codigoPrefixo);
    $codigoEsc = mysqli_real_escape_string($db, $codigo);
    $preco     = number_format($precoBase, 2, '.', '');

    $sqlIns = "
        INSERT INTO servicos_clinica (codigo, nome, categoria, preco, ativo, usuario_criacao)
        VALUES ('$codigoEsc', '$nomeEsc', '$catEsc', $preco, 1, $userID)
    ";
    if (!mysqli_query($db, $sqlIns)) {
        // Se for erro de chave duplicada (código já existe), tentar reaproveitar o serviço existente
        if (mysqli_errno($db) == 1062) {
            $sqlFind = "SELECT id FROM servicos_clinica WHERE codigo = '$codigoEsc' LIMIT 1";
            $rsFind  = mysqli_query($db, $sqlFind);
            if ($rsFind && mysqli_num_rows($rsFind) > 0) {
                $row = mysqli_fetch_assoc($rsFind);
                $id  = (int)$row['id'];
                // Atualizar nome/categoria/preço para alinhar com a nova tabela
                mysqli_query($db, "
                    UPDATE servicos_clinica
                    SET nome = '$nomeEsc', categoria = '$catEsc', preco = $preco, ativo = 1
                    WHERE id = $id
                ");
                return $id;
            }
        }
        return null;
    }
    return mysqli_insert_id($db);
}

function gravarPrecoNaTabela(mysqli $db, $tabelaPrecosId, $servicoId, $preco) {
    $tabelaPrecosId = (int)$tabelaPrecosId;
    $servicoId      = (int)$servicoId;
    $preco          = number_format($preco, 2, '.', '');

    // Usar ON DUPLICATE KEY baseado na UNIQUE (tabela_precos_id, servico_id)
    $sql = "
        INSERT INTO tabela_precos_servicos (tabela_precos_id, servico_id, preco, ativo)
        VALUES ($tabelaPrecosId, $servicoId, $preco, 1)
        ON DUPLICATE KEY UPDATE preco = VALUES(preco), ativo = 1
    ";
    return mysqli_query($db, $sql);
}

// ========================================================================
// IMPORTADORES ESPECÍFICOS
// ========================================================================

function importarTabelaFidelidade(mysqli $db, $pathCsv, $userID, &$log) {
    if (!file_exists($pathCsv)) {
        $log['erros'][] = "Ficheiro Fidelidade não encontrado: $pathCsv";
        return;
    }

    // Empresas do grupo Fidelidade
    $empresasNomes = [
        ['Fidelidade',            10.0],
        ['Agência de zambeze',    0.0],
        ['INSS',                  0.0],
        ['Indico seguros',        0.0],
        ['Mais Vida',             0.0],
        ['Oraclemed',             0.0],
    ];

    $empresaIds = [];
    foreach ($empresasNomes as $info) {
        $id = obterOuCriarEmpresa($db, $info[0], $userID, $info[1]);
        if ($id) {
            $empresaIds[] = $id;
        }
    }
    if (empty($empresaIds)) {
        $log['erros'][] = "Nenhuma empresa do grupo Fidelidade pôde ser criada.";
        return;
    }

    // Tabela de preços do grupo (empresa base: primeira)
    $empresaBaseId   = $empresaIds[0];
    $tabelaGrupoId   = obterOuCriarTabelaGrupo($db, $empresaBaseId, 'Tabela Fidelidade/Seguradoras', 'Tabela base Monte Sinai para grupo Fidelidade', $userID);
    if (!$tabelaGrupoId) {
        $log['erros'][] = "Erro ao criar/obter tabela de preços do grupo Fidelidade.";
        return;
    }
    vincularEmpresasATabela($db, $empresaIds, $tabelaGrupoId);

    $handle = fopen($pathCsv, 'r');
    if (!$handle) {
        $log['erros'][] = "Erro ao abrir CSV Fidelidade.";
        return;
    }

    $cabecalho = fgetcsv($handle, 5000, ';'); // 1ª linha
    $idxSector   = null;
    $idxNome     = null;
    $idxPrecoBase= null; // Monte Sinai Seguradoras e empresas

    if (is_array($cabecalho)) {
        foreach ($cabecalho as $i => $col) {
            $colN = mb_strtolower(trim($col), 'UTF-8');
            if (strpos($colN, 'sector') !== false) $idxSector = $i;
            if (strpos($colN, 'nome do procedimento') !== false) $idxNome = $i;
            if (strpos($colN, 'seguradoras e empresas') !== false) $idxPrecoBase = $i;
        }
    }

    if ($idxSector === null || $idxNome === null || $idxPrecoBase === null) {
        $log['erros'][] = "Cabeçalho Fidelidade inesperado. Verifique o ficheiro.";
        fclose($handle);
        return;
    }

    $linhaNum = 1;
    while (($linha = fgetcsv($handle, 8000, ';')) !== false) {
        $linhaNum++;
        if (empty(array_filter($linha))) continue;

        $sector = isset($linha[$idxSector]) ? trim($linha[$idxSector]) : '';
        $nome   = isset($linha[$idxNome]) ? trim($linha[$idxNome]) : '';
        $precoS = isset($linha[$idxPrecoBase]) ? trim($linha[$idxPrecoBase]) : '';

        if ($nome === '' || $precoS === '') continue;

        $preco = normalizarPrecoPt($precoS);
        if ($preco === false) {
            $log['erros'][] = "Fidelidade L$linhaNum: preço inválido ($precoS)";
            continue;
        }

        $servicoId = obterOuCriarServico($db, $sector, $nome, $preco, $userID, 'FID');
        if (!$servicoId) {
            $log['erros'][] = "Fidelidade L$linhaNum: erro ao criar/obter serviço.";
            continue;
        }

        if (gravarPrecoNaTabela($db, $tabelaGrupoId, $servicoId, $preco)) {
            $log['fidelidade']['itens']++;
        } else {
            $log['erros'][] = "Fidelidade L$linhaNum: erro ao gravar preço na tabela.";
        }
    }

    fclose($handle);
}

function importarTabelaVulcan(mysqli $db, $pathCsv, $userID, &$log) {
    if (!file_exists($pathCsv)) {
        $log['erros'][] = "Ficheiro Vulcan não encontrado: $pathCsv";
        return;
    }

    $empresasNomes = ['Vulcan', 'Med plus', 'Momentun'];
    $empresaIds = [];
    foreach ($empresasNomes as $nome) {
        $id = obterOuCriarEmpresa($db, $nome, $userID, 0.0);
        if ($id) $empresaIds[] = $id;
    }
    if (empty($empresaIds)) {
        $log['erros'][] = "Nenhuma empresa do grupo Vulcan pôde ser criada.";
        return;
    }

    $empresaBaseId = $empresaIds[0];
    $tabelaGrupoId = obterOuCriarTabelaGrupo($db, $empresaBaseId, 'Tabela Vulcan', 'Tabela de preços Vulcan/Med Plus/Momentun', $userID);
    if (!$tabelaGrupoId) {
        $log['erros'][] = "Erro ao criar/obter tabela de preços do grupo Vulcan.";
        return;
    }
    vincularEmpresasATabela($db, $empresaIds, $tabelaGrupoId);

    $handle = fopen($pathCsv, 'r');
    if (!$handle) {
        $log['erros'][] = "Erro ao abrir CSV Vulcan.";
        return;
    }

    // Procurar a linha de cabeçalho real (pode não ser a primeira)
    $cabecalho = null;
    for ($i = 0; $i < 5 && !$cabecalho; $i++) {
        $linhaTmp = fgetcsv($handle, 8000, ';');
        if ($linhaTmp === false) break;
        $linhaJoin = mb_strtolower(implode(' ', $linhaTmp), 'UTF-8');
        if (strpos($linhaJoin, 'section') !== false && strpos($linhaJoin, 'provider') !== false) {
            $cabecalho = $linhaTmp;
            break;
        }
    }
    $idxSector = null;
    $idxNome   = null;
    $idxPreco  = null; // REVISED TARIFFS - VULCAN

    if (is_array($cabecalho)) {
        foreach ($cabecalho as $i => $col) {
            $colN = mb_strtolower(trim($col), 'UTF-8');
            if (strpos($colN, 'section') !== false) $idxSector = $i;
            if (strpos($colN, 'provider') !== false) $idxNome = $i;
            if (strpos($colN, 'revised tariffs') !== false || strpos($colN, 'revised tariff') !== false) $idxPreco = $i;
        }
    }

    if ($idxSector === null || $idxNome === null || $idxPreco === null) {
        $log['erros'][] = "Cabeçalho Vulcan inesperado. Verifique o ficheiro.";
        fclose($handle);
        return;
    }

    $linhaNum = 1;
    while (($linha = fgetcsv($handle, 10000, ';')) !== false) {
        $linhaNum++;
        if (empty(array_filter($linha))) continue;

        $sector = isset($linha[$idxSector]) ? trim($linha[$idxSector]) : '';
        $nome   = isset($linha[$idxNome]) ? trim($linha[$idxNome]) : '';
        $precoS = isset($linha[$idxPreco]) ? trim($linha[$idxPreco]) : '';

        if ($nome === '' || $precoS === '') continue;

        $preco = normalizarPrecoPt($precoS);
        if ($preco === false) {
            $log['erros'][] = "Vulcan L$linhaNum: preço inválido ($precoS)";
            continue;
        }

        $servicoId = obterOuCriarServico($db, $sector, $nome, $preco, $userID, 'VUL');
        if (!$servicoId) {
            $log['erros'][] = "Vulcan L$linhaNum: erro ao criar/obter serviço.";
            continue;
        }

        if (gravarPrecoNaTabela($db, $tabelaGrupoId, $servicoId, $preco)) {
            $log['vulcan']['itens']++;
        } else {
            $log['erros'][] = "Vulcan L$linhaNum: erro ao gravar preço na tabela.";
        }
    }

    fclose($handle);
}

function importarTabelaGenericaEmpresasOuParticular(mysqli $db, $pathCsv, $userID, $nomeTabela, $descricaoTabela, $codigoPrefixo, $colunaPrecoAlvo, &$logDestino) {
    if (!file_exists($pathCsv)) {
        $logDestino['erros'][] = "Ficheiro não encontrado: $pathCsv";
        return;
    }

    $handle = fopen($pathCsv, 'r');
    if (!$handle) {
        $logDestino['erros'][] = "Erro ao abrir CSV: $pathCsv";
        return;
    }

    $baseName = strtolower(basename($pathCsv));

    // Tentar encontrar cabeçalho real nas primeiras linhas
    $cabecalho = null;
    for ($i = 0; $i < 5 && !$cabecalho; $i++) {
        $linhaTmp = fgetcsv($handle, 10000, ';');
        if ($linhaTmp === false) break;
        $linhaJoin = mb_strtolower(implode(' ', $linhaTmp), 'UTF-8');
        if (strpos($linhaJoin, 'sector') !== false || strpos($linhaJoin, 'setor') !== false) {
            $cabecalho = $linhaTmp;
            break;
        }
        // Para ficheiros tipo "Particulares"/"Empresas", o cabeçalho começa com "Ordem;Sector;..."
        if (isset($linhaTmp[1]) && mb_strtolower(trim($linhaTmp[1]), 'UTF-8') === 'sector') {
            $cabecalho = $linhaTmp;
            break;
        }
    }
    $idxSector = null;
    $idxNome   = null;
    $idxPreco  = null;

    if (is_array($cabecalho)) {
        foreach ($cabecalho as $i => $colRaw) {
            $col = mb_strtolower(trim($colRaw), 'UTF-8');
            if ($idxSector === null && strpos($col, 'sector') !== false) {
                $idxSector = $i;
            }
            // Nas tabelas de particulares/empresas, há uma coluna vazia entre Ordem e Sector;
            // as descrições estão tipicamente na 3ª coluna (index 2) sem nome claro.
            if ($idxNome === null) {
                if (strpos($col, 'nome do procedimento') !== false) {
                    $idxNome = $i;
                }
            }
            // Procurar a coluna de preço alvo pelo texto passado ($colunaPrecoAlvo)
            if ($idxPreco === null && strpos($col, mb_strtolower($colunaPrecoAlvo, 'UTF-8')) !== false) {
                $idxPreco = $i;
            }
        }
    }

    // Fallbacks específicos por ficheiro, se algum índice não foi detetado
    if ($idxSector === null || $idxNome === null || $idxPreco === null) {
        // Estrutura conhecida dos ficheiros Monte Sinai Empresas/Particulares:
        // Ordem;Sector;[col vazia];Preço referência;Preço Monte Sinai empresas;Preço Monte Sinai particulares;...
        if (strpos($baseName, 'particulares tabela de precos') !== false) {
            $idxSector = 1;
            $idxNome   = 2;
            $idxPreco  = 5; // coluna "Preço Monte Sinai para particulares"
        } elseif (strpos($baseName, 'monte sinai-empresas') !== false) {
            $idxSector = 1;
            $idxNome   = 2;
            $idxPreco  = 4; // coluna "Preço Monte Sinai para empresas"
        }
    }

    // Fallback geral: se ainda não encontrou nome, assumir coluna 2 (índice 2)
    if ($idxNome === null && isset($cabecalho[2])) {
        $idxNome = 2;
    }

    if ($idxSector === null || $idxNome === null || $idxPreco === null) {
        $logDestino['erros'][] = "Cabeçalho inesperado em $pathCsv (Sector/Nome/Preço não encontrados).";
        fclose($handle);
        return;
    }

    $linhaNum = 1;
    while (($linha = fgetcsv($handle, 20000, ';')) !== false) {
        $linhaNum++;
        if (empty(array_filter($linha))) continue;

        $sector = isset($linha[$idxSector]) ? trim($linha[$idxSector]) : '';
        $nome   = isset($linha[$idxNome]) ? trim($linha[$idxNome]) : '';
        $precoS = isset($linha[$idxPreco]) ? trim($linha[$idxPreco]) : '';

        if ($nome === '' || $precoS === '') continue;

        $preco = normalizarPrecoPt($precoS);
        if ($preco === false) {
            $logDestino['erros'][] = "$nomeTabela L$linhaNum: preço inválido ($precoS)";
            continue;
        }

        $servicoId = obterOuCriarServico($db, $sector, $nome, $preco, $userID, $codigoPrefixo);
        if (!$servicoId) {
            $logDestino['erros'][] = "$nomeTabela L$linhaNum: erro ao criar/obter serviço.";
            continue;
        }

        if (gravarPrecoNaTabela($db, $logDestino['tabela_id'], $servicoId, $preco)) {
            $logDestino['itens']++;
        } else {
            $logDestino['erros'][] = "$nomeTabela L$linhaNum: erro ao gravar preço na tabela.";
        }
    }

    fclose($handle);
}

// ========================================================================
// EXECUÇÃO PRINCIPAL
// ========================================================================

$logGlobal = [
    'fidelidade' => ['itens' => 0],
    'vulcan'     => ['itens' => 0],
    'empresas'   => ['itens' => 0, 'tabela_id' => null, 'erros' => []],
    'particular' => ['itens' => 0, 'tabela_id' => null, 'erros' => []],
    'erros'      => []
];

// 1) Grupo FIDELIDADE
importarTabelaFidelidade($db, $PATH_FIDELIDADE, $userID, $logGlobal);

// 2) Grupo VULCAN
importarTabelaVulcan($db, $PATH_VULCAN, $userID, $logGlobal);

// 3) Grupo EMPRESAS (tabela mãe = Monte Sinai Empresas)
$empresasGrupoEmpresas = [
    'MLT',
    'TTL',
    'Ifs (Vulcan, Montaengil)',
    'Montaengil',
    'Fulao',
    'Taiana',
    'Vivo consultantes',
    'MedHealth',
    'Máximo',
    'Salvador construções',
    'Run power',
    'Henner',
    'Better care',
    'Cohima',
];

$empresaIdsEmpresas = [];
foreach ($empresasGrupoEmpresas as $nome) {
    $id = obterOuCriarEmpresa($db, $nome, $userID, 0.0);
    if ($id) $empresaIdsEmpresas[] = $id;
}

if (!empty($empresaIdsEmpresas)) {
    $empresaBaseEmpresas = $empresaIdsEmpresas[0];
    $tabelaEmpresasId = obterOuCriarTabelaGrupo(
        $db,
        $empresaBaseEmpresas,
        'Tabela Empresas',
        'Tabela de preços Monte Sinai para grupo de Empresas',
        $userID
    );
    if ($tabelaEmpresasId) {
        vincularEmpresasATabela($db, $empresaIdsEmpresas, $tabelaEmpresasId);
        $logGlobal['empresas']['tabela_id'] = $tabelaEmpresasId;
        // Importar da coluna "Pre�o  Monte sinai para empresas"
        importarTabelaGenericaEmpresasOuParticular(
            $db,
            $PATH_EMPRESAS,
            $userID,
            'Tabela Empresas',
            'Tabela Monte Sinai Empresas',
            'EMP',
            'preço  monte sinai para empresas',
            $logGlobal['empresas']
        );
    } else {
        $logGlobal['erros'][] = "Erro ao criar tabela de preços do grupo Empresas.";
    }
} else {
    $logGlobal['erros'][] = "Nenhuma empresa do grupo Empresas pôde ser criada.";
}

// 4) Grupo PARTICULAR
$empresaParticularId = obterOuCriarEmpresa($db, 'Particular', $userID, 0.0);
if ($empresaParticularId) {
    $tabelaParticularId = obterOuCriarTabelaGrupo(
        $db,
        $empresaParticularId,
        'Tabela Particular',
        'Tabela de preços Monte Sinai para particulares',
        $userID
    );
    if ($tabelaParticularId) {
        vincularEmpresasATabela($db, [$empresaParticularId], $tabelaParticularId);
        $logGlobal['particular']['tabela_id'] = $tabelaParticularId;
        importarTabelaGenericaEmpresasOuParticular(
            $db,
            $PATH_PARTICULAR,
            $userID,
            'Tabela Particular',
            'Tabela Monte Sinai particulares',
            'PAR',
            'preço  monte sinai para particulares',
            $logGlobal['particular']
        );
    } else {
        $logGlobal['erros'][] = "Erro ao criar tabela de preços do grupo Particular.";
    }
} else {
    $logGlobal['erros'][] = "Empresa 'Particular' não pôde ser criada.";
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Importação das 4 Tabelas de Preços</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .stats-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        .stat-number {
            font-size: 26px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
        .error-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3>Importação das 4 Tabelas de Preços por Grupo</h3>

    <div class="success-box">
        <p><strong>Ficheiros utilizados:</strong></p>
        <ul>
            <li>Fidelidade: <code><?php echo htmlspecialchars($PATH_FIDELIDADE); ?></code></li>
            <li>Vulcan: <code><?php echo htmlspecialchars($PATH_VULCAN); ?></code></li>
            <li>Empresas: <code><?php echo htmlspecialchars($PATH_EMPRESAS); ?></code></li>
            <li>Particular: <code><?php echo htmlspecialchars($PATH_PARTICULAR); ?></code></li>
        </ul>
    </div>

    <div class="stats-box">
        <div class="row">
            <div class="col-md-3 stat-item">
                <div class="stat-number text-primary"><?php echo (int)$logGlobal['fidelidade']['itens']; ?></div>
                <div class="stat-label">Serviços na Tabela Fidelidade/Seguradoras</div>
            </div>
            <div class="col-md-3 stat-item">
                <div class="stat-number text-primary"><?php echo (int)$logGlobal['vulcan']['itens']; ?></div>
                <div class="stat-label">Serviços na Tabela Vulcan</div>
            </div>
            <div class="col-md-3 stat-item">
                <div class="stat-number text-primary"><?php echo (int)$logGlobal['empresas']['itens']; ?></div>
                <div class="stat-label">Serviços na Tabela Empresas</div>
            </div>
            <div class="col-md-3 stat-item">
                <div class="stat-number text-primary"><?php echo (int)$logGlobal['particular']['itens']; ?></div>
                <div class="stat-label">Serviços na Tabela Particular</div>
            </div>
        </div>
    </div>

    <?php
    $todosErros = array_merge(
        $logGlobal['erros'],
        $logGlobal['empresas']['erros'],
        $logGlobal['particular']['erros']
    );
    if (!empty($todosErros)): ?>
        <div class="error-box">
            <h5>Erros / Avisos (<?php echo count($todosErros); ?>)</h5>
            <ul style="max-height: 300px; overflow-y: auto;">
                <?php foreach (array_slice($todosErros, 0, 80) as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
                <?php if (count($todosErros) > 80): ?>
                    <li><em>... e mais <?php echo count($todosErros) - 80; ?> linhas</em></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="servicos_clinica.php" class="btn btn-primary">Ver Serviços</a>
        <a href="empresas.php" class="btn btn-secondary">Ver Empresas</a>
    </div>
</div>
</body>
</html>


