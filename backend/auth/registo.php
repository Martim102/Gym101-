<?php
require_once __DIR__ . "/../config/inicio.php";

$dados = corpoPedido();

$nome = trim($dados['nome'] ?? '');
$email = trim(strtolower($dados['email'] ?? ''));
$password = $dados['password'] ?? '';

if ($nome === '' || $email === '' || $password === '') {
    http_response_code(400);
    echo json_encode(["erro" => "Preenche todos os campos."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["erro" => "O email introduzido não é válido."]);
    exit;
}

if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(["erro" => "A palavra-passe tem de ter pelo menos 6 caracteres."]);
    exit;
}

$verifica = $pdo->prepare("SELECT id FROM utilizadores WHERE email = ?");
$verifica->execute([$email]);
if ($verifica->fetch()) {
    http_response_code(409);
    echo json_encode(["erro" => "Já existe uma conta registada com este email."]);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$inserir = $pdo->prepare("INSERT INTO utilizadores (nome, email, password_hash) VALUES (?, ?, ?)");
$inserir->execute([$nome, $email, $hash]);

$novoId = $pdo->lastInsertId();

// cria já a linha de metas nutricionais por defeito
$pdo->prepare("INSERT INTO metas_nutricionais (utilizador_id) VALUES (?)")->execute([$novoId]);

$_SESSION['utilizador_id'] = $novoId;
$_SESSION['utilizador_nome'] = $nome;

echo json_encode(["sucesso" => true, "nome" => $nome]);
