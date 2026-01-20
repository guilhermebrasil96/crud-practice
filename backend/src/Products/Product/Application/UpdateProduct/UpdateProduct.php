<?php

declare(strict_types=1);

namespace App\Products\Product\Application\UpdateProduct;

use App\Products\Product\Domain\Product;
use App\Products\Product\Domain\ProductRepository;

final class UpdateProduct
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    /**
     * @param array{name?: string, description?: string, price?: string|float} $data
     */
    public function __invoke(int $id, array $data): ?Product
    {
        $product = $this->productRepository->findById($id);
        if ($product === null) {
            return null;
        }

        if (array_key_exists('name', $data)) {
            $product->setName($data['name']);
        }
        if (array_key_exists('description', $data)) {
            $product->setDescription($data['description']);
        }
        if (array_key_exists('price', $data)) {
            $product->setPrice($data['price']);
        }

        $this->productRepository->save($product);

        return $product;
    }
}
