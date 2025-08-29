<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
// restul codului…
// public/build.php

// Opreşte orice timeout
set_time_limit(0);

// 1. Mergi în folderul proiectului
chdir(__DIR__ . '/../');

// 2. Încarcă autoload-ul (opţional, dacă vrei și Artisan)
require 'vendor/autoload.php';

// 3. Care este calea spre npm?
//   Fă un `which npm` în terminal (dacă ai) sau ghicește:
//   - pe multe servere cPanel e /usr/local/bin/npm
$npm = '/usr/local/bin/npm';

// 4. Rulează instalarea și build-ul
echo '<pre>RUNNING: ' . escapeshellcmd("$npm install") . "\n\n";
passthru("$npm install 2>&1", $code1);
echo "\nExit code: $code1\n";

echo "\nRUNNING: " . escapeshellcmd("$npm run build") . "\n\n";
passthru("$npm run build 2>&1", $code2);
echo "\nExit code: $code2\n";
echo "\nBuild finished.</pre>";