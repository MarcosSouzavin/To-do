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

            $_SESSION['user_id'] = (int) $user['id'];

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
    <link rel="stylesheet" href="/To-do/public/assets/css/core.css">
    <link rel="stylesheet" href="/To-do/public/assets/css/auth.css">
</head>

<body>
    <div class="container auth-wrap">

        <div class="card auth-card">
            <div class="card-body">

                <div class="auth-top">
                    <div>
                        <h1>Login</h1>
                        <p class="sub">Acesse sua central de tarefas</p>
                    </div>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
                    <br>
                <?php endif; ?>

                <form method="post">

                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="field">
                        <label>Senha</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="stack">
                        <button class="btn btn-primary" type="submit">Entrar</button>
                    </div>

                </form>

                <div class="auth-footer">
                    Não tem conta? <a href="register.php">Criar agora</a>
                </div>

            </div>
        </div>

    </div>
</body>

</html>