<?php

declare(strict_types=1);

namespace App\Products\Product\Domain;

interface ProductRepository
{
    public function findAll(): array;
}