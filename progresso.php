<?php
require_once __DIR__ . "/../config/inicio.php";
$idUtilizador = utilizadorAutenticado();

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $stmt = $pdo->prepare("SELECT id, data_registo, peso, gordura_corporal, musculo, notas 
                            FROM progresso WHERE utilizador_id = ? ORDER BY data_registo ASC");
    $stmt->execute([$idUtilizador]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($metodo === 'POST') {
    $dados = corpoPedido();
    $data = $dados['data_registo'] ?? date('Y-m-d');
    $peso = $dados['peso'] ?? null;
    $gordura = $dados['gordura_corporal'] ?? null;
    $musculo = $dados['musculo'] ?? null;
    $notas = $dados['notas'] ?? null;

    if (!$peso) {
        http_response_code(400);
        echo json_encode(["erro" => "Indica pelo menos o peso."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO progresso (utilizador_id, data_registo, peso, gordura_corporal, musculo, notas)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$idUtilizador, $data, $peso, $gordura, $musculo, $notas]);

    echo json_encode(["sucesso" => true, "id" => $pdo->lastInsertId()]);
    exit;
}

if ($metodo === 'DELETE') {
    $dados = corpoPedido();
    $id = $dados['id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM progresso WHERE id = ? AND utilizador_id = ?");
    $stmt->execute([$id, $idUtilizador]);
    echo json_encode(["sucesso" => true]);
    exit;
}

http_response_code(405);
echo json_encode(["erro" => "Método não permitido."]);
