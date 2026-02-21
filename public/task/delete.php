<?php
require __DIR__ . '/../../app/config.php';
require __DIR__ . '/../../app/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        echo 'ID inválido.';
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, current_user_id()]);

    header('Location: index.php');
    exit;
} else {
    http_response_code(405);
    echo 'Método não permitido.';
    exit;
}