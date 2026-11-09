<?php
declare(strict_types=1);

namespace App\Api;

use App\Checko\CheckoService;

class CompanyController
{
    public function __construct(
        private CheckoService $service
    ) {}

    public function byInn(string $inn): array
    {
        return $this->service->getCompanyByInn($inn);
    }

    public function byOgrn(string $ogrn): array
    {
        return $this->service->getCompanyByOgrn($ogrn);
    }

    public function search(string $query): array
    {
        return $this->service->search($query);
    }
}
