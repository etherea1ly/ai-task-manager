<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Демо — задачи</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'Arial', 'sans-serif'],
                        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace'],
                    },
                    colors: {
                        deep: {
                            bg: '#05030B',
                            bg2: '#0B0820',
                            card: '#0E0A2A',
                            line: 'rgba(255,255,255,.08)',
                            text: '#F2EFFF',
                            muted: 'rgba(242,239,255,.74)',
                            faint: 'rgba(242,239,255,.54)',
                            brand: '#8B5CF6',
                            brand2: '#A78BFA',
                            success: '#34D399',
                            danger: '#FB7185',
                        }
                    },
                    boxShadow: {
                        deep: '0 20px 60px rgba(0,0,0,.55)',
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root { color-scheme: dark; }
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 9999px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,.25); }
    </style>
</head>
<body class="min-h-screen bg-deep-bg text-deep-text font-sans">
<div class="relative min-h-screen overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-56 left-1/2 h-[560px] w-[980px] -translate-x-1/2 rounded-full bg-gradient-to-r from-deep-brand/28 via-fuchsia-400/10 to-deep-brand2/22 blur-3xl"></div>
        <div class="absolute -bottom-56 left-0 h-[440px] w-[560px] rounded-full bg-deep-brand2/12 blur-3xl"></div>
        <div class="absolute -bottom-64 right-0 h-[500px] w-[620px] rounded-full bg-violet-400/10 blur-3xl"></div>
    </div>

    <header class="relative mx-auto flex max-w-5xl items-center justify-between px-4 py-6">
        <a href="/ai-demo" class="group inline-flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 ring-1 ring-white/10 shadow-deep">
                <span class="h-2.5 w-2.5 rounded-full bg-deep-brand2"></span>
            </span>
            <span class="text-sm font-semibold tracking-tight">AI Task Manager</span>
            <span class="hidden rounded-full bg-white/5 px-2 py-0.5 text-[11px] text-deep-faint ring-1 ring-white/10 sm:inline">demo</span>
        </a>
        <div class="flex items-center gap-2">
            <div class="hidden text-[12px] text-deep-faint sm:block font-mono">/ai-demo</div>
            <a href="/" class="rounded-lg bg-white/5 px-3 py-2 text-xs font-semibold text-deep-muted ring-1 ring-white/10 hover:bg-white/10">Главная</a>
        </div>
    </header>

    <main class="relative mx-auto max-w-5xl px-4 pb-12">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Создание задач с AI</h1>
                <p class="mt-1 text-sm text-deep-muted">Сгенерируйте описание, выберите статус и сохраните. Список подтягивается из API автоматически.</p>
            </div>
            <div id="status" class="hidden max-w-full rounded-xl bg-white/5 px-4 py-3 text-sm ring-1 ring-white/10 sm:max-w-[52%] sm:truncate"></div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <section class="rounded-2xl bg-white/5 p-5 shadow-deep ring-1 ring-white/10 backdrop-blur">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-semibold">Новая задача</div>
                    <div class="text-[12px] text-deep-faint font-mono">
                        AI: POST /api/tasks/generate-description
                    </div>
                </div>
                <div class="mt-4">
                    <form id="taskForm" class="space-y-4">
            <div>
                <label for="title" class="block text-[11px] font-semibold uppercase tracking-wide text-deep-faint">Название</label>
                <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                    <input
                        id="title"
                        name="title"
                        type="text"
                        autocomplete="off"
                        placeholder="Например: Подготовить отчёт по продажам"
                        class="w-full flex-1 rounded-xl bg-deep-bg2/70 px-4 py-3 text-sm text-deep-text placeholder:text-deep-faint ring-1 ring-inset ring-white/10 focus:outline-none focus:ring-2 focus:ring-deep-brand/60"
                        required
                    />
                    <button
                        id="aiBtn"
                        type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-deep-brand to-deep-brand2 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-deep-brand/60 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                    >
                        <span id="aiBtnLabel">Сгенерировать AI</span>
                        <span id="aiSpinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                    </button>
                </div>
            </div>

            <div>
                <label for="description" class="block text-[11px] font-semibold uppercase tracking-wide text-deep-faint">Описание</label>
                <div class="mt-2">
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Можно сгенерировать через AI или ввести вручную…"
                        class="w-full resize-none rounded-xl bg-deep-bg2/70 px-4 py-3 text-sm text-deep-text placeholder:text-deep-faint ring-1 ring-inset ring-white/10 focus:outline-none focus:ring-2 focus:ring-deep-brand/60"
                    ></textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 sm:items-end">
                <div class="sm:col-span-1">
                    <label for="statusSelect" class="block text-[11px] font-semibold uppercase tracking-wide text-deep-faint">Статус</label>
                    <select
                        id="statusSelect"
                        name="status"
                        class="mt-2 w-full rounded-xl bg-deep-bg2/70 px-4 py-3 text-sm text-deep-text ring-1 ring-inset ring-white/10 focus:outline-none focus:ring-2 focus:ring-deep-brand/60"
                    >
                        <option value="pending" selected>pending</option>
                        <option value="in_progress">in_progress</option>
                        <option value="completed">completed</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <button
                    id="saveBtn"
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white/10 px-4 py-3 text-sm font-semibold text-deep-text ring-1 ring-white/10 transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-deep-brand/60 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <span id="saveBtnLabel">Сохранить задачу</span>
                    <span id="saveSpinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                </button>
                <div class="text-xs text-discord-faint">
                    Enter — сохранить, AI — справа от title
                </div>
                </div>
            </div>
        </form>
                </div>
            </section>

            <section class="rounded-2xl bg-white/5 p-5 shadow-deep ring-1 ring-white/10 backdrop-blur">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm font-semibold">Все задачи</div>
                        <div id="tasksMeta" class="mt-1 text-xs text-deep-faint"></div>
                    </div>
                    <button
                        id="refreshBtn"
                        type="button"
                        class="rounded-xl bg-white/10 px-4 py-2.5 text-xs font-semibold text-deep-text ring-1 ring-white/10 transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-deep-brand/60"
                    >
                        Обновить
                    </button>
                </div>

                <div id="tasksList" class="mt-4 space-y-3"></div>
                <div id="tasksEmpty" class="mt-4 hidden rounded-xl bg-deep-bg2/60 px-4 py-4 text-sm text-deep-muted ring-1 ring-white/10">
                    Пока нет задач. Создайте первую слева.
                </div>

                <div class="mt-4 rounded-xl bg-deep-bg2/60 p-4 ring-1 ring-white/10">
                    <div class="text-xs font-semibold text-deep-muted">Подсказка</div>
                    <div class="mt-1 text-xs text-deep-faint">
                        Если AI вернёт 503 — запустите Ollama и проверьте модель (<span class="font-mono">ollama serve</span>, <span class="font-mono">ollama pull llama3.2</span>).
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>

<script>
    const form = document.getElementById('taskForm');
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const statusSelect = document.getElementById('statusSelect');
    const statusEl = document.getElementById('status');
    const saveBtn = document.getElementById('saveBtn');
    const saveSpinner = document.getElementById('saveSpinner');
    const saveBtnLabel = document.getElementById('saveBtnLabel');
    const aiBtn = document.getElementById('aiBtn');
    const aiSpinner = document.getElementById('aiSpinner');
    const aiBtnLabel = document.getElementById('aiBtnLabel');

    const refreshBtn = document.getElementById('refreshBtn');
    const tasksListEl = document.getElementById('tasksList');
    const tasksEmptyEl = document.getElementById('tasksEmpty');
    const tasksMetaEl = document.getElementById('tasksMeta');

    function setBusy(target, isBusy) {
        if (target === 'save') {
            saveBtn.disabled = isBusy;
            saveSpinner.classList.toggle('hidden', !isBusy);
            saveBtnLabel.textContent = isBusy ? 'Сохраняем…' : 'Сохранить задачу';
            return;
        }
        if (target === 'ai') {
            aiBtn.disabled = isBusy;
            aiSpinner.classList.toggle('hidden', !isBusy);
            aiBtnLabel.textContent = isBusy ? 'Генерируем…' : 'Сгенерировать AI';
            return;
        }
    }

    function showStatus(message, kind = 'info') {
        statusEl.classList.remove('hidden');
        statusEl.textContent = message;
        statusEl.dataset.kind = kind;

        statusEl.classList.remove('text-deep-text', 'text-deep-muted', 'ring-white/10', 'ring-deep-danger/40', 'ring-deep-success/40', 'ring-deep-brand/40');
        if (kind === 'success') {
            statusEl.classList.add('text-deep-text', 'ring-deep-success/40');
        } else if (kind === 'error') {
            statusEl.classList.add('text-deep-text', 'ring-deep-danger/40');
        } else {
            statusEl.classList.add('text-deep-muted', 'ring-deep-brand/40');
        }
    }

    function hideStatus() {
        statusEl.classList.add('hidden');
        statusEl.textContent = '';
        statusEl.dataset.kind = '';
    }

    function badgeForStatus(status) {
        const base = 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1';
        if (status === 'completed') return { cls: `${base} bg-deep-success/15 text-deep-text ring-deep-success/25`, label: 'completed' };
        if (status === 'in_progress') return { cls: `${base} bg-deep-brand/15 text-deep-text ring-deep-brand/25`, label: 'in_progress' };
        return { cls: `${base} bg-white/10 text-deep-text ring-white/10`, label: 'pending' };
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function renderTasks(tasks) {
        tasksListEl.innerHTML = '';
        const count = Array.isArray(tasks) ? tasks.length : 0;
        tasksMetaEl.textContent = `Найдено задач: ${count}`;

        if (!count) {
            tasksEmptyEl.classList.remove('hidden');
            return;
        }
        tasksEmptyEl.classList.add('hidden');

        for (const t of tasks) {
            const b = badgeForStatus(t.status);
            const el = document.createElement('div');
            el.className = 'rounded-xl bg-deep-bg2/60 p-4 ring-1 ring-white/10';
            el.innerHTML = `
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-deep-text">${escapeHtml(t.title)}</div>
                        <div class="mt-1 whitespace-pre-wrap text-sm text-deep-muted">${escapeHtml(t.description || '')}</div>
                    </div>
                    <div class="${b.cls}">${b.label}</div>
                </div>
            `;
            tasksListEl.appendChild(el);
        }
    }

    async function fetchTasks() {
        try {
            tasksMetaEl.textContent = 'Загружаем…';
            const res = await fetch('/api/tasks', { headers: { 'Accept': 'application/json' } });
            const payload = await res.json().catch(() => []);
            if (!res.ok) {
                const msg = payload?.message || `Ошибка API (HTTP ${res.status})`;
                showStatus(msg, 'error');
                renderTasks([]);
                return;
            }
            renderTasks(payload);
        } catch {
            showStatus('Не удалось загрузить задачи. Проверьте, что сервер запущен.', 'error');
            renderTasks([]);
        }
    }

    async function generateAiDescription() {
        hideStatus();
        const title = (titleInput.value || '').trim();
        if (!title) {
            showStatus('Введите название задачи', 'error');
            titleInput.focus();
            return;
        }

        setBusy('ai', true);
        showStatus('Генерируем описание…', 'info');

        try {
            const res = await fetch('/api/tasks/generate-description', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ title }),
            });

            const payload = await res.json().catch(() => ({}));
            if (!res.ok) {
                const msg = payload?.error || payload?.message || `Ошибка API (HTTP ${res.status})`;
                showStatus(msg, 'error');
                return;
            }

            descriptionInput.value = payload?.description || '';
            showStatus('Описание заполнено', 'success');
        } catch {
            showStatus('Не удалось выполнить запрос. Проверьте, что сервер запущен.', 'error');
        } finally {
            setBusy('ai', false);
        }
    }

    aiBtn.addEventListener('click', generateAiDescription);
    refreshBtn.addEventListener('click', fetchTasks);

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideStatus();

        const title = (titleInput.value || '').trim();
        const description = (descriptionInput.value || '').trim();
        const status = (statusSelect.value || '').trim() || 'pending';

        if (!title) {
            showStatus('Введите название задачи', 'error');
            titleInput.focus();
            return;
        }

        setBusy('save', true);
        showStatus('Сохраняем задачу…', 'info');

        try {
            const res = await fetch('/api/tasks', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ title, description: description || null, status }),
            });

            const payload = await res.json().catch(() => ({}));
            if (!res.ok) {
                const msg =
                    payload?.message ||
                    (payload?.errors ? Object.values(payload.errors).flat().join(' ') : null) ||
                    `Ошибка API (HTTP ${res.status})`;
                showStatus(msg, 'error');
                return;
            }

            showStatus('Задача создана', 'success');
            titleInput.value = '';
            descriptionInput.value = '';
            statusSelect.value = 'pending';
            await fetchTasks();
        } catch {
            showStatus('Не удалось сохранить задачу. Проверьте, что сервер запущен.', 'error');
        } finally {
            setBusy('save', false);
        }
    });

    fetchTasks();
</script>
</body>
</html>

