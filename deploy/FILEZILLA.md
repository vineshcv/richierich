# Deploy Richie Rich with FileZilla only (no SSH)

Domain document root: `public_html` → http://shop.richierich.in

## Final server folder layout

```
public_html/
  index.php                 ← use deploy/public_index.php
  .htaccess                 ← from public/.htaccess
  assets/
  robots.txt (optional)
  deploy-install.php        ← delete after install
  storage/                  ← product/banner images (created by installer)
  richierich/                  ← full Laravel app
    app/
    bootstrap/
    config/
    database/
    resources/
    routes/
    storage/
    vendor/                 ← upload from your Mac after composer install
    .env                    ← from deploy/env.production (edited)
    artisan
    composer.json
    ...
```

## On your Mac (before upload)

```bash
cd /Applications/MAMP/htdocs/richierich
composer install --no-dev --optimize-autoloader
```

## Hostinger (hPanel)

1. **MySQL** → create database + user. Note name / user / password.
2. Keep phpMyAdmin handy (only needed if the web installer fails).

## FileZilla upload

1. Create folder `public_html/richierich/`
2. Upload **everything except** the local `public/` folder into `public_html/richierich/`  
   (include `vendor/`, `storage/`, `bootstrap/`, etc.)
3. Upload **contents of** local `public/` into `public_html/`  
   (`assets`, `deploy-install.php`, `.htaccess`, …)
4. Upload `deploy/public_index.php` as `public_html/index.php` (overwrite)
5. Upload `deploy/env.production` as `public_html/richierich/.env`
6. Edit `.env` on the server (FileZilla → View/Edit) and set:

```
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
APP_URL=http://shop.richierich.in
```

## Run installer in browser

Open:

`http://shop.richierich.in/deploy-install.php?key=richierich-install-2026`

You should see `SUCCESS`.

Then **delete** `public_html/deploy-install.php` in FileZilla.

## Login

- Store: http://shop.richierich.in
- Admin: http://shop.richierich.in/admin/login
- Email: `admin@richierich.test`
- Password: `password`  
  Change this after login.

## If images are missing

Upload local folder:

`storage/app/public/` → `public_html/storage/`

(so URLs like `/storage/products/...` work)

## If installer errors on DB

1. Open Hostinger phpMyAdmin  
2. Locally run: `php artisan migrate --seed` against a dump, or import a SQL export  
3. Or fix DB_* in `.env` and re-run installer after deleting `richierich/storage/app/deploy-installed.lock`

## After SSL is enabled

Set in `.env`:

```
APP_URL=https://shop.richierich.in
```
