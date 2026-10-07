<?php
require_once __DIR__ . "/../config/inicio.php";

if (isset($_SESSION['utilizador_id'])) {
    echo json_encode([
        "autenticado" => true,
        "id" => $_SESSION['utilizador_id'],
        "nome" => $_SESSION['utilizador_nome']
    ]);
} else {
    echo json_encode(["autenticado" => false]);
}
