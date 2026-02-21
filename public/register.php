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

            $_SESSION['user_id'] = (int) $pdo->lastInsertId();

            header('Location: /To-do/public/task/index.php');
            exit;

        } catch (PDOException $e) {
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
    <link rel="stylesheet" href="/To-do/public/assets/css/core.css">
    <link rel="stylesheet" href="/To-do/public/assets/css/auth.css">
</head>

<body>
    <div class="container auth-wrap">

        <div class="card auth-card">
            <div class="card-body">

                <div class="auth-top">
                    <div>
                        <h1>Criar conta</h1>
                        <p class="sub">Sua Central de Tarefas</p>
                    </div>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="notice error"><?php echo htmlspecialchars($error); ?></div>
                    <br>
                <?php endif; ?>

                <form method="post">

                    <div class="field">
                        <label>Nome</label>
                        <input type="text" name="name" required>
                    </div>

                    <div class="field">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>

                    <div class="field">
                        <label>Senha</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="stack">
                        <button class="btn btn-primary" type="submit">Cadastrar</button>
                    </div>

                </form>

                <div class="auth-footer">
                    Já tem conta? <a href="login.php">Entrar</a>
                </div>

            </div>
        </div>

    </div>
</body>

</html>