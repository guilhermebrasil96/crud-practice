<?php

declare(strict_types=1);

namespace App\Products\Product\Application\CreateProduct;

use App\Products\Product\Domain\Product;
use App\Products\Product\Domain\ProductRepository;

final class CreateProduct
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    public function __invoke(string $name, string $description, string|float|null $price = null): Product
    {
        if (trim($name) === '') {
            throw new \InvalidArgumentException('name is required');
        }

        $product = new Product();
        $product->setName(trim($name));
        $product->setDescription($description);
        if ($price !== null) {
            $product->setPrice($price);
        }

        $this->productRepository->save($product);

        return $product;
    }
}
