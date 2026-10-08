<?php
require_once __DIR__ . "/../config/inicio.php";

$_SESSION = [];
session_destroy();

echo json_encode(["sucesso" => true]);
