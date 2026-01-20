<?php

declare(strict_types=1);

namespace App\Car\Application;

use App\Car\Domain\CarRepository;

final class GetCars
{
    public function __construct(
        private readonly CarRepository $carRepository
    ) {
    }

    public function __invoke(): array
    {
        return $this->carRepository->findAll();
    }
}