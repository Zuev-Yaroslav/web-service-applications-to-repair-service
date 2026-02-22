#!/bin/bash
read -p "Введите ID request: " REQUEST_ID
read -p "Введите laravel_session: " SESSION_TOKEN

if [ -z "$REQUEST_ID" ]; then
    echo "Ошибка: ID не может быть пустым"
    exit 1
fi

echo "Запускаю тест..."
seq 10 | xargs -I{} -P 5 \
    curl -s -o /dev/null -w "Запрос {}: HTTP %{http_code}\n" \
    -X POST \
    -H "Accept: application/json" \
    -H "Cookie: laravel-session=$SESSION_TOKEN" \
    http://localhost:7354/request-record-panel/$REQUEST_ID/start-work

echo "Тестирование завершено."
