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

function GetMessages($pdo): array
{
    $stmt = $pdo->query(
            'SELECT p.id, p.body, p.created_at, u.name AS author_name
           FROM posts AS p
           JOIN users AS u ON p.author_id = u.id
          ORDER BY p.created_at DESC'
    );

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $now = time();
    foreach ($messages as &$msg) {
        $diff = $now - strtotime($msg['created_at']);

        if ($diff < 60) {
            $msg['created_at'] = 'только что';
        } elseif ($diff < 3600) {
            $msg['created_at'] = floor($diff / 60) . ' минут назад';
        } elseif ($diff < 86400) {
            $msg['created_at'] = floor($diff / 3600) . ' часов назад';
        } else {
            $msg['created_at'] = floor($diff / 86400) . ' дней назад';
        }
    }
    unset($msg);

    return $messages;
}

if (empty($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$messages = GetMessages($pdo);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Boardy — Сообщения</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/nav.css">
    <link rel="stylesheet" href="/css/posts.css">
</head>
<body>
    <?php include 'partials/nav.php'; ?>
    <main>
        <div class="posts-container">
            <h2>Все посты</h2>
            <?php if (empty($messages)): ?>
                <p>Постов пока нет</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="post-card">
                        <div class="post-meta">
                            <h3><?= htmlspecialchars($msg['author_name']) ?></h3>
                            <span><?= htmlspecialchars($msg['created_at']) ?></span>
                        </div>
                        <p><?= htmlspecialchars($msg['body']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
