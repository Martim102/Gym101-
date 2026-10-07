<?php
// Liga à base de dados MySQL do XAMPP.
// Se mudares a password do MySQL no teu XAMPP, atualiza aqui.

$host = "localhost";
$dbname = "gym100";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(["erro" => "Não foi possível ligar à base de dados."]));
}
