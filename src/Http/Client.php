<?php
declare(strict_types=1);

namespace App\Http;

class Client
{
    public function __construct(
        private int $timeout = 15
    ) {}

    public function get(string $url, array $query = [], array $headers = []): array
    {
        if ($query) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($query);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => $this->prepareHeaders($headers),
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body     = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            throw new \RuntimeException('Upstream request failed: ' . $error);
        }

        $decoded = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON from upstream');
        }

        return [
            'status' => $httpCode,
            'body'   => $decoded,
        ];
    }

    private function prepareHeaders(array $headers): array
    {
        $result = ['Accept: application/json', 'User-Agent: CheckoApiProxy/1.0'];
        foreach ($headers as $k => $v) {
            $result[] = is_int($k) ? $v : "$k: $v";
        }
        return $result;
    }
}
