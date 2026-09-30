<?php

/**
 * One-time FileZilla migrate helper (no SSH). Does NOT seed.
 *
 * Visit: http://shop.richierich.in/deploy-migrate.php?key=richierich-migrate-2026
 * DELETE this file immediately after it succeeds.
 */

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$installKey = 'richierich-migrate-2026';
$key = isset($_GET['key']) ? (string) $_GET['key'] : '';
if ($key !== $installKey) {
    http_response_code(403);
    echo "Forbidden\n";
    exit;
}

$candidates = [
    __DIR__.'/richierich',
    __DIR__.'/../richierich',
    dirname(__DIR__),
];

$appPath = null;
foreach ($candidates as $path) {
    if (is_file($path.'/bootstrap/app.php') && is_file($path.'/vendor/autoload.php')) {
        $appPath = $path;
        break;
    }
}

if ($appPath === null) {
    http_response_code(500);
    echo "Laravel app not found. Expected public_html/richierich with vendor/ uploaded.\n";
    exit;
}

echo "App path: {$appPath}\n";

require $appPath.'/vendor/autoload.php';
$app = require $appPath.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Running migrate --force (no seed)...\n";
    $kernel->call('migrate', ['--force' => true]);
    echo $kernel->output();

    $kernel->call('view:clear');
    $kernel->call('route:clear');
    echo "Caches cleared.\n";
    echo "\nSUCCESS. Delete deploy-migrate.php from the server now.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR: ".$e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}
