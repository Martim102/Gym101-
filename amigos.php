<?php
require_once __DIR__ . "/../config/inicio.php";
$idUtilizador = utilizadorAutenticado();

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';

// Pesquisar utilizadores por nome/email para adicionar
if ($acao === 'pesquisar' && $metodo === 'GET') {
    $termo = '%' . ($_GET['termo'] ?? '') . '%';
    $stmt = $pdo->prepare("SELECT id, nome, email FROM utilizadores 
                            WHERE (nome LIKE ? OR email LIKE ?) AND id != ? LIMIT 15");
    $stmt->execute([$termo, $termo, $idUtilizador]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Enviar pedido de amizade
if ($acao === 'pedir' && $metodo === 'POST') {
    $d = corpoPedido();
    $amigoId = $d['amigo_id'] ?? 0;

    if ($amigoId == $idUtilizador) {
        http_response_code(400);
        echo json_encode(["erro" => "Não podes adicionar-te a ti próprio."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT IGNORE INTO amizades (utilizador_id, amigo_id, estado) VALUES (?, ?, 'pendente')");
    $stmt->execute([$idUtilizador, $amigoId]);
    echo json_encode(["sucesso" => true]);
    exit;
}

// Aceitar pedido de amizade
if ($acao === 'aceitar' && $metodo === 'POST') {
    $d = corpoPedido();
    $deId = $d['de_id'] ?? 0;

    $pdo->prepare("UPDATE amizades SET estado = 'aceite' WHERE utilizador_id = ? AND amigo_id = ?")
        ->execute([$deId, $idUtilizador]);

    // cria também a relação inversa para facilitar as pesquisas
    $stmt = $pdo->prepare("INSERT IGNORE INTO amizades (utilizador_id, amigo_id, estado) VALUES (?, ?, 'aceite')");
    $stmt->execute([$idUtilizador, $deId]);

    echo json_encode(["sucesso" => true]);
    exit;
}

// Recusar / remover
if ($acao === 'remover' && $metodo === 'POST') {
    $d = corpoPedido();
    $outroId = $d['outro_id'] ?? 0;
    $pdo->prepare("DELETE FROM amizades WHERE (utilizador_id = ? AND amigo_id = ?) OR (utilizador_id = ? AND amigo_id = ?)")
        ->execute([$idUtilizador, $outroId, $outroId, $idUtilizador]);
    echo json_encode(["sucesso" => true]);
    exit;
}

// Pedidos pendentes recebidos
if ($acao === 'pedidos' && $metodo === 'GET') {
    $stmt = $pdo->prepare("SELECT a.utilizador_id AS de_id, u.nome, u.email
                            FROM amizades a JOIN utilizadores u ON u.id = a.utilizador_id
                            WHERE a.amigo_id = ? AND a.estado = 'pendente'");
    $stmt->execute([$idUtilizador]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// Lista de amigos aceites
if ($metodo === 'GET') {
    $stmt = $pdo->prepare("SELECT u.id, u.nome, u.email,
                            (SELECT COUNT(*) FROM treinos t WHERE t.utilizador_id = u.id) AS total_treinos
                            FROM amizades a JOIN utilizadores u ON u.id = a.amigo_id
                            WHERE a.utilizador_id = ? AND a.estado = 'aceite'");
    $stmt->execute([$idUtilizador]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

http_response_code(405);
echo json_encode(["erro" => "Pedido inválido."]);
