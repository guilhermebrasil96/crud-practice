<?php

declare(strict_types=1);

namespace App\Products\Product\Application\DeleteProduct;

use App\Products\Product\Domain\ProductRepository;

final class DeleteProduct
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    public function __invoke(int $id): bool
    {
        $product = $this->productRepository->findById($id);
        if ($product === null) {
            return false;
        }

        $this->productRepository->remove($product);

        return true;
    }
}
