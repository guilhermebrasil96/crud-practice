<?php

declare(strict_types=1);

namespace App\Moto\Infrastructure\Controller;

use App\Infrastructure\File\FileUploader;
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
        private readonly FileUploader $fileUploader,
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
        $isForm = $request->request->has('name') || $request->files->has('image');
        $body = $isForm
            ? ['name' => $request->request->get('name', ''), 'description' => $request->request->get('description', ''), 'price' => $request->request->get('price')]
            : (json_decode($request->getContent(), true) ?? []);

        if ($request->files->has('image') && $request->files->get('image')) {
            try {
                $body['image'] = $this->fileUploader->upload($request->files->get('image'), 'motos');
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $moto = $this->createMoto->__invoke($body);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['success' => true, 'data' => ['moto' => $moto->toArray()]], Response::HTTP_CREATED);
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
                $body['image'] = $this->fileUploader->upload($request->files->get('image'), 'motos');
            } catch (\InvalidArgumentException $e) {
                return new JsonResponse(['success' => false, 'error' => ['message' => $e->getMessage()]], Response::HTTP_BAD_REQUEST);
            }
        }

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