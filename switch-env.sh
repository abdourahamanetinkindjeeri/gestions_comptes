#!/bin/bash

ENV_FILE=$1

if [ -z "$ENV_FILE" ]; then
  echo "❌ Aucun fichier .env spécifié. Exemple : ./switch-env.sh production"
  exit 1
fi

echo "🔄 Chargement de .env.$ENV_FILE..."
cp .env.$ENV_FILE .env

echo "🧹 Rechargement de la configuration Laravel..."
php artisan config:clear
php artisan config:cache

echo "✅ Environnement '$ENV_FILE' chargé avec succès."
