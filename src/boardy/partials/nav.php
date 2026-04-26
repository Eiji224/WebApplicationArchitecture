<?php
$is_logged = !empty($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
?>
<nav>
    <div>
        <a href="/" class="brand">Boardy</a>
        <a href="/messages.php" class="nav-button-main">Все посты</a>
        <?php if ($is_logged) : ?>
            <a href="/submit.php" class="nav-button-main">Добавить пост</a>
        <?php endif; ?>
    </div>

    <div>
        <?php if ($is_logged): ?>
            <span>Привет, <?= htmlspecialchars($user_name) ?>!</span>
            <a href="/logout.php" class="nav-button-secondary">Выйти</a>
        <?php else: ?>
            <a href="/login.php" class="nav-button-secondary">Вход</a>
            <a href="/register.php" class="nav-button-secondary">Регистрация</a>
        <?php endif; ?>
    </div>
</nav>
