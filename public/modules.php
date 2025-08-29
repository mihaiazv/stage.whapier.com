<?php
// scripts/post_module_setup.php

// Output as plain text
header('Content-Type: text/plain; charset=UTF-8');

// 1) Regenerate Composer autoloader
echo "Running composer dump-autoload…\n";
//passthru('composer dump-autoload --optimize', $composerExit);
echo " → exit {$composerExit}\n\n";

// 2) Bootstrap Laravel for Artisan calls
require __DIR__ . '/../vendor/autoload.php';
$app    = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Helper to run an Artisan command
function art($kernel, string $cmd, array $args = []): void
{
    $full = $cmd . (count($args) ? ' ' . implode(' ', array_map(
        fn($k, $v) => is_bool($v)
            ? ($v ? "--{$k}" : '')
            : "--{$k}=".escapeshellarg($v),
        array_keys($args),
        $args
    )) : '');
    echo "php artisan {$full}…\n";
    $exit = $kernel->call($cmd, $args);
    echo " → exit {$exit}\n\n";
}

// 3) Run database migrations
art($kernel, 'migrate', ['--force' => true]);

// 4) Seed the addons table (if needed)
art($kernel, 'db:seed', ['--class' => 'AddonsTableSeeder', '--force' => true]);

// 5) Clear configuration & route caches
art($kernel, 'config:clear');
art($kernel, 'route:clear');

echo "✅ Post-module-setup tasks complete!\n";