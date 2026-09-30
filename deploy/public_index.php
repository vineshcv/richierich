<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
| FileZilla layout (domain → public_html):
|
|   public_html/
|     index.php          ← this file
|     .htaccess
|     assets/
|     sw.js
|     manifest.webmanifest
|     storage/           ← copy of storage/app/public (see deploy guide)
|     richierich/           ← full Laravel app (app, vendor, .env, ...)
*/

$appPath = __DIR__.'/richierich';

if (file_exists($maintenance = $appPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $appPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
