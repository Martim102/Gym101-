<?php
// Incluído no topo de todos os ficheiros da API.

session_start();

header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . "/db.php";

function utilizadorAutenticado() {
    if (!isset($_SESSION['utilizador_id'])) {
        http_response_code(401);
        echo json_encode(["erro" => "Sessão inválida. Faz login novamente."]);
        exit;
    }
    return $_SESSION['utilizador_id'];
}

function corpoPedido() {
    return json_decode(file_get_contents("php://input"), true) ?? [];
}
