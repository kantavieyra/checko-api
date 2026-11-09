<?php
declare(strict_types=1);

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

return [
    'checko_api_key' => $_ENV['CHECKO_API_KEY'] ?? '',
    'checko_base_url' => $_ENV['CHECKO_BASE_URL'] ?? 'https://api.checko.ru/v2',
];
