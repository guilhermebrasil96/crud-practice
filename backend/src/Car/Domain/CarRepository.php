<?php

declare(strict_types=1);

namespace App\Car\Domain;

interface CarRepository
{
    public function findAll(): array;
    public function findById(int $id): ?Car;
    public function save(Car $car): void;
    public function remove(Car $car): void;
}