<?php
require_once __DIR__ . "/../config/inicio.php";
$idUtilizador = utilizadorAutenticado();

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $stmt = $pdo->prepare("SELECT id, nome, tipo, data_treino, duracao_minutos, calorias_queimadas, notas
                            FROM treinos WHERE utilizador_id = ? ORDER BY data_treino DESC");
    $stmt->execute([$idUtilizador]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

if ($metodo === 'POST') {
    $d = corpoPedido();
    $stmt = $pdo->prepare("INSERT INTO treinos (utilizador_id, nome, tipo, data_treino, duracao_minutos, calorias_queimadas, notas)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $idUtilizador,
        $d['nome'] ?? 'Treino',
        $d['tipo'] ?? null,
        $d['data_treino'] ?? date('Y-m-d'),
        $d['duracao_minutos'] ?? null,
        $d['calorias_queimadas'] ?? null,
        $d['notas'] ?? null
    ]);

    // se o utilizador estiver a participar em desafios do tipo "treinos" a decorrer, soma pontos
    $pdo->prepare("UPDATE desafio_participantes dp
                    JOIN desafios de ON de.id = dp.desafio_id
                    SET dp.pontos = dp.pontos + 1
                    WHERE dp.utilizador_id = ? AND de.tipo = 'treinos'
                    AND CURDATE() BETWEEN de.data_inicio AND de.data_fim")
        ->execute([$idUtilizador]);

    echo json_encode(["sucesso" => true, "id" => $pdo->lastInsertId()]);
    exit;
}

if ($metodo === 'DELETE') {
    $d = corpoPedido();
    $stmt = $pdo->prepare("DELETE FROM treinos WHERE id = ? AND utilizador_id = ?");
    $stmt->execute([$d['id'] ?? 0, $idUtilizador]);
    echo json_encode(["sucesso" => true]);
    exit;
}

http_response_code(405);
echo json_encode(["erro" => "Método não permitido."]);
