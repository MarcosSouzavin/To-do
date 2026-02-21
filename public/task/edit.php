<?php
require __DIR__ . '/../../app/config.php';
require __DIR__ . '/../../app/auth.php';

require_login();

$error = '';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  http_response_code(400);
  echo 'ID inválido.';
  exit;
}


$stmt = $pdo->prepare("
  SELECT id, title, description, priority, due_date, status
  FROM tasks
  WHERE id = ? AND user_id = ?
");
$stmt->execute([$id, current_user_id()]);
$task = $stmt->fetch();

if (!$task) {
  http_response_code(404);
  echo 'Tarefa não encontrada.';
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $priority = (string)($_POST['priority'] ?? 'media');
  $due_date = $_POST['due_date'] ?? null;
  $status = (string)($_POST['status'] ?? 'pendente');

  
  $force_status = (string)($_POST['force_status'] ?? '');
  if ($force_status === 'finalizado') {
    $status = 'finalizado';
  }

  if ($title === '') {
    $error = 'Título é obrigatório.';
  } else {
    if ($description === '') $description = null;
    if ($due_date === '') $due_date = null;

 
    if (!empty($due_date)) {
      $today = date('Y-m-d');
      if ($due_date < $today) {
        $status = 'finalizado';
      }
    }

   
    $allowedPriority = ['baixa', 'media', 'alta'];
    if (!in_array($priority, $allowedPriority, true)) $priority = 'media';


    $allowedStatus = ['pendente', 'fazendo', 'finalizado'];
    if (!in_array($status, $allowedStatus, true)) $status = 'pendente';


    $stmt = $pdo->prepare("
      UPDATE tasks
      SET title = ?, description = ?, priority = ?, due_date = ?, status = ?, updated_at = NOW()
      WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([
      $title,
      $description,
      $priority,
      $due_date,
      $status,
      $id,
      current_user_id()
    ]);

    header('Location: index.php');
    exit;
  }

  $task['title'] = $title;
  $task['description'] = $description;
  $task['priority'] = $priority;
  $task['due_date'] = $due_date;
  $task['status'] = $status;
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>Editar tarefa</title>
    <link rel="stylesheet" href="/To-do/public/assets/css/core.css">
    <link rel="stylesheet" href="/To-do/public/assets/css/tasks.css">
</head>

<body>
    <div class="container">

        <div class="header">
            <div>
                <h1>Editar tarefa</h1>
                <p class="sub">Ajuste sua missão com precisão 🎯</p>
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
                        <input type="text" name="title" value="<?php echo htmlspecialchars((string)$task['title']); ?>"
                            required>
                    </div>

                    <div class="field">
                        <label>Descrição</label>
                        <textarea
                            name="description"><?php echo htmlspecialchars((string)($task['description'] ?? '')); ?></textarea>
                    </div>

                    <div class="field">
                        <label>Prioridade</label>
                        <select name="priority">
                            <option value="baixa" <?php echo $task['priority']==='baixa'?'selected':''; ?>>Baixa
                            </option>
                            <option value="media" <?php echo $task['priority']==='media'?'selected':''; ?>>Média
                            </option>
                            <option value="alta" <?php echo $task['priority']==='alta'?'selected':''; ?>>Alta</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="pendente" <?php echo $task['status']==='pendente'?'selected':''; ?>>Pendente
                            </option>
                            <option value="fazendo" <?php echo $task['status']==='fazendo'?'selected':''; ?>>Fazendo
                            </option>
                            <option value="finalizado" <?php echo $task['status']==='finalizado'?'selected':''; ?>>
                                Finalizado</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Prazo</label>
                        <input type="date" name="due_date" min="2000-01-01" max="2100-12-31"
                            value="<?php echo htmlspecialchars((string)($task['due_date'] ?? '')); ?>">
                    </div>

                    <div class="stack">
                        <button class="btn btn-primary" type="submit">Salvar</button>
                        <button class="btn btn-success" type="submit" name="force_status" value="finalizado">
                            Finalizar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</body>

</html>