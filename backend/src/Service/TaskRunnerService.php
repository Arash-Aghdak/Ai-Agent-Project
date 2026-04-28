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

        $output = $this->executeTask($agent, $inputText, $taskRun);
        $taskRun->setOutputText($output);

        $this->entityManager->persist($taskRun);
        $this->entityManager->flush();

        return $taskRun;
    }

    private function executeTask(Agent $agent, string $inputText, TaskRun $taskRun): string
    {
        try {
            $prompt = $this->promptBuilder->buildPrompt($agent, $inputText);
            $output = $this->openAIService->generateText($prompt, $agent->getModel());

            $taskRun->setStatus('completed');

            return $output;
        } catch (\Throwable $e) {
            $taskRun->setStatus('failed');

            return $e->getMessage();
        }
    }
}