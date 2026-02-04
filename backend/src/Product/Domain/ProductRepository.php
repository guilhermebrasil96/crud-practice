<?php

declare(strict_types=1);

namespace App\Product\Domain;

interface ProductRepository
{
    public function findAll(): array;
    public function findById(int $id): ?Product;
    public function save(Product $product): void;
    public function remove(Product $product): void;
}