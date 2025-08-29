<?php
// public/refresh.php
header('Content-Type: text/plain; charset=UTF-8');

// 1) Bootstrap the application
require __DIR__ . '/../vendor/autoload.php';
$app    = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// 2) Helper to run and log an Artisan command
function runCommand($kernel, string $cmd, array $args = [], string $label = null): void
{
    $label  = $label ?: $cmd;
    $time   = date('Y-m-d H:i:s');
    try {
        $exit    = $kernel->call($cmd, $args);
        $output  = trim($kernel->output());
        $message = $output !== '' ? "\nOUTPUT:\n$output" : '';
        printf("[%s] %-25s → exit %d%s\n\n", $time, $label, $exit, $message);
    } catch (\Throwable $e) {
        printf("[%s] %-25s → ERROR: %s\n\n", $time, $label, $e->getMessage());
    }
}

// 3) Enter maintenance mode
runCommand($kernel, 'down', ['--quiet' => true], 'Maintenance ON');

// 4) Delete old log files
foreach (glob(__DIR__ . '/../storage/logs/*.log') as $file) {
    @unlink($file);
    printf("[%s] Deleted log: %s\n\n", date('Y-m-d H:i:s'), basename($file));
}

// 5) Clear all caches
foreach ([
    'view:clear'     => 'View cache',
    'route:clear'    => 'Route cache',
    'config:clear'   => 'Config cache',
    'cache:clear'    => 'App cache',
    'optimize:clear' => 'Optimize files',
    'event:clear'    => 'Event cache',
    'clear-compiled' => 'Compiled classes',
] as $cmd => $label) {
    runCommand($kernel, $cmd, [], "Clear: {$label}");
}

// 6) Ensure storage symlink
runCommand($kernel, 'storage:link', [], 'Symlink storage');

// 7) Run core migrations
runCommand($kernel, 'migrate', ['--force' => true], 'Core migrations');

// 8) Run each module’s migrations
$modulesDir = __DIR__ . '/../modules';
if (is_dir($modulesDir)) {
    foreach (scandir($modulesDir) as $mod) {
        if (in_array($mod, ['.', '..'], true)) {
            continue;
        }
        $path = "modules/{$mod}/database/migrations";
        if (is_dir(__DIR__ . "/../{$path}")) {
            runCommand(
                $kernel,
                'migrate',
                ['--path' => $path, '--force' => true],
                "Module migrations: {$mod}"
            );
        }
    }
}

// 9) Exit maintenance mode
runCommand($kernel, 'up', [], 'Maintenance OFF');

// 10) Re-build caches (skipping failures on route/event cache)
foreach ([
    'config:cache' => 'Config cache',
    'route:cache'  => 'Route cache',
    'view:cache'   => 'View cache',
    'event:cache'  => 'Event cache',
] as $cmd => $label) {
    runCommand($kernel, $cmd, [], "Cache: {$label}");
}

// 11) Restart queue workers
runCommand($kernel, 'queue:restart', [], 'Queue restart');

echo "\n✅ Full application refresh complete!\n";



echo "\n✅ COMENZI MANUALE\n";

 Artisan::call('db:seed', ['--class' => 'AddonsTableSeeder']);
 Artisan::call('migrate');

echo "\n✅ COMENZI MANUALE GATA\n";
