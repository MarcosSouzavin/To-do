<?php
require __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/auth.php';

start_session();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    $error = 'Preenche email e senha.';
  } else {

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
      $error = 'Email ou senha inválidos.';
    } else {

      $_SESSION['user_id'] = (int)$user['id'];

      header('Location: /To-do/public/task/index.php');
      exit;
    }
  }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Login</title>
</head>
<body>
  <h1>Login</h1>

  <?php if ($error !== ''): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>

  <form method="post">
    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Senha</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Entrar</button>
  </form>

  <p>Não tem conta? <a href="/To-do/public/register.php">Criar agora</a></p>
</body>
</html>