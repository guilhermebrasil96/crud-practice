<?php

declare(strict_types=1);

namespace App\Products\Product\Application\GetProduct;

use App\Products\Product\Domain\Product;
use App\Products\Product\Domain\ProductRepository;

final class GetProduct
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    public function __invoke(int $id): ?Product
    {
        return $this->productRepository->findById($id);
    }
}
