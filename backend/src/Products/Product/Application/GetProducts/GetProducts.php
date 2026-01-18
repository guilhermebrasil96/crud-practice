<?php

declare(strict_types=1);

namespace App\Products\Product\Application\GetProducts;

use App\Products\Product\Domain\ProductRepository;

final class GetProducts
{

    public function __construct(
        private readonly ProductRepository $productRepository
    ) {}

    public function __invoke(): array
    {
        $products = $this->productRepository->findAll();
        
        return $products;
    }
}