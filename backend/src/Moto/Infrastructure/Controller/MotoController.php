<?php

declare(strict_types=1);

namespace App\Moto\Infrastructure\Controller;

use App\Moto\Application\CreateMoto;
use App\Moto\Application\DeleteMoto;
use App\Moto\Application\GetMoto;
use App\Moto\Application\GetMotos;
use App\Moto\Application\UpdateMoto;
use App\Moto\Domain\Moto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MotoController
{
    public function __construct(
        private readonly GetMotos $getMotos,
        private readonly GetMoto $getMoto,
        private readonly CreateMoto $createMoto,
        private readonly UpdateMoto $updateMoto,
        private readonly DeleteMoto $deleteMoto,
    ) {
    }

    public function list(): JsonResponse
    {
        $motos = $this->getMotos->__invoke();
        $data = array_map(fn (Moto $m) => $m->toArray(), $motos);
        return new JsonResponse(['success' => true, 'data' => ['motos' => $data]], Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $moto = $this->getMoto->__invoke($id);
        if ($moto === null) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Moto not found']], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['success' => true, 'data' => ['moto' => $moto->toArray()]], Response::HTTP_OK);
    }

    public function create(Request $request): JsonResponse
    {
        $body = json_decode($request->getContent(), true) ?? [];
        try {
            $moto = $this->createMoto->__invoke($body);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['success' => true, 'data' => ['moto' => $moto->toArray()]], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $body = json_decode($request->getContent(), true) ?? [];
        $moto = $this->updateMoto->__invoke($id, $body);
        if ($moto === null) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Moto not found']], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['success' => true, 'data' => ['moto' => $moto->toArray()]], Response::HTTP_OK);
    }

    public function delete(int $id): Response
    {
        $deleted = $this->deleteMoto->__invoke($id);
        if (!$deleted) {
            return new JsonResponse(['success' => false, 'error' => ['message' => 'Moto not found']], Response::HTTP_NOT_FOUND);
        }
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}