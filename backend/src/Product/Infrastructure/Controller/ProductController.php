<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Application\GetProducts;
use App\Product\Application\GetProduct;
use App\Product\Application\CreateProduct;
use App\Product\Application\UpdateProduct;
use App\Product\Application\DeleteProduct;
use App\Product\Domain\Product;
use App\Shared\Infrastructure\File\FileUploader;
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
        private readonly FileUploader $fileUploader,
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
        $isForm = $request->request->has('name') || $request->files->has('image');
        $body = $isForm
            ? ['name' => $request->request->get('name', ''), 'description' => $request->request->get('description', ''), 'price' => $request->request->get('price')]
            : (json_decode($request->getContent(), true) ?? []);

        if ($request->files->has('image') && $request->files->get('image')) {
            try {
                $body['image'] = $this->fileUploader->upload($request->files->get('image'), 'products');
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
            }
        }
        try {
            $product = $this->createProduct->__invoke($body);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['success' => true, 'data' => ['product' => $product->toArray()]], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $isForm = $request->request->has('name') || $request->files->has('image');
        $body = $isForm
            ? ['name' => $request->request->get('name', ''), 'description' => $request->request->get('description', ''), 'price' => $request->request->get('price')]
            : (json_decode($request->getContent(), true) ?? []);

        if ($request->files->has('image') && $request->files->get('image')) {
            try {
                $body['image'] = $this->fileUploader->upload($request->files->get('image'), 'products');
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
            }
        }

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