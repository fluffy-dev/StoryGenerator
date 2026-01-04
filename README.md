# AI Story Generator

Сервис генерации детских сказок на базе OpenAI (GPT), FastAPI и Yii2. Реализован как микросервисная архитектура с потоковой передачей данных.

## Требования

- Docker и Docker Compose
- API ключ OpenAI

## Установка и запуск

1. Клонируйте репозиторий.

2. Создайте файл конфигурации:
cp .env.example .env

3. Откройте файл .env и укажите ваш ключ:
OPENAI_API_KEY=sk-ваш-ключ

4. Запустите проект:
docker compose up -d --build

После запуска:
- Frontend (Yii2): http://localhost:8080
- Backend API (Swagger): http://localhost:8000/docs

## Структура проекта

- story_generator_py/ - Микросервис на Python (FastAPI). Отвечает за генерацию текста через OpenAI.
- yii2-app/ - Фронтенд на PHP (Yii2). Проксирует запросы, отображает поток (SSE) и сохраняет историю.
- docker-compose.yml - Оркестрация контейнеров (Python, PHP, Postgres).

## Основные команды

Остановка контейнеров:
docker compose down

Пересборка после изменений в коде:
docker compose up -d --build

Просмотр логов (Python):
docker logs -f story_gen_python

Просмотр логов (PHP):
docker logs -f story_gen_php

## Работа с базой данных

Миграции накатываются автоматически при старте PHP контейнера.
Данные сохраняются в PostgreSQL (volume: story_gen_db_data).

## Переменные окружения (.env)

Основные настройки:
- OPENAI_API_KEY - Ключ API OpenAI
- OPENAI_MODEL - Модель (по умолчанию gpt-4o-mini)
- POSTGRES_* - Доступы к базе данных

## Особенности реализации

1. Python сервис отдает ответ потоком (StreamingResponse).
2. PHP сервис читает поток и транслирует его клиенту через Server-Sent Events (SSE).
3. Автоматическое удаление пустых инпутов на фронтенде.
4. Логирование Python в формате JSON для продакшена.
5. Глобальный перехват ошибок.