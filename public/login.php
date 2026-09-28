<?php
require_once __DIR__ . '/../includes/functions.php';

// Если уже вошли — на главную


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $error = 'Заполните все поля';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "SELECT id, name, surname, lastname, username, password, role
             FROM users WHERE username = ?"
        );
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        $ok = false;
        if ($user) {
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $ok = true;
            }
        }

        if ($ok) {
            unset($user['password']);
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $user['role'];
            if($_SESSION['role'] == 'dispatcher'){
                header('Location: ../dispatcher/index.php');
            }
            if($_SESSION['role'] == 'executor'){
                header('Location: ../executor/index.php');
            }
            exit;
        }
        $error = 'Неверный логин или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>НОВОМЕТ — Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<main class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <img src="img/logo.png" alt="НОВОМЕТ" class="logo-img">
        </div>

        <?php if ($error): ?>
            <div class="warning-text" style="margin-bottom:16px;">
                <span class="icon-warn">⚠</span> <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="login">Логин</label>
                <input type="text" id="login" name="login" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn-login">Войти</button>
        </form>
    </div>
</main>
</body>
</html>