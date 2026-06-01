<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        <h3 class="mt-3">Авторизация в Boardy...</h3>
        <p class="text-muted">Пожалуйста, подождите, мы перенаправляем вас на главную страницу.</p>
    </div>
</div>

<script type="module">
    import { handleCallback } from "{{ asset('js/auth.js') }}";

    const redirectUrl = sessionStorage.getItem('redirectUrl');

    document.addEventListener("DOMContentLoaded", function() {
        handleCallback().then(token => {
            if (token) {
                console.log('Успешно авторизован, токен получен!');
            } else {
                console.error('Не удалось получить токен');
            }

            sessionStorage.setItem('token', token)
            console.log(redirectUrl)

            sessionStorage.removeItem('redirectUrl');
            window.location.href = redirectUrl;
        }).catch(err => {
            console.error('Критическая ошибка коллбэка:', err);
            sessionStorage.removeItem('redirectUrl');
            window.location.href = redirectUrl;
        });
    });
</script>
</body>
</html>
