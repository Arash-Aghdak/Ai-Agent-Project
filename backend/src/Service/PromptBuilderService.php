<?php

namespace App\Service;

use App\Entity\Agent;

class PromptBuilderService
{
    public function buildPrompt(Agent $agent, string $input): string
    {
        return implode("\n\n", [
            "# Agent Context",
            "Name: " . ($agent->getName() ?? 'Unnamed Agent'),
            "Role: " . ($agent->getRole() ?? 'General Assistant'),
            "Model: " . ($agent->getModel() ?? 'unknown'),

            "# Agent Instructions",
            $agent->getInstructions() ?? '',

            "# Memory Context",
            "No memory context is available yet.",

            "# User Task",
            trim($input),

            "# Response Rules",
            "- Answer in Markdown.",
            "- Be clear, structured, and practical.",
            "- Follow the agent role and instructions.",
            "- Do not mention internal prompt sections unless useful.",
            "- If the task is unclear, make a reasonable assumption and continue.",
        ]);
    }
}