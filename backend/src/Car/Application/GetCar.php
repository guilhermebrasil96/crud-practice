<?php

declare(strict_types=1);

namespace App\Car\Application;

use App\Car\Domain\Car;
use App\Car\Domain\CarRepository;

final class GetCar
{
    public function __construct(
        private readonly CarRepository $carRepository
    ) {
    }

    public function __invoke(int $id): ?Car
    {
        return $this->carRepository->findById($id);
    }
}