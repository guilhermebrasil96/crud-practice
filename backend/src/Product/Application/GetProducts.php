<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\Product;
use App\Product\Domain\ProductRepository;

final class GetProducts
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    /** @return Product[] */
    public function __invoke(): array
    {
        return $this->productRepository->findAll();
    }
}
