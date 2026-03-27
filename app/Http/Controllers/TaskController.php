<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks.
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
        ]);

        $validated['status'] = $validated['status'] ?? 'pending';

        $task = Task::create($validated);
        
        return response()->json($task, 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        return response()->json($task);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed',
            'due_date' => 'nullable|date'
        ]);

        $task->update($validated);
        
        return response()->json($task);
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        
        return response()->json(['message' => 'Task deleted successfully']);
    }

    /**
     * Generate task description using AI (Ollama).
     */
    public function generateDescription(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100'
        ]);

        try {
            // Проверяем, запущен ли Ollama
            $healthCheck = Http::timeout(2)->get('http://localhost:11434/api/tags');
            
            if (!$healthCheck->successful()) {
                return response()->json([
                    'error' => 'Ollama не запущен. Запустите Ollama командой: ollama serve'
                ], 503);
            }

            // Запрос к Ollama
            $response = Http::timeout(30)->post('http://localhost:11434/api/generate', [
                'model' => 'llama3.2',
                'prompt' => "Придумай подробное описание для задачи: {$request->title}. Описание должно быть на русском языке, 1-2 предложения, полезное и конкретное. Только описание, без лишнего текста.",
                'stream' => false
            ]);

            $description = $response->json()['response'] ?? 'Описание не сгенерировано';
            
            // Убираем возможные лишние символы
            $description = trim(preg_replace('/[\r\n]+/', ' ', $description));
            
            return response()->json([
                'description' => $description
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ошибка генерации: ' . $e->getMessage(),
                'hint' => 'Убедитесь, что Ollama запущен и модель llama3.2 загружена (ollama pull llama3.2)'
            ], 500);
        }
    }
}