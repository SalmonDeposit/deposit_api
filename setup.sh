#!/bin/bash
set -xe

ENV_FILE="/var/www/html/.env";

if [ ! -f "$ENV_FILE" ]; then
  rm "$ENV_FILE"
  touch "$ENV_FILE"
fi

{
  echo "APP_NAME=deposit"
  echo "APP_ENV=${APP_ENV:-local}";
  echo "APP_KEY=${APP_KEY:-base64:Q0gqyvX+LwEFWOW3QYqHrMT3s6CbbDka6plbeK8o1KU=}";
  echo "APP_DEBUG=${APP_DEBUG:-false}";
  echo "APP_URL=${APP_URL:-http://localhost:5000}";

  echo "FRONTEND_URL=${FRONTEND_URL:-'http://localhost:4200'}";
  echo "SESSION_DOMAIN=${SESSION_DOMAIN:-'localhost'}";
  echo "SANCTUM_STATEFUL_DOMAINS=${SANCTUM_STATEFUL_DOMAINS:-'localhost:4200,localhost:5000'}";

  echo "DB_CONNECTION=${DB_CONNECTION:-mysql}";
  echo "DB_HOST=${DB_HOST:-database}";
  echo "DB_PORT=${DB_PORT:-3306}";
  echo "DB_DATABASE=${DB_NAME:-deposit_db}";
  echo "DB_USERNAME=${DB_USERNAME:-username}";
  echo "DB_PASSWORD=${DB_PASSWORD:-password}";

  echo "AZURE_FUNCTION_ACCESS_TOKEN=${AZURE_FUNCTION_ACCESS_TOKEN:-''}";
  echo "AZURE_STORAGE_CONTAINER=${AZURE_STORAGE_CONTAINER:-''}";
  echo "AZURE_STORAGE_EXTRACT_CONTAINER=${AZURE_STORAGE_EXTRACT_CONTAINER:-''}";
  echo "AZURE_STORAGE_CONNECTION_STRING=${AZURE_STORAGE_CONNECTION_STRING:-''}";

  echo "GOOGLE_CLIENT_ID=${GOOGLE_CLIENT_ID:-''}";
  echo "GOOGLE_CLIENT_SECRET=${GOOGLE_CLIENT_SECRET:-''}";
  echo "GOOGLE_REDIRECT_URI=${GOOGLE_REDIRECT_URI:-''}"

  echo "MAIL_DRIVER=${MAIL_DRIVER:-smtp}";
  echo "MAIL_HOST=${MAIL_HOST:-mailtrap.io}";
  echo "MAIL_PORT=${MAIL_PORT:-2525}";
  echo "MAIL_USERNAME=${MAIL_USERNAME:-null}";
  echo "MAIL_PASSWORD=${MAIL_PASSWORD:-null}";
  echo "MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-null}";

  echo "CACHE_DRIVER=${CACHE_DRIVER:-redis}";
  echo "SESSION_DRIVER=${SESSION_DRIVER:-redis}";
  echo "QUEUE_DRIVER=${QUEUE_DRIVER:-database}";

  echo "REDIS_HOST=${REDIS_HOST:-redis}";
  echo "REDIS_PASSWORD=${REDIS_PASSWORD:-null}";
  echo "REDIS_PORT=${REDIS_PORT:-6379}";

} >> "${ENV_FILE}";

chown "${CODE_OWNER}":"${APP_GROUP}" "${ENV_FILE}"
chmod 777 "${ENV_FILE}"
php artisan config:cache
php artisan key:generate
php artisan migrate:fresh
chmod 777 -R storage
chmod 777 -R bootstrap

apache2-foreground
