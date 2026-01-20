<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\Product;
use App\Product\Domain\ProductRepository;

final class UpdateProduct
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    /**
     * @param array{name?: string, description?: string, price?: string|float, image?: string|null} $data
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
        if (array_key_exists('image', $data)) {
            $product->setImage($data['image']);
        }

        $this->productRepository->save($product);

        return $product;
    }
}
