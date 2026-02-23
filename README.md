<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Инструкция развёртывния проекта


- Создать пустую папку для клонирования проекта
- Прописать в терминале в той же папке `https://github.com/Zuev-Yaroslav/web-service-applications-to-repair-service.git` (убедитесь, что на вашем ПК установлен GIT)
- Создать дубликат файла .env.example и переименовать на .env
- В .env указать:
  * пароль {DB_PASSWORD}
  * базу данных {DB_DATABASE}
  * порт {DB_PORT}
  * пароль пользователей для Seed {USER_PASSWORD}
  * {NGINX_PORT} - порт nginx для docker-compose
  * {VITE_PORT} - порт npm run dev

- Запускаем докер:
```` bash
docker-compose up -d
````

- Чтобы зайти в repair_service_app контейнер:
```` bash
docker exec -it repair_service_app bash
````

- Там запускаем скрипт `./_docker/deploy.sh`
- Потом запускаем seed `php artisan migrate --seed`

Можно пользоваться

## Тестовые пользователи (email) пароль берётся в .env
- kkeebler@example.org
- hlockman@example.net
- njaskolski@example.com
- mzboncak@example.org
- ihomenick@example.com

## Проверка на "гонку"

- убедитесь, что контейнеры запущены
- Запускаем `./race_test.sh` в корневой папке проект в терминале Linux
- Следуйте указанием. laravel-session нужно достать на запущенном сайте. Надо сначала авторизоваться потом зайти в Инструменты разработчика -> Приложение -> Файлы Cookie -> <Наш домен>. Скопировать значение cookie laravel-session.

