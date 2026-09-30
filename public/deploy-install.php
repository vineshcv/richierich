<?php

/**
 * One-time FileZilla install helper (no SSH).
 *
 * Visit: http://shop.richierich.in/deploy-install.php?key=richierich-install-2026
 * DELETE this file immediately after it succeeds.
 */

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$installKey = 'richierich-install-2026';
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

$lock = $appPath.'/storage/app/deploy-installed.lock';
if (is_file($lock)) {
    echo "Already installed (lock file exists). Delete storage/app/deploy-installed.lock to re-run.\n";
    exit;
}

try {
    echo "Running migrate --force --seed...\n";
    $kernel->call('migrate', ['--force' => true]);
    echo $kernel->output();
    $kernel->call('db:seed', ['--force' => true]);
    echo $kernel->output();

    $publicStorage = __DIR__.'/storage';
    $target = $appPath.'/storage/app/public';
    if (! is_dir($publicStorage)) {
        if (@symlink($target, $publicStorage)) {
            echo "Created storage symlink.\n";
        } else {
            echo "Symlink not allowed. Copy files instead...\n";
            if (! is_dir($publicStorage)) {
                mkdir($publicStorage, 0755, true);
            }
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $item) {
                $dest = $publicStorage.DIRECTORY_SEPARATOR.$iterator->getSubPathName();
                if ($item->isDir()) {
                    if (! is_dir($dest)) {
                        mkdir($dest, 0755, true);
                    }
                } else {
                    copy($item->getPathname(), $dest);
                }
            }
            echo "Copied storage/app/public → public_html/storage\n";
        }
    } else {
        echo "public_html/storage already exists.\n";
    }

    $kernel->call('config:clear');
    $kernel->call('route:clear');
    $kernel->call('view:clear');
    echo "Caches cleared.\n";

    file_put_contents($lock, date('c'));
    echo "\nSUCCESS. Delete deploy-install.php from the server now.\n";
    echo "Admin: /admin/login  →  admin@richierich.test / password\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR: ".$e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}
