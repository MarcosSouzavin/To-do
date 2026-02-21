<?php
require __DIR__ . '/../../app/config.php';
require __DIR__ . '/../../app/auth.php';

require_login();


$stmt = $pdo->prepare("
  UPDATE tasks
  SET status = 'finalizado', updated_at = NOW()
  WHERE user_id = ?
    AND due_date IS NOT NULL
    AND due_date < CURDATE()
    AND status <> 'finalizado'
");
$stmt->execute([current_user_id()]);


$stmt = $pdo->prepare("
  SELECT id, title, description, status, priority, due_date, created_at
  FROM tasks
  WHERE user_id = ?
  ORDER BY created_at DESC
");
$stmt->execute([current_user_id()]);
$tasks = $stmt->fetchAll();


$pendentes = [];
$finalizadas = [];

foreach ($tasks as $t) {
    if ($t['status'] === 'pendente') {
        $pendentes[] = $t;
    } elseif ($t['status'] === 'finalizado') {
        $finalizadas[] = $t;
    }
}

function h($v): string
{
    return htmlspecialchars((string) $v);
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Minhas Tarefas</title>
    <link rel="stylesheet" href="/To-do/public/assets/css/core.css">
    <link rel="stylesheet" href="/To-do/public/assets/css/tasks.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Minhas Tarefas</h1>
                <p class="sub">Organizado e produtivo</p>
            </div>

            <div class="stack">
                <a class="btn btn-primary" href="create.php">+ Nova tarefa</a>
                <a class="btn" href="../logout.php">Sair</a>
            </div>
        </div>

        <div class="card">

            <section class="section">
                <div class="section-head">
                    <p class="section-title">
                        Pendentes <span class="pill pill-pendente"><?php echo count($pendentes); ?></span>
                    </p>
                </div>

                <?php if (count($pendentes) === 0): ?>
                    <div class="notice">Nenhuma pendência por aqui.</div>
                <?php else: ?>
                    <ul class="list">
                        <?php foreach ($pendentes as $task): ?>
                            <li class="task">
                                <div class="task-top">
                                    <div>
                                        <p class="task-title"><?php echo h($task['title']); ?></p>
                                        <p class="task-desc"><?php echo h($task['description'] ?? ''); ?></p>
                                    </div>

                                    <div class="task-actions">
                                        <a class="btn" href="edit.php?id=<?php echo (int) $task['id']; ?>">Editar</a>

                                        <form method="post" action="delete.php" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo (int) $task['id']; ?>">
                                            <button class="btn btn-danger" type="submit"
                                                onclick="return confirm('Excluir essa tarefa?');">Excluir</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="task-meta">
                                    <span>Prioridade: <?php echo h($task['priority']); ?></span>
                                    <?php if (!empty($task['due_date'])): ?>
                                        <span>Prazo: <?php echo h($task['due_date']); ?></span>
                                    <?php endif; ?>
                                    <span>Criada: <?php echo h($task['created_at']); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="section">
                <div class="section-head">
                    <p class="section-title">
                        Finalizadas <span class="pill pill-finalizado"><?php echo count($finalizadas); ?></span>
                    </p>
                </div>

                <?php if (count($finalizadas) === 0): ?>
                    <div class="notice">Ainda nada finalizado. Bora produzir 😈</div>
                <?php else: ?>
                    <ul class="list">
                        <?php foreach ($finalizadas as $task): ?>
                            <li class="task">
                                <div class="task-top">
                                    <div>
                                        <p class="task-title"><?php echo h($task['title']); ?></p>
                                        <p class="task-desc"><?php echo h($task['description'] ?? ''); ?></p>
                                    </div>

                                    <div class="task-actions">
                                        <a class="btn" href="edit.php?id=<?php echo (int) $task['id']; ?>">Editar</a>

                                        <form method="post" action="delete.php" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo (int) $task['id']; ?>">
                                            <button class="btn btn-danger" type="submit"
                                                onclick="return confirm('Excluir essa tarefa?');">Excluir</button>
                                        </form>
                                    </div>
                                </div>

                                <div class="task-meta">
                                    <span>Prioridade: <?php echo h($task['priority']); ?></span>
                                    <?php if (!empty($task['due_date'])): ?>
                                        <span>Prazo: <?php echo h($task['due_date']); ?></span>
                                    <?php endif; ?>
                                    <span>Criada: <?php echo h($task['created_at']); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

        </div>
    </div>
</body>

</html>