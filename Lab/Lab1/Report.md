# Практическая работа №1 - Основы командной строки Linux

## 1. Создание виртуальной машины

1. Параметры виртуальной машины в процессе создания
![Параметры ВМ](screenshots/01-vm-settings.png)
2. Командная строка Ubuntu после установки с приглашением
![Командная строка](screenshots/02-vm-console.png)

## 2. Информация о системе

1. Вывод информации о системе
![Информация о системе](screenshots/03-system-info.png)

## 3. Сеть: IP-адрес и открытые порты

1. Вывод `ip addr show`
![ip addr show](screenshots/04-ip-addr.png)
2. Вывод `sudo ss -tlnp`
![sudo ss -tlnp](screenshots/05-ports.png)

## 4. Сервис SSH

1. Вывод `sudo systemctl status ssh`
![sudo systemctl status ssh](screenshots/06-ssh-status.png)
2. Вывод `sudo ss -tlnp | grep ssh`
![sudo ss -tlnp | grep ssh](screenshots/07-ssh-port.png)

## 5. Пользователи и группы

1. Вывод `grep '/bin/bash' /etc/passwd'`
![users](screenshots/08-users.png)
2. Процесс создания пользователя `boardy`
![new-user](screenshots/09-new-user.png)
3. Вывод `id boardy`
![user-check](screenshots/10-user-check.png)

## 6. Дерево каталогов

1. Вывод `ls -la /`
![ls -la /](screenshots/11-root-tree.png)
2. Вывод `ls -la ~`
![ls -la ~](screenshots/12-home-tree.png)

## 7. Права доступа

1. Вывод `ls -ld / /etc /var/ tmp /home`
![permissions](screenshots/13-permissions.png)
2. Три состояния testfile.txt
[chmod](screenshots/14-chmod.png)

## 8. Установленные пакеты и сервисы

1. Вывод `dpkg -l | grep -E 'openssh|python|git|curl|vim|nano`
![packages](screenshots/15-packages.png)
2. Вывод `systemctl list-units --type=service --state=running`
![services](screenshots/16-services.png)

## 9. Конвейер и перенаправление

1. Вывод топ-10 процессов по памяти
![top-processes](screenshots/17-top-processes.png)
2. Вывод процессов по пользователям
![processes-count](screenshots/18-process-count.png)
3. Вывод топ-10 больших файлов в /var
![big-files](screenshots/19-big-files.png)

## 10. Итоговый файл

1. Вывод всех файлов с отчётами
![report-files](screenshots/20-report-files.png)