<?php

declare(strict_types=1);

namespace App\Car\Application;

use App\Car\Domain\Car;
use App\Car\Domain\CarRepository;

final class CreateCar
{
    public function __construct(
        private readonly CarRepository $carRepository
    ) {
    }

    public function __invoke(array $data): Car
    {
        $name = $data['name'] ?? '';
        if (trim($name) === '') {
            throw new \InvalidArgumentException('name is required');
        }

        $car = new Car();
        $car->setName(trim($name));
        $car->setDescription($data['description'] ?? '');
        if (array_key_exists('price', $data)) {
            $car->setPrice($data['price']);
        }

        $this->carRepository->save($car);

        return $car;
    }
}