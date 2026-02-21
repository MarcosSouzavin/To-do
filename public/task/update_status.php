<?php
require __DIR__ . '/../../app/config.php';
require __DIR__ . '/../../app/auth.php';

require_login();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo 'Método não permitido.';
  exit;
}


$id = (int)($_POST['id'] ?? 0);
$status = (string)($_POST['status'] ?? '');


if ($id <= 0) {
  http_response_code(400);
  echo 'ID inválido.';
  exit;
}


$allowed = ['fazendo', 'finalizado'];
if (!in_array($status, $allowed, true)) {
  http_response_code(400);
  echo 'Status inválido.';
  exit;
}


$stmt = $pdo->prepare("
  UPDATE tasks
  SET status = ?, updated_at = NOW()
  WHERE id = ? AND user_id = ?
");
$stmt->execute([$status, $id, current_user_id()]);


header('Location: index.php');
exit;