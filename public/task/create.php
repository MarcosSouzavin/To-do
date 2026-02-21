<?php
require __DIR__ . '/../../app/config.php';
require __DIR__ . '/../../app/auth.php';

require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = (string) ($_POST['priority'] ?? 'media');
    $due_date = $_POST['due_date'] ?? null;

    if ($title === '') {
        $error = 'É obrigatório ter título.';
    } else {
        if ($description === '')
            $description = null;
        if ($due_date === '')
            $due_date = null;

        $allowed = ['baixa', 'media', 'alta'];
        if (!in_array($priority, $allowed, true))
            $priority = 'media';

        $stmt = $pdo->prepare("
      INSERT INTO tasks (user_id, title, description, priority, due_date)
      VALUES (?, ?, ?, ?, ?)
    ");
        $stmt->execute([
            current_user_id(),
            $title,
            $description,
            $priority,
            $due_date
        ]);

        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Criar Tarefa</title>
    <link rel="stylesheet" href="/To-do/public/assets/css/core.css">
    <link rel="stylesheet" href="/To-do/public/assets/css/tasks.css">
</head>

<body>
    <div class="container">

        <div class="header">
            <div>
                <h1>Criar nova tarefa</h1>
                <p class="sub">Organize sua próxima missão 🚀</p>
            </div>
            <a class="btn btn-ghost" href="index.php">Voltar</a>
        </div>

        <div class="card">
            <div class="card-body">

                <?php if ($error !== ''): ?>
                    <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
                    <br>
                <?php endif; ?>

                <form method="post">

                    <div class="field">
                        <label>Título</label>
                        <input type="text" name="title" required>
                    </div>

                    <div class="field">
                        <label>Descrição</label>
                        <textarea name="description"></textarea>
                    </div>

                    <div class="field">
                        <label>Prioridade</label>
                        <select name="priority">
                            <option value="baixa">Baixa</option>
                            <option value="media" selected>Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Data de vencimento</label>
                        <input type="date" name="due_date" min="2026-02-20" max="2100-12-31"
                            value="<?php echo htmlspecialchars((string) ($task['due_date'] ?? '')); ?>">
                    </div>

                    <div class="stack">
                        <button class="btn btn-primary" type="submit">Criar tarefa</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</body>

</html>