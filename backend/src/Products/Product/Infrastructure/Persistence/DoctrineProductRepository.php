<?php

declare(strict_types=1);

namespace App\Products\Product\Infrastructure\Persistence;

use App\Products\Product\Domain\ProductRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DoctrineProductRepository implements ProductRepository
{
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {
    }

    public function findAll(): array
    {
        $response = $this->httpClient->request('GET', 'https://dummyjson.com/products');
                            
        $data = $response->toArray();
                    
        return $data['products'];
    }
}