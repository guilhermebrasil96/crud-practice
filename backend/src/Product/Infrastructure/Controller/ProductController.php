<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\CreateProduct;
use App\Product\Application\DeleteProduct;
use App\Product\Application\GetProduct;
use App\Product\Application\GetProducts;
use App\Product\Application\UpdateProduct;
use App\Product\Domain\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ProductController
{
    public function __construct(
        private readonly GetProducts $getProducts,
        private readonly GetProduct $getProduct,
        private readonly CreateProduct $createProduct,
        private readonly UpdateProduct $updateProduct,
        private readonly DeleteProduct $deleteProduct,
    ) {
    }

    public function list(): JsonResponse
    {
        $products = $this->getProducts->__invoke();
        $data = array_map(fn (Product $p) => $p->toArray(), $products);
        return new JsonResponse(['success' => true, 'data' => ['products' => $data]], Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->getProduct->__invoke($id);
        if ($product === null) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Product not found']], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['success' => true, 'data' => ['product' => $product->toArray()]], Response::HTTP_OK);
    }

    public function create(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent(), true) ?? [];

        try {
            $product = $this->createProduct->__invoke(
                $body['name'] ?? '',
                $body['description'] ?? '',
                $body['price'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['success' => true, 'data' => ['product' => $product->toArray()]], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $body = json_decode($request->getContent(), true) ?? [];
        $product = $this->updateProduct->__invoke($id, $body);

        if ($product === null) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Product not found']], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['success' => true, 'data' => ['product' => $product->toArray()]], Response::HTTP_OK);
    }

    public function delete(int $id): Response
    {
        $deleted = $this->deleteProduct->__invoke($id);

        if (!$deleted) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Product not found']], Response::HTTP_NOT_FOUND);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
