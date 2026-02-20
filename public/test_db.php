<?php 

require_once __DIR__ . '/../app/config.php';

$stmt = $pdo->query("SELECT NOW() AS agora");
$row = $stmt->fetch();

echo "Conexão bem-sucedida! Hora atual do banco de dados: " . $row['agora'];
