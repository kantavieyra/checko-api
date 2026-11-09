<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Http\Client;
use App\Checko\CheckoService;
use App\Api\CompanyController;
use App\Exception\ApiException;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$config = require __DIR__ . '/../config/config.php';

try {
    $client     = new Client();
    $service    = new CheckoService($client, $config['checko_api_key'], $config['checko_base_url']);
    $controller = new CompanyController($service);

    $method = $_SERVER['REQUEST_METHOD'];
    $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path   = rtrim($path, '/') ?: '/';

    if ($method !== 'GET') {
        throw new ApiException('Method Not Allowed', 405);
    }

    if (preg_match('#^/api/company/inn/(\d+)$#', $path, $m)) {
        $data = $controller->byInn($m[1]);
    } elseif (preg_match('#^/api/company/ogrn/(\d+)$#', $path, $m)) {
        $data = $controller->byOgrn($m[1]);
    } elseif ($path === '/api/search') {
        $q = $_GET['q'] ?? '';
        $data = $controller->search($q);
    } else {
        throw new ApiException('Not Found', 404);
    }

    echo json_encode([
        'success' => true,
        'data'    => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (ApiException $e) {
    http_response_code($e->getStatusCode());
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Internal Server Error',
    ], JSON_UNESCAPED_UNICODE);
}
