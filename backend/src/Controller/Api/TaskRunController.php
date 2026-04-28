<?php

namespace App\Controller\Api;

use App\Entity\Agent;
use App\Entity\TaskRun;
use App\Entity\User;
use App\Service\TaskRunnerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskRunController extends AbstractController
{
    #[Route('/api/agents/{id}/run', name: 'api_agents_run', methods: ['POST'])]
    public function run(
        int $id,
        Request $request, 
        EntityManagerInterface $entityManager,
        TaskRunnerService $taskRunnerService
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

        if (!$data || empty($data['inputText'])) {
            return $this->json([
                'error' => 'inputText is required.'
            ], 400);
        }

        $taskRun = $taskRunnerService->run(
            $agent,
            $user,
            $data['inputText']
        );

        return $this->json([
            'taskRunId' => $taskRun->getId(),
            'status' => $taskRun->getStatus(),
            'outputText' => $taskRun->getOutputText(),
        ], 201);
    }

    #[Route('/api/agents/{id}/runs', name: 'api_agents_runs_list', methods: ['GET'])]
    public function listRuns(int $id, EntityManagerInterface $entityManager): JsonResponse
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

        $runs = $entityManager->getRepository(TaskRun::class)->findBy(
            ['agent' => $agent, 'createdBy' => $user],
            ['createdAt' => 'DESC']
        );

        $data = [];

        foreach ($runs as $run) {
            $data[] = [
                'id' => $run->getId(),
                'inputText' => $run->getInputText(),
                'outputText' => $run->getOutputText(),
                'status' => $run->getStatus(),
                'createdAt' => $run->getCreatedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    private function findUserAgentOrNull(int $id, User $user, EntityManagerInterface $entityManager): ?Agent
    {
        return $entityManager->getRepository(Agent::class)->findOneBy([
            'id' => $id,
            'createdBy' => $user
        ]);
    }
}