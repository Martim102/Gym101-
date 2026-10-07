<?php
require_once __DIR__ . "/../config/inicio.php";
$idUtilizador = utilizadorAutenticado();

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';

// Criar um novo desafio e convidar amigos
if ($acao === 'criar' && $metodo === 'POST') {
    $d = corpoPedido();

    $stmt = $pdo->prepare("INSERT INTO desafios (criador_id, nome, descricao, tipo, data_inicio, data_fim)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $idUtilizador,
        $d['nome'] ?? 'Novo desafio',
        $d['descricao'] ?? '',
        $d['tipo'] ?? 'treinos',
        $d['data_inicio'] ?? date('Y-m-d'),
        $d['data_fim'] ?? date('Y-m-d', strtotime('+7 days'))
    ]);
    $desafioId = $pdo->lastInsertId();

    // o criador entra logo como participante
    $pdo->prepare("INSERT INTO desafio_participantes (desafio_id, utilizador_id) VALUES (?, ?)")
        ->execute([$desafioId, $idUtilizador]);

    // convidar amigos selecionados
    if (!empty($d['convidados']) && is_array($d['convidados'])) {
        $stmtConvite = $pdo->prepare("INSERT IGNORE INTO desafio_participantes (desafio_id, utilizador_id) VALUES (?, ?)");
        foreach ($d['convidados'] as $amigoId) {
            $stmtConvite->execute([$desafioId, $amigoId]);
        }
    }

    echo json_encode(["sucesso" => true, "id" => $desafioId]);
    exit;
}

// Entrar num desafio existente
if ($acao === 'entrar' && $metodo === 'POST') {
    $d = corpoPedido();
    $pdo->prepare("INSERT IGNORE INTO desafio_participantes (desafio_id, utilizador_id) VALUES (?, ?)")
        ->execute([$d['desafio_id'] ?? 0, $idUtilizador]);
    echo json_encode(["sucesso" => true]);
    exit;
}

// Classificação (leaderboard) de um desafio
if ($acao === 'classificacao' && $metodo === 'GET') {
    $desafioId = $_GET['desafio_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT u.nome, dp.pontos
                            FROM desafio_participantes dp JOIN utilizadores u ON u.id = dp.utilizador_id
                            WHERE dp.desafio_id = ? ORDER BY dp.pontos DESC");
    $stmt->execute([$desafioId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Lista dos desafios em que o utilizador participa (ativos e passados)
if ($metodo === 'GET') {
    $stmt = $pdo->prepare("SELECT de.id, de.nome, de.descricao, de.tipo, de.data_inicio, de.data_fim,
                            (SELECT COUNT(*) FROM desafio_participantes WHERE desafio_id = de.id) AS n_participantes
                            FROM desafios de JOIN desafio_participantes dp ON dp.desafio_id = de.id
                            WHERE dp.utilizador_id = ? ORDER BY de.data_fim DESC");
    $stmt->execute([$idUtilizador]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

http_response_code(405);
echo json_encode(["erro" => "Pedido inválido."]);
