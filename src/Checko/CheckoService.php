<?php
declare(strict_types=1);

namespace App\Checko;

use App\Http\Client;
use App\Exception\ApiException;

class CheckoService
{
    public function __construct(
        private Client $client,
        private string $apiKey,
        private string $baseUrl = 'https://api.checko.ru/v2'
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function getCompanyByInn(string $inn): array
    {
        $this->validateInn($inn);
        return $this->request('/company', ['inn' => $inn]);
    }

    public function getCompanyByOgrn(string $ogrn): array
    {
        $this->validateOgrn($ogrn);
        return $this->request('/company', ['ogrn' => $ogrn]);
    }

    public function search(string $query): array
    {
        $query = trim($query);
        if (mb_strlen($query) < 2) {
            throw new ApiException('Query too short', 400);
        }
        return $this->request('/search', ['query' => $query]);
    }

    private function request(string $endpoint, array $params): array
    {
        if (empty($this->apiKey)) {
            throw new ApiException('API key is not configured', 500);
        }

        $params['key'] = $this->apiKey;

        $response = $this->client->get($this->baseUrl . $endpoint, $params);
        return $this->handleResponse($response);
    }

    private function handleResponse(array $response): array
    {
        $status = $response['status'];
        $body   = $response['body'];

        if ($status === 200 && array_key_exists('data', $body)) {
            return $body['data'];
        }

        $message = $body['message'] ?? $body['error'] ?? 'Upstream error';
        $code    = ($status >= 400 && $status < 600) ? $status : 502;

        throw new ApiException((string) $message, $code);
    }

    private function validateInn(string $inn): void
    {
        if (!preg_match('/^\d{10}$|^\d{12}$/', $inn)) {
            throw new ApiException('Invalid INN (must be 10 or 12 digits)', 400);
        }
    }

    private function validateOgrn(string $ogrn): void
    {
        if (!preg_match('/^\d{13}$|^\d{15}$/', $ogrn)) {
            throw new ApiException('Invalid OGRN (must be 13 or 15 digits)', 400);
        }
    }
}
