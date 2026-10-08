<?php
require_once __DIR__ . "/../config/inicio.php";
$idUtilizador = utilizadorAutenticado();

$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? 'refeicoes';

// ---- METAS NUTRICIONAIS ----
if ($acao === 'metas') {
    if ($metodo === 'GET') {
        $stmt = $pdo->prepare("SELECT * FROM metas_nutricionais WHERE utilizador_id = ?");
        $stmt->execute([$idUtilizador]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        exit;
    }
    if ($metodo === 'POST') {
        $d = corpoPedido();
        $stmt = $pdo->prepare("UPDATE metas_nutricionais SET calorias_meta=?, proteina_meta=?, hidratos_meta=?, gordura_meta=? WHERE utilizador_id=?");
        $stmt->execute([
            $d['calorias_meta'] ?? 2000,
            $d['proteina_meta'] ?? 150,
            $d['hidratos_meta'] ?? 200,
            $d['gordura_meta'] ?? 60,
            $idUtilizador
        ]);
        echo json_encode(["sucesso" => true]);
        exit;
    }
}

// ---- REFEIÇÕES ----
if ($metodo === 'GET') {
    $data = $_GET['data'] ?? date('Y-m-d');
    $stmt = $pdo->prepare("SELECT id, nome, tipo, data_refeicao, calorias, proteina_g, hidratos_g, gordura_g
                            FROM refeicoes WHERE utilizador_id = ? AND data_refeicao = ? ORDER BY id ASC");
    $stmt->execute([$idUtilizador, $data]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($metodo === 'POST') {
    $d = corpoPedido();
    $stmt = $pdo->prepare("INSERT INTO refeicoes (utilizador_id, nome, tipo, data_refeicao, calorias, proteina_g, hidratos_g, gordura_g)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $idUtilizador,
        $d['nome'] ?? 'Refeição',
        $d['tipo'] ?? 'almoco',
        $d['data_refeicao'] ?? date('Y-m-d'),
        $d['calorias'] ?? 0,
        $d['proteina_g'] ?? 0,
        $d['hidratos_g'] ?? 0,
        $d['gordura_g'] ?? 0
    ]);
    echo json_encode(["sucesso" => true, "id" => $pdo->lastInsertId()]);
    exit;
}

if ($metodo === 'DELETE') {
    $d = corpoPedido();
    $stmt = $pdo->prepare("DELETE FROM refeicoes WHERE id = ? AND utilizador_id = ?");
    $stmt->execute([$d['id'] ?? 0, $idUtilizador]);
    echo json_encode(["sucesso" => true]);
    exit;
}

http_response_code(405);
echo json_encode(["erro" => "Método não permitido."]);
