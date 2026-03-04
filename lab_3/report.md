# Практика 3. NGINX, DNS

## Часть A. NGINX

1. Установка NGINX
![nginx_status](screenshots/01-nginx-status.png)

2. Страница по IP
![browser-ip](screenshots/02-browser-ip.png)

3. curl
![curl](screenshots/03-curl.png)

4. Директория и права
![permissions](screenshots/04-permissions.png)

5. Конфигурация Nginx
   - **listen** - Какой порт необходимо слушать серверу
   - **root** - Директория с файлами, которые должен отдавать сервер
   - **server_name** - Имя хоста (домена), которое потом сопоставляется с заголовком из HTTP запроса (можно разместить несколько сайтов на одном IP адресе)
   - **index** - название файла(-ов), которые nginx отдаёт по умолчанию

## Часть B. DNS

6. DNS-зона
![dns-zone](screenshots/05-dns-zone.png)

7. A-запись
![a-record](screenshots/06-a-record.png)

8. ping
![ping](screenshots/07-ping.png)

9. dig
![dig](screenshots/08-dig.png)

10. dig +trace
![dig-trace](screenshots/09-dig-trace.png)

11. Сайт по домену
![domain](screenshots/10-browser-domain.png)