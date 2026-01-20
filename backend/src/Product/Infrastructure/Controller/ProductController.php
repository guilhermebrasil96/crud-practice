<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Infrastructure\File\FileUploader;
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
        if ($isForm) {
            $name = (string) $request->request->get('name', '');
            $description = (string) $request->request->get('description', '');
            $price = $request->request->get('price');
        } else {
            $body = json_decode($request->getContent(), true) ?? [];
            $name = $body['name'] ?? '';
            $description = $body['description'] ?? '';
            $price = $body['price'] ?? null;
        }

        $imagePath = null;
        if ($request->files->has('image') && $request->files->get('image')) {
            try {
                $imagePath = $this->fileUploader->upload($request->files->get('image'), 'products');
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $product = $this->createProduct->__invoke($name, $description, $price, $imagePath);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['success' => true, 'data' => ['product' => $product->toArray()]], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $isForm = $request->request->has('name') || $request->files->has('image');
        if ($isForm) {
            $data = [
                'name' => $request->request->get('name'),
                'description' => $request->request->get('description'),
            ];
            $p = $request->request->get('price');
            if ($p !== null && $p !== '') {
                $data['price'] = $p;
            }
        } else {
            $data = json_decode($request->getContent(), true) ?? [];
        }

        if ($request->files->has('image') && $request->files->get('image')) {
            try {
                $data['image'] = $this->fileUploader->upload($request->files->get('image'), 'products');
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
            }
        }

        $product = $this->updateProduct->__invoke($id, $data);

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
