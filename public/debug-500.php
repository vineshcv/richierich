<?php

/**
 * Temporary error probe for FileZilla deploys.
 * Visit: https://shop.richierich.in/debug-500.php?key=richierich-install-2026
 * DELETE this file after debugging.
 */

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

$key = isset($_GET['key']) ? (string) $_GET['key'] : '';
if ($key !== 'richierich-install-2026') {
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
    echo "App not found\n";
    exit(1);
}

echo "App: {$appPath}\n";

try {
    require $appPath.'/vendor/autoload.php';
    $app = require $appPath.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    foreach (['/admin', '/admin/login', '/'] as $uri) {
        echo "\n--- {$uri} ---\n";
        try {
            $request = Illuminate\Http\Request::create($uri, 'GET');
            $response = $kernel->handle($request);
            echo 'status: '.$response->getStatusCode()."\n";
            if ($response->getStatusCode() >= 400) {
                $content = $response->getContent();
                echo substr(strip_tags($content), 0, 800)."\n";
            }
            $kernel->terminate($request, $response);
        } catch (Throwable $e) {
            echo 'EXCEPTION: '.$e->getMessage()."\n";
            echo $e->getFile().':'.$e->getLine()."\n";
        }
    }

    $log = $appPath.'/storage/logs/laravel.log';
    if (is_file($log)) {
        echo "\n--- last log lines ---\n";
        $lines = @file($log);
        if ($lines) {
            echo implode('', array_slice($lines, -40));
        }
    }
} catch (Throwable $e) {
    echo 'BOOT ERROR: '.$e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
    echo $e->getTraceAsString()."\n";
}
