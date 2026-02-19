<?php

namespace App\Http\Controllers;

use App\Http\Requests\AskAiRequest;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AiAgentController extends Controller
{
    public function __construct(
        private AiService $aiService
    ) {}

    /**
     * Show AI Agent page with history.
     */
    public function index(): View
    {
        $history = $this->aiService->getHistory(auth()->user());

        return view('ai-agent.index', [
            'history' => $history,
        ]);
    }

    /**
     * Ask AI a question.
     */
    public function ask(AskAiRequest $request): JsonResponse
    {
        $conversation = $this->aiService->ask(
            auth()->user(),
            $request->validated('question')
        );

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'question' => $conversation->question,
                'answer' => $conversation->answer,
                'created_at' => $conversation->created_at->toISOString(),
            ],
        ]);
    }
}
