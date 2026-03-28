<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Демо — задачи</title>
    <script>
        (function () {
            var k = 'ai-demo-theme';
            var v = localStorage.getItem(k);
            if (v === 'dark') document.documentElement.classList.add('dark');
            else if (v === 'light') document.documentElement.classList.remove('dark');
            else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @import "tailwindcss";
        @custom-variant dark (&:where(.dark, .dark *));
        @theme {
            --font-sans: "Inter", ui-sans-serif, system-ui, "Segoe UI", Arial, sans-serif;
            --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            --shadow-deep: 0 24px 64px rgb(0 0 0 / 0.5), 0 0 48px rgb(34 211 238 / 0.12);
            --color-deep-bg: #020617;
            --color-deep-bg2: #0f172a;
            --color-deep-card: #1e293b;
            --color-deep-line: rgb(34 211 238 / 0.14);
            --color-deep-text: #ecfeff;
            --color-deep-muted: rgb(207 250 254 / 0.78);
            --color-deep-faint: rgb(165 243 252 / 0.55);
            --color-deep-brand: #22d3ee;
            --color-deep-brand2: #f472b6;
            --color-deep-success: #34d399;
            --color-deep-danger: #fb7185;
        }
    </style>
    <style>
        :root { color-scheme: light; }
        html.dark { color-scheme: dark; }

        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-thumb {
            background: rgba(168, 162, 158, 0.4);
            border-radius: 9999px;
        }
        html.dark ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgb(34 211 238 / 0.35), rgb(244 114 182 / 0.25));
            border: 2px solid rgb(15 23 42 / 0.5);
        }
        ::-webkit-scrollbar-track { background: rgba(231, 229, 223, 0.85); }
        html.dark ::-webkit-scrollbar-track { background: rgba(2, 6, 23, 0.6); }

        a, button, input, textarea, select {
            transition-property: color, background-color, border-color, box-shadow, transform, opacity, filter;
            transition-duration: 250ms;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        @media (prefers-reduced-motion: reduce) {
            a, button, input, textarea, select, .task-card-interactive {
                transition-duration: 0.01ms !important;
            }
            .task-card-interactive:hover { transform: none !important; }
        }

        .task-card-interactive {
            transition-property: transform, box-shadow, border-color, background-color;
            transition-duration: 300ms;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        @media (hover: hover) and (pointer: fine) {
            .task-card-interactive:hover {
                transform: translateY(-4px);
            }
        }

        .notebook-shell {
            transition: background-color 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
        }

        html.dark .notebook-shell {
            box-shadow:
                var(--shadow-deep),
                inset 0 1px 0 rgb(255 255 255 / 0.06);
        }

        @keyframes toast-in {
            from { opacity: 0; transform: translateY(12px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes toast-out {
            from { opacity: 1; transform: translateY(0) scale(1); }
            to { opacity: 0; transform: translateY(-8px) scale(0.98); }
        }
        .toast-animate-in {
            animation: toast-in 0.38s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
        .toast-animate-out {
            animation: toast-out 0.28s ease forwards;
        }
        @media (prefers-reduced-motion: reduce) {
            .toast-animate-in, .toast-animate-out { animation: none; }
        }

    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-cyan-50/90 via-white to-fuchsia-50/75 text-stone-800 font-sans dark:from-deep-bg dark:via-[#0a1628] dark:to-[#150a1c] dark:text-deep-text">
<div class="relative min-h-screen overflow-hidden">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-56 left-1/2 h-[560px] w-[980px] -translate-x-1/2 rounded-full bg-gradient-to-r from-cyan-200/35 via-white/40 to-fuchsia-200/30 blur-3xl dark:from-deep-brand/35 dark:via-fuchsia-500/12 dark:to-deep-brand2/28"></div>
        <div class="absolute -bottom-56 left-0 h-[440px] w-[560px] rounded-full bg-cyan-200/25 blur-3xl dark:bg-deep-brand2/18"></div>
        <div class="absolute -bottom-64 right-0 h-[500px] w-[620px] rounded-full bg-fuchsia-200/22 blur-3xl dark:bg-deep-brand/15"></div>
    </div>

    <header class="relative mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 px-4 py-6">
        <a href="/ai-demo" class="group inline-flex min-h-[44px] min-w-0 items-center gap-2 sm:min-h-0 sm:gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/70 shadow-sm ring-1 ring-cyan-200/50 backdrop-blur-md dark:bg-white/5 dark:shadow-[0_0_20px_-4px_rgb(34_211_238/0.45)] dark:ring-deep-brand/35">
                <span class="h-2.5 w-2.5 rounded-full bg-cyan-400 shadow-[0_0_10px_rgb(34_211_238/0.9)] dark:bg-deep-brand dark:shadow-[0_0_12px_rgb(34_211_238/0.85)]"></span>
            </span>
            <span class="truncate text-sm font-semibold tracking-tight text-stone-800 dark:text-deep-text">AI Task Manager</span>
            <span class="hidden rounded-full bg-stone-50/85 px-2 py-0.5 text-[11px] text-stone-500 ring-1 ring-stone-200/55 dark:bg-white/5 dark:text-deep-faint dark:ring-white/10 sm:inline">demo</span>
        </a>
        <div class="flex w-full shrink-0 items-center justify-end gap-2 sm:w-auto">
            <button
                type="button"
                id="themeToggle"
                class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl bg-white/65 px-3 text-stone-600 shadow-sm ring-1 ring-cyan-200/45 backdrop-blur-md hover:bg-white/90 focus:outline-none focus:ring-2 focus:ring-cyan-400/40 dark:bg-white/10 dark:text-deep-muted dark:shadow-[0_0_24px_-8px_rgb(34_211_238/0.35)] dark:ring-deep-brand/30 dark:hover:bg-white/15 dark:focus:ring-deep-brand/50"
                aria-label="Включить тёмную тему"
            >
                <span id="themeIconLight" class="hidden h-5 w-5 shrink-0 dark:inline [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /></svg>
                </span>
                <span id="themeIconDark" class="inline h-5 w-5 shrink-0 dark:hidden [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" /></svg>
                </span>
                <span id="themeToggleLabel" class="text-xs font-semibold whitespace-nowrap">Тёмная тема</span>
            </button>
            <div class="hidden text-[12px] text-stone-500 dark:text-deep-faint md:block font-mono">/ai-demo</div>
            <a href="/" class="inline-flex min-h-[44px] items-center rounded-lg bg-white/65 px-3 py-2 text-xs font-semibold text-stone-600 shadow-sm ring-1 ring-fuchsia-200/40 backdrop-blur-md hover:bg-white/90 dark:bg-white/5 dark:text-deep-muted dark:ring-deep-brand2/25 dark:hover:bg-white/10 dark:hover:shadow-[0_0_20px_-6px_rgb(244_114_182/0.35)]">Главная</a>
        </div>
    </header>

    <main class="relative mx-auto max-w-5xl px-3 pb-12 sm:px-4">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <p class="text-sm text-stone-600 dark:text-deep-muted">Сверху — сохранённые карточки. Ниже AI сформирует текст по заголовку; сохраните заметку в список.</p>
            <div id="status" class="hidden max-w-full rounded-xl px-4 py-3 text-sm backdrop-blur-xl sm:max-w-[52%] sm:truncate"></div>
        </div>

        <div class="notebook-shell overflow-hidden rounded-2xl border border-cyan-200/40 bg-white/55 shadow-xl shadow-cyan-500/10 ring-1 ring-white/80 backdrop-blur-2xl dark:border-deep-brand/25 dark:bg-deep-bg2/70 dark:shadow-deep dark:ring-deep-brand/20">
            <button
                type="button"
                id="notebookToggle"
                class="flex w-full cursor-pointer flex-col gap-2 border-b border-cyan-200/35 bg-gradient-to-b from-white/80 to-cyan-50/40 px-4 py-3 text-left backdrop-blur-md transition hover:from-white hover:to-fuchsia-50/35 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/40 dark:border-deep-brand/20 dark:from-white/[0.07] dark:to-deep-bg2/50 dark:hover:from-white/[0.12] dark:hover:to-deep-bg2/60 dark:focus-visible:ring-deep-brand/50 sm:flex-row sm:items-center sm:justify-between sm:px-5"
                aria-expanded="true"
                aria-controls="notebookPanel"
            >
                <div class="flex min-w-0 flex-1 items-center gap-3">
                    <div class="hidden shrink-0 gap-1.5 sm:flex" aria-hidden="true">
                        <span class="h-2.5 w-2.5 rounded-full bg-cyan-400/80 shadow-[0_0_8px_rgb(34_211_238/0.7)] ring-1 ring-cyan-300/40 dark:bg-deep-brand dark:shadow-[0_0_10px_rgb(34_211_238/0.55)]"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-fuchsia-400/70 shadow-[0_0_8px_rgb(244_114_182/0.55)] ring-1 ring-fuchsia-300/35 dark:bg-deep-brand2 dark:shadow-[0_0_10px_rgb(244_114_182/0.45)]"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-cyan-300/60 ring-1 ring-cyan-200/40 dark:bg-white/25 dark:ring-white/15"></span>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-2xl font-semibold leading-tight tracking-tight text-stone-800 dark:text-deep-text sm:text-3xl">Заметки и задачи</h1>
                        <p class="mt-0.5 text-xs text-stone-500 dark:text-deep-faint">с интеграцией AI <span class="font-mono opacity-80">(Ollama)</span></p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center justify-between gap-3 sm:mt-0 sm:justify-end">
                    <span class="pointer-events-none rounded-full bg-gradient-to-r from-cyan-400/15 to-fuchsia-400/15 px-2.5 py-1 text-[11px] font-semibold text-cyan-900/90 ring-1 ring-cyan-300/40 dark:bg-gradient-to-r dark:from-deep-brand/25 dark:to-deep-brand2/20 dark:text-cyan-100 dark:ring-deep-brand/40">AI → описание</span>
                    <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/70 text-stone-500 ring-1 ring-cyan-200/45 backdrop-blur-sm dark:bg-white/10 dark:text-deep-muted dark:ring-deep-brand/25" aria-hidden="true">
                        <svg id="notebookChevronIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-200 ease-out" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                    </span>
                </div>
            </button>

            <div id="notebookPanel" class="flex flex-col">
                <aside class="flex flex-col border-b border-cyan-200/30 bg-gradient-to-b from-cyan-50/30 to-fuchsia-50/20 p-4 backdrop-blur-xl dark:border-deep-brand/15 dark:from-white/[0.04] dark:to-deep-bg2/30 sm:p-5">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-stone-800 dark:text-deep-text">Карточки в папке</div>
                            <div id="tasksMeta" class="mt-1 text-xs text-stone-500 dark:text-deep-faint"></div>
                        </div>
                        <button
                            id="refreshBtn"
                            type="button"
                            class="min-h-[44px] shrink-0 rounded-xl bg-white/75 px-3 py-2 text-xs font-semibold text-stone-700 ring-1 ring-cyan-200/50 backdrop-blur-md transition hover:bg-white focus:outline-none focus:ring-2 focus:ring-cyan-400/40 dark:bg-white/10 dark:text-deep-text dark:shadow-[0_0_20px_-8px_rgb(34_211_238/0.3)] dark:ring-deep-brand/30 dark:hover:bg-white/15 dark:focus:ring-deep-brand/55"
                        >
                            Обновить
                        </button>
                    </div>

                    <div id="tasksList" class="mt-4 flex max-h-[min(50vh,420px)] flex-col gap-3 overflow-y-auto pr-1 sm:max-h-[min(56vh,520px)]"></div>
                    <div id="tasksEmpty" class="mt-4 hidden rounded-xl border border-dashed border-cyan-300/45 bg-white/50 px-4 py-5 text-center text-sm text-stone-600 backdrop-blur-sm dark:border-deep-brand/25 dark:bg-deep-card/50 dark:text-deep-muted">
                        Пока пусто — создайте заметку внизу страницы.
                    </div>

                    <div class="mt-4 rounded-xl border border-fuchsia-200/35 bg-white/45 p-3 text-xs text-stone-500 ring-1 ring-cyan-100/60 backdrop-blur-md dark:border-deep-brand2/20 dark:bg-deep-card/40 dark:text-deep-faint dark:ring-deep-brand/15">
                        <span class="font-semibold text-stone-600 dark:text-deep-muted">Подсказка:</span>
                        если AI ответит 503 — запустите Ollama (<span class="font-mono">ollama serve</span>).
                    </div>
                </aside>
            </div>

                <section class="relative min-h-[280px] border-t border-cyan-200/30 dark:border-deep-brand/10">
                    <div id="aiFormOverlay" class="pointer-events-none absolute inset-0 z-10 hidden flex-col items-center justify-center gap-4 bg-white/75 backdrop-blur-xl dark:bg-deep-bg/70">
                        <span class="h-11 w-11 animate-spin rounded-full border-2 border-cyan-200/90 border-t-cyan-500 shadow-[0_0_20px_rgb(34_211_238/0.45)] dark:border-deep-brand/30 dark:border-t-deep-brand dark:shadow-[0_0_24px_rgb(34_211_238/0.4)]"></span>
                        <span class="text-sm font-medium text-stone-600 dark:text-deep-muted">Создаём описание…</span>
                    </div>

                    <form id="taskForm" class="relative z-0 space-y-5 px-4 pb-6 pt-5 sm:px-6">
                        <div>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                                <label for="title" class="text-base font-semibold text-stone-700 dark:text-deep-muted">Заголовок заметки</label>
                                <span class="hidden max-w-full break-all text-[10px] text-stone-400 sm:block sm:text-right dark:text-deep-faint font-mono">POST …/generate-description</span>
                            </div>
                            <div class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-stretch">
                                <input
                                    id="title"
                                    name="title"
                                    type="text"
                                    autocomplete="off"
                                    placeholder="Тема заметки — затем «Создать задачу», текст появится в поле ниже"
                                    class="min-h-[44px] w-full flex-1 rounded-xl border-0 bg-white/80 px-4 py-3 text-sm text-stone-800 shadow-inner shadow-cyan-500/5 ring-1 ring-inset ring-cyan-200/55 placeholder:text-stone-400 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-cyan-400/45 dark:bg-deep-card/60 dark:text-deep-text dark:placeholder:text-deep-faint dark:ring-deep-brand/25 dark:focus:ring-deep-brand/55"
                                    required
                                />
                                <button
                                    id="aiBtn"
                                    type="button"
                                    class="inline-flex min-h-[44px] shrink-0 items-center justify-center gap-2 self-stretch rounded-xl bg-gradient-to-r from-cyan-500 via-cyan-400 to-fuchsia-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-cyan-500/25 transition hover:brightness-[1.06] focus:outline-none focus:ring-2 focus:ring-cyan-300/60 disabled:cursor-not-allowed disabled:opacity-60 dark:shadow-[0_0_28px_-6px_rgb(34_211_238/0.45),0_0_20px_-8px_rgb(244_114_182/0.35)] dark:focus:ring-deep-brand/70 sm:w-auto sm:self-auto"
                                >
                                    <span id="aiBtnLabel">Создать задачу</span>
                                    <span id="aiSpinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                                </button>
                            </div>
                        </div>

                        <div class="border-t border-cyan-200/35 pt-5 dark:border-white/10">
                            <label for="description" class="mb-2 flex flex-wrap items-center gap-2 text-base font-semibold text-stone-700 dark:text-deep-muted">
                                Текст
                                <span class="rounded-md bg-cyan-100/80 px-1.5 py-0.5 text-[10px] font-normal uppercase tracking-wide text-cyan-900/80 dark:bg-deep-brand/15 dark:text-deep-faint">только AI</span>
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="6"
                                readonly
                                aria-readonly="true"
                                placeholder="Здесь появится описание после «Создать задачу» по заголовку выше…"
                                class="w-full cursor-default resize-y rounded-xl border border-cyan-200/50 bg-white/60 px-4 py-3 text-sm leading-relaxed text-stone-700 shadow-inner shadow-cyan-500/5 selection:bg-cyan-100/90 dark:border-deep-brand/20 dark:bg-deep-card/50 dark:text-deep-text dark:placeholder:text-deep-faint dark:selection:bg-deep-brand/20"
                            ></textarea>
                        </div>

                        <div class="flex flex-col gap-4 border-t border-fuchsia-200/30 pt-4 dark:border-deep-brand2/15 sm:flex-row sm:items-end sm:justify-between">
                            <div class="w-full sm:w-44">
                                <label for="statusSelect" class="block text-[11px] font-semibold uppercase tracking-wide text-stone-500 dark:text-deep-faint">Статус</label>
                                <select
                                    id="statusSelect"
                                    name="status"
                                    class="mt-2 min-h-[44px] w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm text-stone-800 ring-1 ring-inset ring-cyan-200/55 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-cyan-400/45 dark:bg-deep-card/60 dark:text-deep-text dark:ring-deep-brand/25 dark:focus:ring-deep-brand/60"
                                >
                                    <option value="pending" selected>Ожидает</option>
                                    <option value="in_progress">В работе</option>
                                    <option value="completed">Выполнено</option>
                                </select>
                            </div>
                            <div class="flex flex-1 flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                                <p class="order-2 text-xs text-stone-500 dark:text-deep-faint sm:order-1 sm:mr-3 sm:max-w-[220px] sm:text-right">
                                    Enter — сохранить. Поле «Текст» только из AI, редактировать нельзя.
                                </p>
                                <button
                                    id="saveBtn"
                                    type="submit"
                                    class="order-1 inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl border border-white/10 bg-stone-800 px-5 py-3 text-sm font-semibold text-stone-50 shadow-lg shadow-stone-900/20 ring-1 ring-cyan-900/20 transition hover:bg-stone-900 focus:outline-none focus:ring-2 focus:ring-cyan-400/35 disabled:cursor-not-allowed disabled:opacity-60 dark:border-deep-brand/30 dark:bg-white/10 dark:shadow-[0_0_28px_-8px_rgb(244_114_182/0.25)] dark:ring-deep-brand2/25 dark:hover:bg-white/18 dark:focus:ring-deep-brand/55"
                                >
                                    <span id="saveBtnLabel">Сохранить в список</span>
                                    <span id="saveSpinner" class="hidden h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </section>
        </div>
    </main>
</div>

<div id="toastHost" class="pointer-events-none fixed bottom-6 left-1/2 z-[100] flex w-[min(92vw,22rem)] -translate-x-1/2 flex-col items-center gap-2 sm:bottom-8" aria-live="polite"></div>

<script>
    const THEME_KEY = 'ai-demo-theme';

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
    const aiFormOverlay = document.getElementById('aiFormOverlay');
    const themeToggle = document.getElementById('themeToggle');
    const themeToggleLabel = document.getElementById('themeToggleLabel');

    descriptionInput.addEventListener('paste', (e) => e.preventDefault());
    descriptionInput.addEventListener('drop', (e) => e.preventDefault());

    const refreshBtn = document.getElementById('refreshBtn');
    const tasksListEl = document.getElementById('tasksList');
    const tasksEmptyEl = document.getElementById('tasksEmpty');
    const tasksMetaEl = document.getElementById('tasksMeta');
    const toastHost = document.getElementById('toastHost');

    /** @type {Array<Record<string, *>>} */
    let tasksCache = [];

    /** Защита от повторных DELETE по одной задаче (двойной клик, гонка ответов). */
    const deleteInProgressIds = new Set();

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function showToast(message, variant = 'success') {
        const el = document.createElement('div');
        const base =
            'pointer-events-auto w-full rounded-xl px-4 py-3 text-center text-sm font-medium shadow-lg ring-1 backdrop-blur-md toast-animate-in';
        const variants = {
            success:
                'bg-emerald-50/95 text-emerald-900 ring-emerald-200/60 dark:bg-emerald-950/50 dark:text-emerald-100 dark:ring-emerald-400/35',
            error:
                'bg-rose-50/95 text-rose-900 ring-rose-200/60 dark:bg-rose-950/45 dark:text-rose-100 dark:ring-rose-400/40',
            info: 'bg-sky-50/95 text-sky-900 ring-sky-200/60 dark:bg-sky-950/45 dark:text-sky-100 dark:ring-sky-400/35',
        };
        el.className = `${base} ${variants[variant] || variants.info}`;
        el.textContent = message;
        toastHost.appendChild(el);
        window.setTimeout(() => {
            el.classList.remove('toast-animate-in');
            el.classList.add('toast-animate-out');
            const remove = () => el.remove();
            el.addEventListener('animationend', remove, { once: true });
            window.setTimeout(remove, 400);
        }, 2800);
    }

    function statusLabelRu(status) {
        if (status === 'completed') return 'Выполнено';
        if (status === 'in_progress') return 'В работе';
        return 'Ожидает';
    }

    function setAiStatusButtonLoading(btn, loading) {
        const label = btn.querySelector('.ai-status-btn-label');
        const spin = btn.querySelector('.ai-status-btn-spinner');
        btn.disabled = !!loading;
        if (label) label.classList.toggle('hidden', loading);
        if (spin) spin.classList.toggle('hidden', !loading);
    }

    function setTheme(isDark) {
        const root = document.documentElement;
        if (isDark) {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
        localStorage.setItem(THEME_KEY, isDark ? 'dark' : 'light');
        syncThemeToggleUi();
    }

    function syncThemeToggleUi() {
        const isDark = document.documentElement.classList.contains('dark');
        themeToggle.setAttribute('aria-label', isDark ? 'Включить светлую тему' : 'Включить тёмную тему');
        themeToggleLabel.textContent = isDark ? 'Светлая тема' : 'Тёмная тема';
    }

    themeToggle.addEventListener('click', function () {
        setTheme(!document.documentElement.classList.contains('dark'));
    });

    syncThemeToggleUi();

    const NOTEBOOK_COLLAPSE_KEY = 'ai-demo-notebook-collapsed';
    const notebookToggle = document.getElementById('notebookToggle');
    const notebookPanel = document.getElementById('notebookPanel');
    const notebookChevronIcon = document.getElementById('notebookChevronIcon');

    function applyNotebookCollapsed(collapsed) {
        notebookPanel.classList.toggle('hidden', collapsed);
        notebookToggle.setAttribute('aria-expanded', String(!collapsed));
        notebookToggle.setAttribute(
            'aria-label',
            collapsed ? 'Развернуть блок «Заметки и задачи»' : 'Свернуть блок «Заметки и задачи»'
        );
        notebookChevronIcon.classList.toggle('-rotate-90', collapsed);
    }

    notebookToggle.addEventListener('click', () => {
        const collapsed = !notebookPanel.classList.contains('hidden');
        applyNotebookCollapsed(collapsed);
        localStorage.setItem(NOTEBOOK_COLLAPSE_KEY, collapsed ? '1' : '0');
    });

    applyNotebookCollapsed(localStorage.getItem(NOTEBOOK_COLLAPSE_KEY) === '1');

    function setBusy(target, isBusy) {
        if (target === 'save') {
            saveBtn.disabled = isBusy;
            saveSpinner.classList.toggle('hidden', !isBusy);
            saveBtnLabel.textContent = isBusy ? 'Сохраняем…' : 'Сохранить в список';
            return;
        }
        if (target === 'ai') {
            aiBtn.disabled = isBusy;
            aiSpinner.classList.toggle('hidden', !isBusy);
            aiBtnLabel.textContent = isBusy ? 'Создаём…' : 'Создать задачу';
            aiFormOverlay.classList.toggle('hidden', !isBusy);
            aiFormOverlay.classList.toggle('flex', isBusy);
            return;
        }
    }

    function showStatus(message, kind = 'info') {
        statusEl.classList.remove('hidden');
        statusEl.textContent = message;
        statusEl.dataset.kind = kind;

        const base =
            'max-w-full rounded-xl px-4 py-3 text-sm backdrop-blur-xl sm:max-w-[52%] sm:truncate ring-1 shadow-lg';
        // classList.add() нельзя вызывать с одной строкой из нескольких классов — в браузере это InvalidCharacterError.
        if (kind === 'success') {
            statusEl.className = `${base} bg-emerald-50/80 text-emerald-900/95 ring-emerald-300/60 shadow-emerald-500/10 dark:bg-deep-card/55 dark:text-deep-text dark:ring-deep-success/45 dark:shadow-[0_0_24px_-6px_rgb(52_211_153/0.35)]`;
        } else if (kind === 'error') {
            statusEl.className = `${base} bg-rose-50/80 text-rose-900/95 ring-rose-300/55 shadow-rose-500/10 dark:bg-deep-card/55 dark:text-deep-text dark:ring-deep-danger/45 dark:shadow-[0_0_24px_-6px_rgb(251_113_133/0.35)]`;
        } else {
            statusEl.className = `${base} bg-cyan-50/70 text-stone-800 ring-cyan-200/55 shadow-cyan-500/8 dark:bg-deep-card/50 dark:text-deep-muted dark:ring-deep-brand/45 dark:shadow-[0_0_28px_-8px_rgb(34_211_238/0.25)]`;
        }
    }

    function hideStatus() {
        statusEl.classList.add('hidden');
        statusEl.textContent = '';
        statusEl.dataset.kind = '';
    }

    function badgeForStatus(status) {
        const base = 'task-status-badge inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 transition-colors duration-300';
        if (status === 'completed') {
            return {
                cls: `${base} bg-emerald-100 text-emerald-900 ring-emerald-300/80 shadow-sm dark:bg-emerald-500/20 dark:text-emerald-50 dark:ring-emerald-400/50 dark:shadow-[0_0_14px_-4px_rgb(52_211_153/0.45)]`,
                label: 'Выполнено',
            };
        }
        if (status === 'in_progress') {
            return {
                cls: `${base} bg-blue-100 text-blue-900 ring-blue-300/80 shadow-sm dark:bg-blue-500/25 dark:text-blue-50 dark:ring-blue-400/50 dark:shadow-[0_0_14px_-4px_rgb(59_130_246/0.45)]`,
                label: 'В работе',
            };
        }
        return {
            cls: `${base} bg-amber-100 text-amber-950 ring-amber-300/80 shadow-sm dark:bg-amber-400/20 dark:text-amber-50 dark:ring-amber-400/45 dark:shadow-[0_0_14px_-4px_rgb(251_191_36/0.4)]`,
            label: 'Ожидает',
        };
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
        tasksCache = Array.isArray(tasks) ? tasks : [];
        tasksListEl.innerHTML = '';
        const count = tasksCache.length;
        tasksMetaEl.textContent = `Найдено задач: ${count}`;

        if (!count) {
            tasksEmptyEl.classList.remove('hidden');
            return;
        }
        tasksEmptyEl.classList.add('hidden');

        for (const t of tasksCache) {
            const b = badgeForStatus(t.status);
            const el = document.createElement('div');
            el.className =
                'task-card-interactive cursor-default rounded-lg rounded-tl-md border border-cyan-200/45 bg-white/65 p-4 shadow-md shadow-cyan-500/10 ring-1 ring-white/90 backdrop-blur-md hover:border-cyan-300/70 hover:shadow-xl hover:shadow-cyan-400/15 dark:border-deep-brand/25 dark:bg-deep-card/65 dark:ring-deep-brand/15 dark:backdrop-blur-xl dark:hover:border-deep-brand2/40 dark:hover:shadow-[0_0_32px_-10px_rgb(34_211_238/0.35),0_0_24px_-12px_rgb(244_114_182/0.2)] dark:hover:ring-deep-brand/35';
            el.innerHTML = `
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div class="truncate text-sm font-semibold text-stone-800 dark:text-deep-text">${escapeHtml(t.title)}</div>
                            <div class="${b.cls}" data-role="task-status-badge">${b.label}</div>
                        </div>
                        <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:items-start sm:gap-3">
                            <div class="min-w-0 flex-1 whitespace-pre-wrap text-sm text-stone-600 dark:text-deep-muted">${escapeHtml(t.description || '')}</div>
                            <div class="flex shrink-0 flex-col gap-2 self-stretch sm:flex-row sm:items-start sm:gap-2">
                                <button type="button" class="ai-status-btn relative inline-flex min-h-[40px] min-w-[10.5rem] shrink-0 items-center justify-center gap-2 self-start rounded-lg border border-cyan-200/45 bg-white/85 px-3 py-2 text-xs font-semibold text-stone-700 ring-1 ring-cyan-200/50 backdrop-blur-sm transition hover:bg-white disabled:cursor-not-allowed disabled:opacity-65 dark:border-deep-brand/35 dark:bg-white/10 dark:text-deep-muted dark:ring-deep-brand/25 dark:hover:bg-white/15" data-task-id="${String(t.id)}">
                                    <span class="ai-status-btn-label">🤖 AI статус</span>
                                    <span class="ai-status-btn-spinner hidden h-4 w-4 shrink-0 animate-spin rounded-full border-2 border-cyan-200/80 border-t-cyan-600 dark:border-white/25 dark:border-t-deep-brand" aria-hidden="true"></span>
                                </button>
                                <button type="button" class="task-delete-btn inline-flex min-h-[40px] shrink-0 items-center justify-center gap-1.5 self-start rounded-lg border border-rose-200/80 bg-white/85 px-3 py-2 text-xs font-semibold text-rose-600 ring-1 ring-rose-200/60 backdrop-blur-sm transition hover:bg-rose-50 hover:text-rose-700 dark:border-rose-500/35 dark:bg-white/10 dark:text-rose-400 dark:ring-rose-500/25 dark:hover:bg-rose-950/40 dark:hover:text-rose-300" data-task-id="${String(t.id)}" aria-label="Удалить задачу">
                                    <span class="text-[1.05rem] leading-none" aria-hidden="true">❌</span>
                                    <span>Удалить</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            tasksListEl.appendChild(el);
        }
    }

    async function suggestAiStatusForTask(taskId, triggerBtn) {
        if (!triggerBtn) return;

        const task = tasksCache.find((x) => String(x.id) === String(taskId));
        if (!task) {
            showToast('Задача не найдена', 'error');
            return;
        }

        const btn = triggerBtn;

        setAiStatusButtonLoading(btn, true);
        hideStatus();

        try {
            const suggestRes = await fetch('/tasks/suggest-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    title: task.title,
                    description: task.description ?? '',
                }),
            });

            const suggestPayload = await suggestRes.json().catch(() => ({}));
            if (!suggestRes.ok) {
                const msg =
                    suggestPayload?.error ||
                    suggestPayload?.message ||
                    `Ошибка AI (HTTP ${suggestRes.status})`;
                showToast(msg, 'error');
                showStatus(msg, 'error');
                return;
            }

            const newStatus = suggestPayload?.status;
            if (!newStatus) {
                showToast('Ответ без статуса', 'error');
                return;
            }

            const putRes = await fetch(`/api/tasks/${encodeURIComponent(taskId)}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({ status: newStatus }),
            });

            const putPayload = await putRes.json().catch(() => ({}));
            if (!putRes.ok) {
                const msg =
                    putPayload?.message ||
                    (putPayload?.errors ? Object.values(putPayload.errors).flat().join(' ') : null) ||
                    `Ошибка сохранения (HTTP ${putRes.status})`;
                showToast(msg, 'error');
                showStatus(msg, 'error');
                return;
            }

            const prevStatus = task.status;
            const idx = tasksCache.findIndex((x) => String(x.id) === String(taskId));
            if (idx >= 0) {
                tasksCache[idx] = { ...tasksCache[idx], ...putPayload };
            }
            renderTasks(tasksCache);

            const oldLabel = statusLabelRu(prevStatus);
            const newLabel = statusLabelRu(newStatus);
            showToast(`Статус обновлён: ${oldLabel} → ${newLabel}`, 'success');
            showStatus('Статус сохранён', 'success');
        } catch {
            showToast('Сеть или сервер недоступны', 'error');
            showStatus('Не удалось выполнить запрос', 'error');
        } finally {
            if (btn && btn.isConnected) {
                setAiStatusButtonLoading(btn, false);
            }
        }
    }

    async function deleteTask(taskId, triggerBtn) {
        const key = String(taskId);
        if (deleteInProgressIds.has(key)) {
            return;
        }
        if (!window.confirm('Удалить эту задачу?')) {
            return;
        }
        if (deleteInProgressIds.has(key)) {
            return;
        }
        deleteInProgressIds.add(key);

        if (triggerBtn) {
            triggerBtn.disabled = true;
            triggerBtn.setAttribute('aria-busy', 'true');
        }

        hideStatus();
        try {
            const res = await fetch(`/api/tasks/${encodeURIComponent(taskId)}`, {
                method: 'DELETE',
                headers: { Accept: 'application/json' },
            });
            const payload = await res.json().catch(() => ({}));
            if (!res.ok) {
                const msg = payload?.message || `Ошибка удаления (HTTP ${res.status})`;
                showToast(msg, 'error');
                showStatus(msg, 'error');
                return;
            }
            tasksCache = tasksCache.filter((x) => String(x.id) !== String(taskId));
            renderTasks(tasksCache);
            showToast('Задача удалена', 'success');
            showStatus('Задача удалена', 'success');
        } catch {
            showToast('Не удалось удалить задачу', 'error');
            showStatus('Не удалось удалить задачу', 'error');
        } finally {
            deleteInProgressIds.delete(key);
            if (triggerBtn && triggerBtn.isConnected) {
                triggerBtn.disabled = false;
                triggerBtn.removeAttribute('aria-busy');
            }
        }
    }

    tasksListEl.addEventListener('click', (e) => {
        const delBtn = e.target.closest('.task-delete-btn');
        if (delBtn) {
            if (delBtn.disabled) return;
            const id = delBtn.getAttribute('data-task-id');
            if (id != null && id !== '') deleteTask(id, delBtn);
            return;
        }
        const trigger = e.target.closest('.ai-status-btn');
        if (!trigger || trigger.disabled) return;
        const id = trigger.getAttribute('data-task-id');
        if (id != null && id !== '') suggestAiStatusForTask(id, trigger);
    });

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
