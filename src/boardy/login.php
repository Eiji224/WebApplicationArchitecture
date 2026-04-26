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

$inputErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty(trim($_POST['email']))) {
        $inputErrors['email'] = 'Введите электронную почту';
    } elseif (!filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $inputErrors['email'] = 'Некорректный формат электронной почты';
    }

    if (empty(trim($_POST['password']))) {
        $inputErrors['password'] = 'Введите пароль';
    }

    if (empty($inputErrors)) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare('SELECT id, name, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $inputErrors['not_found'] = 'Пользователь с введёнными данными не найден';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

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
	<title>Вход</title>
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
            <h1>Вход</h1>
            <form method="POST" action="login.php">
                <label for="email">Email</label>
                <input name="email" id="email" type="email" placeholder="Email">


                <label for="password">Пароль</label>
                <input name="password" id="password" type="password" placeholder="Пароль">

                <div class="form-control-buttons">
                    <button type="submit" style="width: 100%;">Войти</button>
                    <span>или</span>
                    <a class="github-login" href="/oauth-github.php">
                        <img src="public/github-icon.svg" alt="github" />
                        Войти через GitHub
                    </a>
                </div>

                <div class="reg-has-acc">
                    <span>Нет аккаунта? </span>
                    <a href="register.php">Регистрация</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>