#!/bin/bash
# ─────────────────────────────────────────────────────────────────────────────
# SWS Mexico ERP — Setup inicial
# Ejecutar en el VPS o localmente con PHP 8.2+, Composer, Node 18+
# ─────────────────────────────────────────────────────────────────────────────

set -e

echo "▶ Creando proyecto Laravel..."
composer create-project laravel/laravel . --prefer-dist

echo "▶ Instalando dependencias PHP..."
composer require \
    laravel/breeze \
    spatie/laravel-permission \
    laravel/horizon \
    predis/predis \
    phpmailer/phpmailer \
    phpcfdi/credentials \
    phpcfdi/sat-ws-descarga-masiva

echo "▶ Instalando Breeze con Inertia + Vue 3..."
php artisan breeze:install vue --ssr

echo "▶ Instalando dependencias Node..."
npm install

echo "▶ Instalando paquetes Vue adicionales..."
npm install \
    @inertiajs/vue3 \
    pinia \
    @vueuse/core \
    vite-plugin-pwa \
    workbox-window \
    @headlessui/vue \
    @heroicons/vue \
    dayjs \
    axios \
    chart.js \
    vue-chartjs \
    sortablejs \
    vuedraggable@next

echo "▶ Publicando assets de Spatie Permission..."
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

echo "▶ Publicando config de Horizon..."
php artisan vendor:publish --provider="Laravel\Horizon\HorizonServiceProvider"

echo "▶ Ejecutando migraciones base..."
php artisan migrate

echo "▶ Ejecutando seeders base..."
php artisan db:seed --class=SistemaBaseSeeder

echo "✅ Setup completo. Próximo paso: cp .env.example .env y configurar variables."
