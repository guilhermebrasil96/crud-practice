<?php

declare(strict_types=1);

namespace App\Car\Infrastructure\Controller;

use App\Car\Application\CreateCar;
use App\Car\Application\DeleteCar;
use App\Car\Application\GetCar;
use App\Car\Application\GetCars;
use App\Car\Application\UpdateCar;
use App\Car\Domain\Car;
use App\Infrastructure\File\FileUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CarController
{
    public function __construct(
        private readonly GetCars $getCars,
        private readonly GetCar $getCar,
        private readonly CreateCar $createCar,
        private readonly UpdateCar $updateCar,
        private readonly DeleteCar $deleteCar,
        private readonly FileUploader $fileUploader,
    ) {
    }

    public function list(): JsonResponse
    {
        $cars = $this->getCars->__invoke();
        $data = array_map(fn (Car $c) => $c->toArray(), $cars);
        return new JsonResponse(['success' => true, 'data' => ['cars' => $data]], Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $car = $this->getCar->__invoke($id);
        if ($car === null) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Car not found']], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['success' => true, 'data' => ['car' => $car->toArray()]], Response::HTTP_OK);
    }

    public function create(Request $request): JsonResponse
    {
        $isForm = $request->request->has('name') || $request->files->has('image');
        $body = $isForm
            ? ['name' => $request->request->get('name', ''), 'description' => $request->request->get('description', ''), 'price' => $request->request->get('price')]
            : (json_decode($request->getContent(), true) ?? []);

        if ($request->files->has('image') && $request->files->get('image')) {
            try {
                $body['image'] = $this->fileUploader->upload($request->files->get('image'), 'cars');
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $car = $this->createCar->__invoke($body);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['success' => true, 'data' => ['car' => $car->toArray()]], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $isForm = $request->request->has('name') || $request->files->has('image');
        if ($isForm) {
            $body = ['name' => $request->request->get('name'), 'description' => $request->request->get('description')];
            $p = $request->request->get('price');
            if ($p !== null && $p !== '') {
                $body['price'] = $p;
            }
        } else {
            $body = json_decode($request->getContent(), true) ?? [];
        }

        if ($request->files->has('image') && $request->files->get('image')) {
            try {
                $body['image'] = $this->fileUploader->upload($request->files->get('image'), 'cars');
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
            }
        }

        $car = $this->updateCar->__invoke($id, $body);
        if ($car === null) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Car not found']], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['success' => true, 'data' => ['car' => $car->toArray()]], Response::HTTP_OK);
    }

    public function delete(int $id): Response
    {
        $deleted = $this->deleteCar->__invoke($id);
        if (!$deleted) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Car not found']], Response::HTTP_NOT_FOUND);
        }
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}