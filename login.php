<?php
require_once __DIR__ . "/../config/inicio.php";

$dados = corpoPedido();
$email = trim(strtolower($dados['email'] ?? ''));
$password = $dados['password'] ?? '';

$stmt = $pdo->prepare("SELECT id, nome, password_hash FROM utilizadores WHERE email = ?");
$stmt->execute([$email]);
$utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$utilizador || !password_verify($password, $utilizador['password_hash'])) {
    http_response_code(401);
    echo json_encode(["erro" => "Email ou palavra-passe incorretos."]);
    exit;
}

$_SESSION['utilizador_id'] = $utilizador['id'];
$_SESSION['utilizador_nome'] = $utilizador['nome'];

echo json_encode(["sucesso" => true, "nome" => $utilizador['nome']]);
