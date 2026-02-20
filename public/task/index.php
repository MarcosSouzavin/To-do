<?php
require __DIR__ . '/../../app/auth.php';
require __DIR__ . '/../../app/config.php';  
require_login();

$stmt = $pdo->prepare("
SELECT id, title, description, created_at FROM tasks WHERE user_id = ? ORDER BY created_at DESC"); 

$stmt->execute([current_user_id()]);

$tasks = $stmt->fetchAll();
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Minhas Tarefas</title>
</head>
<body>
    <h1>Minhas Tarefas</h1>
    
    <p><a href="create.php">Criar nova tarefa</a> | <a href="../logout.php">Sair</a></p>
    
    <?php if (count($tasks) === 0): ?>
        <p>Você ainda não tem tarefas.</p>
    <?php else: ?>
        <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
            <strong><?php echo htmlspecialchars($task['title']); ?></strong><br>
            <?php echo nl2br(htmlspecialchars($task['description'])); ?><br>
            <small>Criada em: <?php echo date('d/m/Y H:i', strtotime($task['created_at'])); ?></small><br>
            <a href="edit.php?id=<?php echo $task['id']; ?>">Editar</a> | 
            <a href="delete.php?id=<?php echo $task['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');">Excluir</a>
            </li>
            <hr>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>