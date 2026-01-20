<?php

declare(strict_types=1);

namespace App\Car\Application;

use App\Car\Domain\Car;
use App\Car\Domain\CarRepository;

final class UpdateCar
{
    public function __construct(
        private readonly CarRepository $carRepository
    ) {
    }

    /**
     * @param array{name?: string, description?: string, price?: string|float} $data
     */
    public function __invoke(int $id, array $data): ?Car
    {
        $car = $this->carRepository->findById($id);
        if ($car === null) {
            return null;
        }
        if (array_key_exists('name', $data)) {
            $car->setName($data['name']);
        }
        if (array_key_exists('description', $data)) {
            $car->setDescription($data['description']);
        }
        if (array_key_exists('price', $data)) {
            $car->setPrice($data['price']);
        }
        if (array_key_exists('image', $data)) {
            $car->setImage($data['image']);
        }
        $this->carRepository->save($car);
        return $car;
    }
}