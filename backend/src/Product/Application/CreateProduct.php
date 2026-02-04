<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\Product;
use App\Product\Domain\ProductRepository;

final class CreateProduct
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
    }
    public function __invoke(array $data): Product
    {
        $name = $data['name'] ?? '';
        if (trim($name) === '') {
            throw new \InvalidArgumentException('name is required');
        }

        $product = new Product();
        $product->setName(trim($name));
        $product->setDescription($data['description'] ?? '');

        if (array_key_exists('price', $data)) {
            $product->setPrice($data['price']);
        }
        if (!empty($data['image'])) {
            $product->setImage($data['image']);
        }

        $this->productRepository->save($product);

        return $product;
    }
}