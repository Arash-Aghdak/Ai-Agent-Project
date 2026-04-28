<?php

namespace App\Service;

use App\Entity\Agent;

class PromptBuilderService
{
    public function buildPrompt(Agent $agent, string $input): string
    {
        return implode("\n\n", [
            "SYSTEM:",
            "You are a professional AI agent.",
            
            "ROLE:",
            $agent->getRole(),

            "INSTRUCTIONS:",
            $agent->getInstructions(),

            "TASK:",
            $input,

            "RESPONSE RULES:",
            "- Be clear and structured",
            "- Write in a professional tone",
            "- Do not include unnecessary explanations"
        ]);
    }
}