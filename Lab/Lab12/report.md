# Отчет по лабораторной работе №12

## Часть A: Установка и переключение домена

1. Composer и PHP-расширение

![composer-php](screenshots/01-composer-php.png)

2. Переезд папок

![folders](screenshots/02-folders.png)

![laravel-ver](screenshots/03-laravel-version.png)

3. Структура Laravel

- public — единственная папка, которую видит nginx. Указание корня проекта вместо public/ открывает публичный доступ к конфигурационным файлам (например, .env с паролями от базы данных), логам и исходному коду, что позволяет любому пользователю прочитать конфиденциальные данные;
- app — где пишете код (Models, Controllers, Policies);
- routes/web.php — описание URL → контроллер;
- resources/views/ — Blade-шаблоны;
- database/migrations/ — миграции. database/seeders/ — тестовые данные;
- .env — секреты;
- .env.example — шаблон без секретов (коммитим)


4. Nginx-конфиг

- На этой лабораторной работе я переехал из vk.cloud на wsl, поэтому конфиг nginx адаптирован под wsl

![nginx-conf](screenshots/04-nginx-config.png)

![laravel welcome](screenshots/05-laravel-welcome.png)

- Директива последовательно проверяет наличие физического файла или папки по указанному адресу, а если их нет - перенаправляет запрос в `index.php`. При заходе на `/posts/3` без этой строки Nginx не найдет соответствующий файл в папке `public` и сразу вернет ошибку 404, не передавая управление роутеру Laravel

## Часть B: БД, миграции, сидер

5. Создание БД boardy_main

![databases](screenshots/06-databases.png)

- У старой boardy схема под чистый PHP: `password_hash` вместо `password`, может быть `username` вместо `name`
- Подгонять под Laravel-конвенции дороже, чем создать с нуля

6. Подключение Laravel к БД

![pdo](screenshots/07-tinker-pdo.png)

7. Миграции posts и comments

![migrate](screenshots/08-migrate-status.png)

![tables](screenshots/09-show-tables.png)

8. Модели со связями

![model-relations](screenshots/10-model-relations.png)

9. Сидер

![seed](screenshots/11-seed-counts.png)


## Часть С: CRUD постов и комментариев

10. Маршруты

![routes](screenshots/12-route-list.png)

11. Лента постов

![post-index](screenshots/13-posts-index.png)

12. Страница поста с комментариями

![post-show](screenshots/14-post-show.png)

13. Создание поста

![post-create](screenshots/15-post-create.png)

![post-created](screenshots/16-post-after-create.png)

14. Policy и редактирование

![edit-own](screenshots/17-edit-own.png)

![403](screenshots/18-edit-foreign-403.png)

- Строчек кода на чистом PHP было намного больше. Каждый раз нам приходилось проверять $_SESSION, лезть в базу по session_id. В laravel это 1 строка в контроллере. Вся остальная работа с cookie скрыта под капотом в laravel.

15. Удаление поста

![post deleted](screenshots/19-post-deleted.png)

16. Комментарий через Blade

![comment created](screenshots/20-comment-created.png)

### Часть D. Breeze + Socialite

17. Установка Breeze

![register](screenshots/21-register.png)

![login](screenshots/22-login.png)

18. Регистрация и вход

![after_reg](screenshots/23-after-register.png)

19. GitHub OAuth-приложение

![github app](screenshots/24-github-app.png)

20. Socialite

![login github](screenshots/25-login-with-github.png)

21. Полный OAuth flow

![github authorize](screenshots/26-github-authorize.png)

![after_github_login](screenshots/27-after-github-login.png)

![mysql](screenshots/28-mysql-github-id.png)

В нативном php приходится парсить все данные вручную. В Laravel Socialite всё это скрыто внутри библиотеки
- Нативный php:
   - вручную формировать URL для редиректа
   - использовать curl для общения с GitHub API,
- Lab12 (Socialite): 10–15 строк в контроллере

- Что сократилось?
   - Больше не нужно самим формировать запрос к GitHub и вручную парсить ответ
   - Socialite автоматически обрабатывает неудачные попытки авторизации
   - Фреймворк сам генерирует и проверяет параметр state в сессии
- Laravel Socialite инкапсулирует всю логику протокола OAuth 2.0 в методы `Socialite::driver('github')->redirect()` и `Socialite::driver('github')->user()`

### Часть E. Архитектурные вопросы

22. Что осталось от прошлых практик
У вас на VPS лежат /var/www/boardy-legacy/ (старый PHP) и БД boardy. Зачем мы их не удалили? Что произойдёт, если попробовать открыть https://фамилия.ai-info.ru/login.php (старый PHP-логин)?
    1. Старые проекты в реальной разработке оставляют по нескольким причинам:
       - Можно обратиться в старую БД и перенести оттуда пользователей или другую важную информацию
       - Если вдруг в новом проекте мы обнаружим критический баг, то можно вернуться к старой версии
    2. При попытке открыть https://boardy.eiji.ai-info.ru/login.php мы получим 404 ошибку.
       - В конфиге nginx мы указали root /var/www/boardy/public. Веб-сервер ищет файлы только внутри этой папки, а файла login.php там нет. Nginx вообще не знает о существовании /var/www/boardy-legacy, если не указать это в конфигурации.

23. FastAPI и React
FastAPI продолжает работать на api.фамилия.ai-info.ru, а React-файлы лежат в Lab9–11. Но в Laravel-проекте мы их не используем. Почему сейчас не используем — что мешает интегрировать? Где они нам пригодятся в Lab13?
    1. Почему не используем старые файлы?
       - Laravel это SSR, а React - CSR. Чтобы их объединить, нам нужно встраивать React компоненты в Blade-шаблоны и настраивать API Laravel (Sanctum), либо полностью переходить на API-интерфейс, отказываясь от Blade
       - React настроен на работу с JWT-токенами через заголовок Authorization, а в laravel мы используем сессии и куки. Так что придется настраивать единый центр авторизации (laravel - поставщик токеном для FastApi), чтоб они могли узнавать одного и того же пользователя

    2. Где пригодятся в Lab13?
       - Пусть в lab13 будут изучаться WebSocket-ы. В таком случае будет оправдано следующее разделение:
         - Laravel продолжит выполнять основные задачи сервиса: авторизация, CRUD постов.
         - React - front, который объединяет Laravel и FastAPI и сможет одновременно отображать данные из Laravel и мгновенно обновлять их при получении сигналов от FastAPI без перезагрузки страницы
24. Реалтайм
Сейчас комментарии появляются только после F5. Какое архитектурное решение нам нужно, чтобы один пользователь видел новый комментарий другого без перезагрузки? Какие два сервера-кандидата для этого решения и почему именно они?

- Архитектурное решение: событийно-ориентированная архитектура с использованием WebSockets
- Для реализации такой архитектуры нам нужны:
    1. FastAPI(Python)
       - Почему: FastAPI асинхронен. Он может удерживать тысячи открытых соединений одновременно, потребляя минимум оперативной памяти. Он будет держать все WebSocket подключения
       - Роль: Laravel записывает комментарий в базу и уведомляет FastAPI (через Redis), а FastAPI рассылает это событие всем подключенным React-клиентам
    2. Laravel Reverb
       - Специализированный WebSocket-сервер для экосистемы Laravel
       - Он позволяет держать тысячи соединений, обеспечивая минимальную задержку при передаче данных между Laravel и React