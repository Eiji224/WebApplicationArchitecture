<?php
require_once 'db.php';

session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
]);
session_start();

function register($pdo, string $username, string $email, string $password): void
{
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare('INSERT INTO users (name, password_hash, email) VALUES (?, ?, ?)');
    $stmt->execute([$username, $hashedPassword, $email]);
    $user_id = $pdo->lastInsertId();

    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $username;
}

$inputErrors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty(trim($_POST['name']))) {
        $inputErrors['name'] = 'Имя пользователя обязательно';
    }

    if (empty(trim($_POST['email']))) {
        $inputErrors['email'] = 'Электронная почта обязательна';
    } elseif (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $inputErrors['email'] = 'Некорректный формат электронной почты';
    }

    if (empty(trim($_POST['password']))) {
        $inputErrors['password'] = 'Пароль обязателен';
    } elseif (strlen($_POST['password']) < 6) {
        $inputErrors['password'] = 'Пароль должен содержать минимум 6 символов';
    }

    if(empty($inputErrors)) {
        $email = $_POST['email'];
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $inputErrors['user_exists'] = 'Пользователь с введённой почтой уже существует';
        } else {
            register($pdo, $_POST['name'], $email, $_POST['password']);

            header('Location: /messages.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="/css/nav.css">
</head>
<body>
    <?php include __DIR__ . '/partials/nav.php'; ?>

    <?php if(!empty($inputErrors)): ?>
        <div class="error-block">
            <strong>Исправьте ошибки:</strong>
            <?php foreach($inputErrors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="form-container">
            <h1>Регистрация</h1>
            <form method="POST" action="register.php">
                <label for="name">Имя</label>
                <input name="name" id="name" placeholder="Имя">

                <label for="email">Email</label>
                <input name="email" id="email" type="email" placeholder="Email">


                <label for="password">Пароль</label>
                <input name="password" id="password" type="password" placeholder="Пароль">

                <div class="form-control-buttons">
                    <button type="submit" style="width: 100%;">Зарегистрироваться</button>
                </div>

                <div class="reg-has-acc">
                    <span>Уже есть аккаунт? </span>
                    <a href="login.php">Войти</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>