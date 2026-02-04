<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\ProductRepository;

final class GetProducts
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
    }
    public function __invoke(): array
    {
        return $this->productRepository->findAll();
    }
}