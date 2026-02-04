<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\Product;
use App\Product\Domain\ProductRepository;

final class GetProduct
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function __invoke(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }
}