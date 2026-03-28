<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TaskController extends Controller
{
    private const OLLAMA_BASE = 'http://localhost:11434';

    private const OLLAMA_MODEL = 'llama3.2';

    /** Параметры генерации: меньше «творчества» и достаточный лимит токенов — реже обрывы и пропуски слогов. */
    private const OLLAMA_OPTIONS_DESCRIPTION = [
        'temperature' => 0.22,
        'top_p' => 0.88,
        'repeat_penalty' => 1.18,
        'num_predict' => 400,
    ];

    private const OLLAMA_OPTIONS_STATUS = [
        'temperature' => 0.15,
        'top_p' => 0.88,
        'repeat_penalty' => 1.08,
        'num_predict' => 48,
    ];

    private const OLLAMA_SYSTEM_DESCRIPTION = <<<'TXT'
Ты помощник для списка задач. Ответ — только на русском, без английских слов и без латиницы внутри фразы.
Запрещены любые английские слова, включая связки: afterwards, then, also, however, therefore, finally, maybe, usually, before, after, with, without, desired, garnish, meat, vegetables — всё только русскими словами (затем, тогда, также, однако, поэтому, наконец, возможно, обычно, до, после, с, без и т.д.).
Не смешивай кириллицу и латиницу в одном слове. Если в заголовке есть термин на латинице — одно отдельное слово, остальное по-русски.
Без вступлений — сразу суть в 1–2 предложениях.
TXT;

    private const OLLAMA_SYSTEM_STATUS = <<<'TXT'
Ты классифицируешь задачи. Отвечай одним словом: pending, in_progress или completed.
Учитывай смысл заголовка и описания; не искажай иностранные слова при оценке.
TXT;

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
            $healthCheck = Http::timeout(2)->get(self::OLLAMA_BASE . '/api/tags');
            
            if (!$healthCheck->successful()) {
                return response()->json([
                    'error' => 'Ollama не запущен. Запустите Ollama командой: ollama serve'
                ], 503);
            }

            $title = trim(preg_replace('/\s+/u', ' ', $request->title));

            // Запрос к Ollama (system + options снижают галлюцинации и обрывы на многоязычном тексте)
            $response = Http::timeout(60)->post(self::OLLAMA_BASE . '/api/generate', [
                'model' => self::OLLAMA_MODEL,
                'system' => self::OLLAMA_SYSTEM_DESCRIPTION,
                'prompt' => 'Составь краткое описание задачи в 1–2 предложения по заголовку ниже. Только русский язык: ноль английских слов (никаких desired, garnish, meat в латинице). '
                    .'Правильные падежи (например «картофеля», не «картофелья»). '
                    ."Только текст описания, без пояснений.\n\nЗаголовок: «{$title}»",
                'stream' => false,
                'options' => self::OLLAMA_OPTIONS_DESCRIPTION,
            ]);

            $description = $response->json()['response'] ?? 'Описание не сгенерировано';

            $description = trim(preg_replace('/[\r\n]+/', ' ', $description));
            $description = $this->polishGeneratedDescription($description, $title);

            return response()->json([
                'description' => $description
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ошибка генерации: ' . $e->getMessage(),
                'hint' => 'Убедитесь, что Ollama запущен и модель ' . self::OLLAMA_MODEL . ' загружена (ollama pull ' . self::OLLAMA_MODEL . ')'
            ], 500);
        }
    }

    /**
     * Предложить статус задачи по заголовку и описанию (Ollama).
     */
    public function suggestStatus(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $title = trim(preg_replace('/\s+/u', ' ', $validated['title']));
        $descriptionTrimmed = isset($validated['description']) && $validated['description'] !== ''
            ? trim(preg_replace('/\s+/u', ' ', $validated['description']))
            : '';
        $descriptionForPrompt = $descriptionTrimmed !== '' ? $descriptionTrimmed : 'не указано';

        $prompt = sprintf(
            'Оцени статус задачи. Задача: %s. Описание: %s. '
            .'Варианты: pending (новая, не начата), in_progress (в работе), completed (завершена). '
            .'Верни только одно слово из трёх латиницей: pending, in_progress или completed.',
            $title,
            $descriptionForPrompt
        );

        try {
            $healthCheck = Http::timeout(2)->get(self::OLLAMA_BASE . '/api/tags');

            if (! $healthCheck->successful()) {
                return response()->json([
                    'error' => 'Ollama не запущен. Запустите Ollama командой: ollama serve',
                ], 503);
            }

            $response = Http::timeout(30)->post(self::OLLAMA_BASE . '/api/generate', [
                'model' => self::OLLAMA_MODEL,
                'system' => self::OLLAMA_SYSTEM_STATUS,
                'prompt' => $prompt,
                'stream' => false,
                'options' => self::OLLAMA_OPTIONS_STATUS,
            ]);

            if (! $response->successful()) {
                return response()->json([
                    'error' => 'Ollama вернул ошибку',
                    'detail' => $response->body(),
                ], $response->status() >= 400 && $response->status() < 600 ? $response->status() : 502);
            }

            $raw = $response->json('response');
            if (! is_string($raw) || $raw === '') {
                return response()->json([
                    'error' => 'Пустой ответ модели',
                ], 502);
            }

            $status = $this->normalizeSuggestedStatus($raw);
            if ($status === null) {
                return response()->json([
                    'error' => 'Не удалось распознать статус в ответе модели',
                    'raw' => trim($raw),
                ], 422);
            }

            return response()->json([
                'status' => $status,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'error' => 'Не удалось подключиться к Ollama',
                'hint' => 'Проверьте, что сервис запущен: ollama serve',
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ошибка при обращении к Ollama: ' . $e->getMessage(),
                'hint' => 'Убедитесь, что Ollama запущен и модель ' . self::OLLAMA_MODEL . ' загружена (ollama pull ' . self::OLLAMA_MODEL . ')',
            ], 500);
        }
    }

    /**
     * Лёгкая правка ответа модели: стык кириллицы и латиницы, частые опечатки.
     */
    private function polishGeneratedDescription(string $text, ?string $titleForAllowlist = null): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));
        // «добавлениемdesired» → вставить пробел между русской и латинской частью
        $text = preg_replace('/([а-яёА-ЯЁ])([a-zA-Z])/u', '$1 $2', $text);
        $text = preg_replace('/([a-zA-Z])([а-яёА-ЯЁ])/u', '$1 $2', $text);
        // распространённая путаница падежа
        $text = preg_replace('/картофелья/ui', 'картофеля', $text);

        foreach ($this->englishLeakReplacements() as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text);
        }

        $text = $this->stripStrayLatinWords($text, $titleForAllowlist);

        return $text;
    }

    /**
     * Словарь + фразы: длинные шаблоны первыми (иначе «such» съестся раньше фразы).
     *
     * @return array<string, string> regex => замена
     */
    private function englishLeakReplacements(): array
    {
        $phrases = [
            '/\bsuch\s+as\b/ui' => 'такими как',
            '/\bas\s+well\s+as\b/ui' => 'а также',
            '/\bas\s+well\b/ui' => 'также',
            '/\bfor\s+example\b/ui' => 'например',
            '/\bfor\s+instance\b/ui' => 'например',
            '/\bin\s+order\s+to\b/ui' => 'чтобы',
            '/\bdue\s+to\b/ui' => 'из-за',
            '/\bbecause\s+of\b/ui' => 'из-за',
            '/\baccording\s+to\b/ui' => 'согласно',
            '/\binstead\s+of\b/ui' => 'вместо',
            '/\bin\s+addition\b/ui' => 'кроме того',
            '/\bat\s+least\b/ui' => 'как минимум',
            '/\bat\s+first\b/ui' => 'сначала',
            '/\bat\s+last\b/ui' => 'наконец',
        ];

        $words = [
            'afterwards' => 'затем',
            'beforehand' => 'заранее',
            'furthermore' => 'кроме того',
            'additionally' => 'кроме того',
            'nevertheless' => 'тем не менее',
            'nonetheless' => 'тем не менее',
            'accordingly' => 'соответственно',
            'consequently' => 'следовательно',
            'therefore' => 'поэтому',
            'meanwhile' => 'тем временем',
            'otherwise' => 'иначе',
            'eventually' => 'в итоге',
            'originally' => 'изначально',
            'previously' => 'ранее',
            'currently' => 'сейчас',
            'recently' => 'недавно',
            'sometimes' => 'иногда',
            'usually' => 'обычно',
            'probably' => 'вероятно',
            'perhaps' => 'возможно',
            'definitely' => 'определённо',
            'certainly' => 'конечно',
            'obviously' => 'очевидно',
            'basically' => 'в основном',
            'actually' => 'на самом деле',
            'especially' => 'особенно',
            'generally' => 'обычно',
            'finally' => 'наконец',
            'however' => 'однако',
            'although' => 'хотя',
            'though' => 'хотя',
            'unless' => 'если не',
            'whether' => 'ли',
            'instead' => 'вместо этого',
            'besides' => 'кроме того',
            'including' => 'включая',
            'excluding' => 'исключая',
            'regarding' => 'относительно',
            'concerning' => 'касательно',
            'during' => 'во время',
            'between' => 'между',
            'through' => 'через',
            'within' => 'в пределах',
            'without' => 'без',
            'against' => 'против',
            'towards' => 'к',
            'among' => 'среди',
            'behind' => 'позади',
            'beyond' => 'за пределами',
            'despite' => 'несмотря на',
            'throughout' => 'на протяжении',
            'something' => 'что-то',
            'nothing' => 'ничего',
            'everything' => 'всё',
            'anything' => 'что угодно',
            'somewhere' => 'где-то',
            'everywhere' => 'везде',
            'anywhere' => 'где угодно',
            'desired' => 'подходящими',
            'desirable' => 'подходящим',
            'garnish' => 'гарниром',
            'garnishes' => 'гарнирами',
            'proper' => 'правильным',
            'needed' => 'нужными',
            'necessary' => 'необходимыми',
            'ingredients' => 'ингредиентами',
            'recipe' => 'рецептом',
            'vegetables' => 'овощи',
            'breakfast' => 'завтрак',
            'lunch' => 'обед',
            'dinner' => 'ужин',
            'maybe' => 'возможно',
            'already' => 'уже',
            'still' => 'всё ещё',
            'again' => 'снова',
            'quite' => 'весьма',
            'rather' => 'скорее',
            'almost' => 'почти',
            'never' => 'никогда',
            'always' => 'всегда',
            'often' => 'часто',
            'then' => 'затем',
            'also' => 'также',
            'even' => 'даже',
            'just' => 'просто',
            'only' => 'только',
            'yet' => 'ещё',
            'because' => 'потому что',
            'since' => 'так как',
            'until' => 'пока не',
            'while' => 'пока',
            'before' => 'до',
            'after' => 'после',
            'under' => 'под',
            'above' => 'над',
            'below' => 'ниже',
            'meat' => 'мясо',
            'where' => 'где',
            'when' => 'когда',
            'what' => 'что',
            'which' => 'который',
            'who' => 'кто',
            'how' => 'как',
            'why' => 'почему',
            'here' => 'здесь',
            'there' => 'там',
            'with' => 'с',
            'from' => 'из',
            'into' => 'в',
            'about' => 'о',
            'onto' => 'на',
            'upon' => 'на',
            'and' => 'и',
            'or' => 'или',
            'but' => 'но',
            'not' => 'не',
            'so' => 'так что',
            'if' => 'если',
            'as' => 'как',
        ];

        uksort($words, static fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));

        $out = $phrases;
        foreach ($words as $en => $ru) {
            $out['/\b' . preg_quote($en, '/') . '\b/ui'] = $ru;
        }

        return $out;
    }

    /**
     * Оставшиеся латинские слова (модель всё равно вставила) — заменяем на «затем»,
     * кроме слов, целиком взятых из заголовка заметки.
     */
    private function stripStrayLatinWords(string $text, ?string $titleForAllowlist): string
    {
        $allowed = [];
        if ($titleForAllowlist !== null && preg_match_all('/[a-zA-Z]{2,}/u', $titleForAllowlist, $m)) {
            foreach ($m[0] as $w) {
                $allowed[mb_strtolower($w)] = true;
            }
        }

        $text = preg_replace_callback('/\b([a-z]{4,})\b/u', static function (array $m) use ($allowed): string {
            $w = $m[1];
            if (isset($allowed[$w])) {
                return $m[0];
            }

            return 'затем';
        }, $text);

        return preg_replace_callback('/\b([A-Z][a-z]{3,})\b/u', static function (array $m) use ($allowed): string {
            $w = mb_strtolower($m[1]);
            if (isset($allowed[$w])) {
                return $m[0];
            }

            return 'Затем';
        }, $text);
    }

    /**
     * Извлечь один из допустимых статусов из текста ответа модели.
     */
    private function normalizeSuggestedStatus(string $raw): ?string
    {
        $normalized = mb_strtolower(trim($raw));
        $normalized = preg_replace('/\s+/u', ' ', $normalized) ?? '';

        if (preg_match('/\b(pending|in_progress|completed)\b/u', $normalized, $m)) {
            return $m[1];
        }

        if (preg_match('/\bin\s+progress\b/u', $normalized)) {
            return 'in_progress';
        }

        return null;
    }
}