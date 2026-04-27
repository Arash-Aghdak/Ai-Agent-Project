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
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'error' => 'User not authenticated.'
            ], 401);
        }

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'error' => 'Invalid JSON data.'
            ], 400);
        }

        if (
            empty($data['name']) ||
            empty($data['role']) ||
            empty($data['instructions']) ||
            empty($data['model'])
        ) {
            return $this->json([
                'error' => 'Name, role, instructions and model are required.'
            ], 400);
        }

        $agent = new Agent();
        $agent->setName($data['name']);
        $agent->setRole($data['role']);
        $agent->setInstructions($data['instructions']);
        $agent->setModel($data['model']);
        $agent->setIsActive(true);
        $agent->setCreatedBy($user);

        $entityManager->persist($agent);
        $entityManager->flush();

        return $this->json([
            'message' => 'Agent created successfully.',
            'id' => $agent->getId()
        ], 201);

        $errors = $validator->validate($agent);

        if (count($errors) > 0) {
            return $this->json([
                'errors' => (string) $errors
            ], 400);
        }
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
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
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

        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json([
                'error' => 'Invalid JSON data.'
            ], 400);
        }

        if (isset($data['name']) && !empty($data['name'])) {
            $agent->setName($data['name']);
        }

        if (isset($data['role']) && !empty($data['role'])) {
            $agent->setRole($data['role']);
        }

        if (isset($data['instructions']) && !empty($data['instructions'])) {
            $agent->setInstructions($data['instructions']);
        }

        if (isset($data['model']) && !empty($data['model'])) {
            $agent->setModel($data['model']);
        }

        if (isset($data['isActive'])) {
            $agent->setIsActive((bool) $data['isActive']);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Agent updated successfully.'
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

        /*$entityManager->remove($agent);
        $entityManager->flush();*/

        return $this->json([
            'message' => 'Agent deleted successfully.'
        ]);

        $runs = $entityManager->getRepository(TaskRun::class)->findBy([
            'agent' => $agent
        ]);

        foreach ($runs as $run) {
            $entityManager->remove($run);
        }

        $entityManager->remove($agent);
        $entityManager->flush();
    }

    private function findUserAgentOrNull(int $id, User $user, EntityManagerInterface $entityManager): ?Agent
    {
        return $entityManager->getRepository(Agent::class)->findOneBy([
            'id' => $id,
            'createdBy' => $user
        ]);
    }
}