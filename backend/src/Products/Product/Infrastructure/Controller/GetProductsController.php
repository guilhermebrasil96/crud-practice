<?php

declare(strict_types=1);

namespace App\Products\Product\Infrastructure\Controller;

use App\Products\Product\Application\GetProducts\GetProducts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class GetProductsController extends AbstractController
{

    public function __construct(
        private GetProducts $getProducts
    ) {
    }

    #[Route('/products', name: 'app_products_get', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $products = $this->getProducts->__invoke();

            return new JsonResponse([
                'success' => true,
                'data' => [
                    'products' => $products,
                ],
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => [
                    'message' => 'An unexpected error occurred',
                    'details' => $e->getMessage(),
                ],
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}