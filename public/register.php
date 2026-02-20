<?php
require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/auth.php';

start_session();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($name === '' || $email === '' || $password === '') {
    $error = 'Todos os campos são obrigatórios.';
  } elseif (strlen($password) < 6) {
    $error = 'A senha deve ter pelo menos 6 caracteres.';
  } else {

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
      $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      $stmt->execute([$name, $email, $hash]);

      $_SESSION['user_id'] = (int)$pdo->lastInsertId();

      header('Location: /To-do/public/task/index.php');
      exit;

    }  catch (PDOException $e) {
  if ($e->getCode() === '23000') {

    $msg = $e->getMessage();

    if (strpos($msg, "for key 'users_email_unique'") !== false || strpos($msg, "for key 'email'") !== false) {
      $error = 'Email já cadastrado.';
    } elseif (strpos($msg, "for key 'users_name_unique'") !== false || strpos($msg, "for key 'name'") !== false) {
      $error = 'Nome já cadastrado.';
    } else {
      $error = 'Já existe um registro com esses dados.';
    }

  } else {
    throw $e;
  }
}
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Registro</title>
</head>
<body>
  <h1>Criar conta</h1>

  <?php if ($error !== ''): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Nome</label><br>
    <input name="name" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Senha</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Cadastrar</button>
  </form>

  <p>Já tem conta? <a href="/To-do/public/login.php">Entrar</a></p>
</body>
</html>