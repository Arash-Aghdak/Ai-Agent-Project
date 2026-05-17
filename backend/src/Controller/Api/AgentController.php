<?php

namespace App\Controller\Api;

use App\Entity\Agent;
use App\Entity\User;
use App\Entity\TaskRun;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AgentController extends AbstractController
{
    #[Route('/api/agents', name: 'api_agents_create', methods: ['POST'])]
public function create(
    Request $request,
    EntityManagerInterface $entityManager,
    ValidatorInterface $validator
): JsonResponse {

    /** @var User|null $user */
    $user = $this->getUser();

    if (!$user) {
        return $this->json([
            'error' => 'User not authenticated.'
        ], 401);
    }

    $data = json_decode($request->getContent(), true);

    if (!is_array($data)) {
        return $this->json([
            'error' => 'Invalid JSON data.'
        ], 400);
    }

    $agent = new Agent();

    $agent->setName(trim($data['name'] ?? ''));
    $agent->setRole(trim($data['role'] ?? ''));
    $agent->setInstructions(trim($data['instructions'] ?? ''));
    $agent->setModel(trim($data['model'] ?? ''));
    $agent->setIsActive(true);
    $agent->setCreatedBy($user);

    $errors = $validator->validate($agent);

    if (count($errors) > 0) {

        $formattedErrors = [];

        foreach ($errors as $error) {
            $formattedErrors[] = [
                'field' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }

        return $this->json([
            'errors' => $formattedErrors
        ], 400);
    }

    $entityManager->persist($agent);
    $entityManager->flush();

    return $this->json([
        'message' => 'Agent created successfully.',
        'data' => [
            'id' => $agent->getId(),
        ]
    ], 201);
}

    #[Route('/api/agents', name: 'api_agents_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'error' => 'User not authenticated.'
            ], 401);
        }

        $agents = $entityManager->getRepository(Agent::class)->findBy([
            'createdBy' => $user
        ]);

        $data = [];

        foreach ($agents as $agent) {
            $data[] = [
                'id' => $agent->getId(),
                'name' => $agent->getName(),
                'role' => $agent->getRole(),
                'model' => $agent->getModel(),
                'isActive' => $agent->isActive(),
                'createdAt' => $agent->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/agents/{id}', name: 'api_agents_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'error' => 'User not authenticated.'
            ], 401);
        }

        $agent = $this->findUserAgentOrNull($id, $user, $entityManager);

        if (!$agent) {
            return $this->json([
                'error' => 'Agent not found.'
            ], 404);
        }

        return $this->json([
            'id' => $agent->getId(),
            'name' => $agent->getName(),
            'role' => $agent->getRole(),
            'instructions' => $agent->getInstructions(),
            'model' => $agent->getModel(),
            'isActive' => $agent->isActive(),
            'createdAt' => $agent->getCreatedAt()?->format('Y-m-d H:i:s'),
        ]);
    }

    #[Route('/api/agents/{id}', name: 'api_agents_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'error' => 'User not authenticated.'
            ], 401);
        }

        $agent = $this->findUserAgentOrNull($id, $user, $entityManager);

        if (!$agent) {
            return $this->json([
                'error' => 'Agent not found.'
            ], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json([
                'error' => 'Invalid JSON data.'
            ], 400);
        }

        if (array_key_exists('name', $data)) {
            $agent->setName(trim((string) $data['name']));
        }

        if (array_key_exists('role', $data)) {
            $agent->setRole(trim((string) $data['role']));
        }

        if (array_key_exists('instructions', $data)) {
            $agent->setInstructions(trim((string) $data['instructions']));
        }

        if (array_key_exists('model', $data)) {
            $agent->setModel(trim((string) $data['model']));
        }

        if (array_key_exists('isActive', $data)) {
            $agent->setIsActive((bool) $data['isActive']);
        }

        $errors = $validator->validate($agent);

        if (count($errors) > 0) {
            $formattedErrors = [];

            foreach ($errors as $error) {
                $formattedErrors[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }

            return $this->json([
                'errors' => $formattedErrors
            ], 400);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Agent updated successfully.',
            'data' => [
                'id' => $agent->getId(),
            ]
        ]);
    }

   #[Route('/api/agents/{id}', name: 'api_agents_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'error' => 'User not authenticated.'
            ], 401);
        }

        $agent = $this->findUserAgentOrNull($id, $user, $entityManager);

        if (!$agent) {
            return $this->json([
                'error' => 'Agent not found.'
            ], 404);
        }

        $runs = $entityManager->getRepository(TaskRun::class)->findBy([
            'agent' => $agent,
            'createdBy' => $user,
        ]);

        foreach ($runs as $run) {
            $entityManager->remove($run);
        }

        $entityManager->remove($agent);
        $entityManager->flush();

        return $this->json([
            'message' => 'Agent deleted successfully.'
        ]);
    }

    private function findUserAgentOrNull(int $id, User $user, EntityManagerInterface $entityManager): ?Agent
    {
        return $entityManager->getRepository(Agent::class)->findOneBy([
            'id' => $id,
            'createdBy' => $user
        ]);
    }
}