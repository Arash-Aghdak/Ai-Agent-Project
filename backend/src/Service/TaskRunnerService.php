<?php

namespace App\Service;

use App\Entity\Agent;
use App\Entity\TaskRun;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TaskRunnerService
{
    public function __construct(
        private PromptBuilderService $promptBuilder,
        private OpenAIService $openAIService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function run(Agent $agent, User $user, string $inputText): TaskRun
    {
        $taskRun = new TaskRun();
        $taskRun->setAgent($agent);
        $taskRun->setCreatedBy($user);
        $taskRun->setInputText($inputText);
        $taskRun->setStatus('pending');

        $this->entityManager->persist($taskRun);
        $this->entityManager->flush();

        $this->executeTask($agent, $inputText, $taskRun);

        $this->entityManager->flush();

        return $taskRun;
    }

    private function executeTask(Agent $agent, string $inputText, TaskRun $taskRun): void
    {
        $taskRun->setStatus('running');
        $taskRun->setStartedAt(new \DateTimeImmutable());

        $prompt = $this->promptBuilder->buildPrompt($agent, $inputText);
        $taskRun->setFinalPrompt($prompt);

        try {
            $output = $this->openAIService->generateText($prompt, $agent->getModel());

            $taskRun->setOutputText($output);
            $taskRun->setStatus('completed');
        } catch (\Throwable $e) {
            $taskRun->setErrorMessage($e->getMessage());
            $taskRun->setStatus('failed');
        }

        $taskRun->setFinishedAt(new \DateTimeImmutable());
    }
}