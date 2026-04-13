# Практика 8: MySQL

## Часть A. MySQL - установка и настройка

1. Установка MySQL

![mysql-status](screenshots/01-mysql-status.png)

2. База данных и пользователь

![db-charset](screenshots/02-db-charset.png)

- Мы выбираем **utf8mb4**, так как в этой кодировке содержатся все Unicode-символы, а это все языки + эмодзи. **Utf8** содержит только BMP: буквы, цифры, знаки
- **Collation** - это правила сравнения и сортировки символов
- **unicode_ci** - это правила точного сравнения по Unicode-правилам

3. phpMyAdmin

![phpmyadmin](screenshots/03-phpmyadmin.png)

## Часть B. Таблицы и связи

4. Три таблицы

![tables-cli](screenshots/04-tables-cli.png)

![tables-pma](screenshots/05-tables-pma.png)

- **FOREIGN KEY** - это инструкция, говорящая, что в столбце будет использоваться значение из другой таблицы
- **ON DELETE CASCADE** - это инструкция, говорящая, что запись необходимо удалить, если удалится запись из другой таблицы, связанной через **FOREIGN KEY**
- Мы используем движок **InnoDB**, т.к. в нём встроенны транзакции, внешние ключи, а также строковые блокировки. В другом движке **MyISAM** таких свойств нет

5. SQL-скрипт

![schema-sql](screenshots/06-schema-sql.png)

## Часть C. SQL - базовые операции

6. INSERT

![data-cli](screenshots/07-data-cli.png)

![data-pma](screenshots/08-data-pma.png)

7. SELECT + JOIN

![join](screenshots/09-join.png)

8. Foreign Key - защита целостности

![fk-error](screenshots/10-fk-error.png)

9. CASCADE

![cascade](screenshots/11-cascade.png)

10. SQL-инъекция

![injection](screenshots/12-injection.png)

- SQL-инъекция работает так, что злоумышленник вводит специальные символы в форму, которые «ломают» структуру SQL-запроса и заставляют базу данных выполнить чужой код
- prepared statement защищает, потому что заранее компилирует структуру запроса, а данные передаются отдельно и экранируются автоматически

## Часть D. PHP + MySQL

11. db.php

![db-php](screenshots/13-db-php.png)

12. submit.php через MySQL

![submit](screenshots/14-submit.png)

![submit-pma](screenshots/15-submit-pma.png)

13. messages.php через MySQL

![messages](screenshots/16-messages.png)

## Часть E. FastAPI + MySQL

14. aiomysql

![api-messages](screenshots/17-api-messages.png)

![api-users](screenshots/18-api-users.png)

- aiomysql - это асинхронный драйвер MySQL
  - await — не блокирует event loop при запросе к БД
  - Обычный mysql-connector заблокировал бы, как `time.sleep`


# Pull Request

![pr](screenshots/19-pull-request.png)