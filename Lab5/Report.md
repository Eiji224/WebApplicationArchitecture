# Практика 5 - HTTPS

## Часть A. HTTPS для основного сайта

1. Установка certbot

![installed](screenshots/01-certbot-installed.png)

2. Получение сертификата

![success](screenshots/02-certbot-success.png)

3. Проверка в браузере

![browser](screenshots/03-browser-lock.png)

![redirect](screenshots/04-certificate-info.png)

4. Редирект

![redirect](screenshots/05-redirect.png)

5. Конфиг после certbot

![config](screenshots/06-nginx-ssl-config.png)

## Часть B. HTTPS для API-сервиса

6. Сертификат для api-поддомена

![api-success](screenshots/07-api-certbot.png)

7. Проверка обоих доменов

![both-https](screenshots/08-both-https.png)

## Часть C. Разбор TLS

8. TLS handshake

![handshake](screenshots/09-tls-handshake.png)

9. Цепочка доверия

Браузер проверяет цепочку доверия сверху вниз: начиная с доверенного корневого сертификата в своём хранилище, он криптографически проверяет подпись каждого следующего сертификата. Только если вся цепочка валидна (подписи верны, сроки не истекли, домен совпадает, сертификат не отозван), соединение считается безопасным.

![chain](screenshots/10-chain.png)

10. Сравнение сертификатов

![compare-certs](screenshots/11-compare-certs.png)

Общие характеристики:
- Оба сертификата действительны 90 дней (с 18 марта 2026 по 16 июня 2026)
- Оба домена являются поддоменами зоны eiji.ai-info.ru

Различие - время выпуска, разница в 7 минут

## Часть D. HSTS, кэширование, gzip

11. HSTS

HSTS (HTTP Strict Transport Security) - это заголовок, который предписывает браузеру взаимодействовать с сайтом только по защищённому протоколу HTTPS, автоматически преобразуя все попытки подключения по HTTP в безопасные запросы. Он защищает от атак типа SSL-stripping и перехвата cookie, исключая возможность перехвата данных при первом обращении к сайту или при подмене незащищённых ссылок

![hsts](screenshots/12-hsts.png)

12. Кэширование и gzip

![cache-gzip](screenshots/13-cache-gzip.png)

13. Автообновление

![renew](screenshots/14-renew.png)