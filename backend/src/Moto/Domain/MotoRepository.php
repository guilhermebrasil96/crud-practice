<?php

declare(strict_types=1);

namespace App\Moto\Domain;

interface MotoRepository
{
    public function findAll(): array;
    public function findById(int $id): ?Moto;
    public function save(Moto $moto): void;
    public function remove(Moto $moto): void;
}