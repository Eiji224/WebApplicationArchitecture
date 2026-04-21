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

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$inputErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $announcement = $_POST['announcement'];

    if(empty($announcement)) {
        $inputErrors['announcement'] = 'Введите текст поста';
    }

    if(empty($inputErrors)) {
        $stmt = $pdo->prepare('INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)');
        $stmt->execute(['Сообщение', $announcement, $_SESSION['user_id']]);

        header('Location: /messages.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Boardy</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/nav.css">
    <link rel="stylesheet" href="/css/form.css">
</head>
<body>
    <?php include 'partials/nav.php'; ?>
    <main>
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
                <h1>Новый пост</h1>
                <form method="POST" action="submit.php">
                    <label for="announcement">Текст</label>
                    <textarea name="announcement" id="announcement" placeholder="Напишите ваше объявление..."></textarea>
                    <div class="form-control-buttons">
                        <button>Опубликовать</button>
                        <a>Отмена</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>