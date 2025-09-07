Setup Instructions
1. Clone and Install
git clone <repo-url>
cd terabox-bot
composer install
cp .env.example .env
php artisan key:generate

2. Configure .env
APP_NAME=TeraboxBot
APP_ENV=local
APP_KEY=base64:AXWcZSiCyDsPLGgmO+O4Xh9fdrhXOx8IN3kWf3Kk6A4=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=https://borders-handmade-connected-hdtv.trycloudflare.com

TELEGRAM_BOT_TOKEN=8268349060:AAFTeZgu17M2QchxIt0YexJnKyVh22UP1PE
TELEGRAM_WEBHOOK_SECRET=my-super-secret-key
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=terabox_bot
DB_USERNAME=root
DB_PASSWORD=
3. Database Migration
php artisan migrate
4. Start Server
php artisan serve
# default: http://127.0.0.1:8000
Local Development (Polling)
Open your bot on Telegram (@your_bot).
Send a message with a Terabox link:
https://www.terabox.com/s/abcd1234
Run artisan poll command:
php artisan telegram:poll
This fetches new updates, processes Terabox links, saves them in DB, and replies back.
Check the generated link:
http://127.0.0.1:8000/video/{slug}

Database Schema
video_links table:
id
telegram_user_id (nullable)
telegram_chat_id (nullable)
telegram_message_id (nullable)
original_url (text)
slug (string, unique)
new_url (string, unique)
created_at, updated_at
