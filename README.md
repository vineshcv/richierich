# Richie Rich

Boutique storefront + admin + JWT API built with Laravel.

## Requirements

- PHP 8.2+
- Composer
- MySQL (MAMP recommended locally)
- MAMP Apache on port **8888**, MySQL on port **8889**

## Setup

1. Install dependencies:

```bash
composer install
```

2. Copy env and generate key (if needed):

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

3. Configure `.env` database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=richierich
DB_USERNAME=root
DB_PASSWORD=root
```

4. Create the MySQL database `richierich` (phpMyAdmin or CLI):

```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -proot -h 127.0.0.1 -P 8889 -e "CREATE DATABASE IF NOT EXISTS richierich CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

5. Run migrations and seed:

```bash
php artisan migrate --seed
php artisan storage:link
```

Seeded admin:

- Email: `admin@richierich.test`
- Password: `password`

## URLs (MAMP)

| Area | URL |
|------|-----|
| Storefront | http://localhost:8888/richierich/public/ |
| Shop | http://localhost:8888/richierich/public/shop |
| Cart | http://localhost:8888/richierich/public/cart |
| Contact | http://localhost:8888/richierich/public/contact |
| Sets / Season / Bulk | `/combos` · `/season` · `/bulk` |
| Admin login | http://localhost:8888/richierich/public/admin/login |
| API base | http://localhost:8888/richierich/public/api |

Storefront UI/UX matches the dress demo (`business/dressindex.html`): same CSS/JS (product sheet, cart, bottom nav, WhatsApp flows, filters). Catalog data is loaded from MySQL into the dress JS globals.

## API auth (JWT)

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@richierich.test",
  "password": "password"
}
```

Use the returned token:

```http
Authorization: Bearer {token}
```

Other auth routes (JWT required): `GET /api/auth/me`, `POST /api/auth/logout`, `POST /api/auth/refresh`.

Public reads: `/api/categories`, `/api/products`, `/api/banners`, `/api/settings`.

## Production

| Area | URL |
|------|-----|
| Storefront | http://shop.richierich.in |
| Admin | http://shop.richierich.in/admin/login |
| API | http://shop.richierich.in/api |

On the server `.env`:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=http://shop.richierich.in
```

Document root must be Laravel’s `public/` folder (or upload so `public` contents sit at the subdomain root). Then:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
```
