<?php

declare(strict_types=1);

namespace App\Car\Application;

use App\Car\Domain\CarRepository;

final class DeleteCar
{
    public function __construct(
        private readonly CarRepository $carRepository
    ) {
    }

    public function __invoke(int $id): bool
    {
        $car = $this->carRepository->findById($id);
        if ($car === null) {
            return false;
        }
        $this->carRepository->remove($car);
        return true;
    }
}