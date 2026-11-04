# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repo is

Teaching material for a 39-week PHP course (lesson text is largely Dutch, with some English). It is not one application: it is markdown lessons plus many small, independent example projects.

- `Part 1 (week 1 to 13)/` – PHP fundamentals, numbered lesson folders (`02 - Basics`, `03 - Functions & Arrays`, `04 - OOP 1`, `05 - HTML & Forms & Sessions`, `07 - Databases`, `08/09 - OOP 2/3`). Each has a `README.md` and usually an `example/` folder.
- `Part 2 (week 14 to 26)/` – Laravel; currently only a README.
- `Part 3 (week 27 to 39)/` – design principles (SOLID, GRASP, DDD, patterns), numbered folders plus `example/`.
- `_archive/`, `course-analysis-knowledge-gaps.md` – old material / curriculum analysis, not course content.

Examples evolve across lessons, e.g. the Hangman game goes `hangman_v3` (OOP 1) → `hangman_v4` (OOP 3), and the library system goes from `03` (arrays) → `05` (web/sessions) → `07` (databases). When changing a concept, check whether later or earlier lessons show the same example and keep the explanations consistent.

## Running examples

There is no root build, lint, or test setup. Each example is run on its own with PHP (devcontainer uses PHP 8.2, with xdebug, PDO/mysqli and a MariaDB client):

- Console scripts: `php console.php` from inside the example directory (they use `readline()` and need a real terminal).
- Web examples: `php -S localhost:8090` from the example directory (the devcontainer forwards 8080, 8090, 9000, 3306).
- Examples with a `composer.json` (`moviemanagement`, `librarysystem`, `hangman_v4`) use PSR-4 autoloading (e.g. `Hacklabfrl\Hangman\` → `game/`) and a committed-ignored `vendor/`; run `composer install` / `composer dump-autoload` in that directory. `hangman_v4` pulls in PHPUnit, with an (currently empty) `Test/` directory.
- Debugging: `.vscode/launch.json` has "Launch current script", and "Listen for Xdebug" (port 9003) configurations.

## Writing lesson material

The owner (the teacher) uses Claude to draft lessons and exercises. The owner presents the lesson, so the material must read like their own writing.

### Which text is the owner's own
- Their own writing is everything from commit `2428f4b` ("reverted back to no AI generated text", 2026-10-06) onwards. Reference lessons for style: `03 - Functions & Arrays/functions.md`, `callback_functions.md`, `vorige_keer_behandeld.md`, the `example/*.php` files there, and the changes in `02 - Basics/README.md`.
- Do NOT use the repo `README.md`, `Part 1/README.md`, or anything in Part 2 and Part 3 as a style reference: that text is AI generated.
- When asked to write new material, check `git log` first and only imitate text from that period. The newest lesson (check the latest commits) is the best template.

### Style
- Dutch, informal ("laten we...", "Let op" / "LETOP"), short paragraphs. Technical terms in backticks and in their English form (`parameters`, `return`, `constructor`).
- Start from what students already know (their Python background, the previous lesson) and build up step by step, often "zonder X → met X" (see `function.php`). Compare with Python where it helps (`self` → `$this`).
- Small ```PHP blocks, each followed directly by an explanation of what to notice. No long theory without code.
- Example code uses English names and Dutch comments that explain the "why". Files use `declare(strict_types=1);` when types are part of the lesson.
- Recap tables and "Samengevat" sections are fine, but keep them short. Do not add emoji or marketing-style headings.

### Structure of a lesson
- `<topic>.md` in the lesson folder with the explanation, a "Voorbeeldbestanden" section (numbered list of links to `./example/...`, one sentence each) and a link to the exercises.
- Exercises go in a separate `<topic>_opgaven_N.md`: intro sentence, then `### Opgave N: Titel`, the assignment, and a `**Hint:**`. Exercises build on each other, use different examples than the explanation (not copy-paste), and require students to combine the concepts themselves.
- Examples live in `example/<topic>/`, small scripts that build on each other and share one running example (e.g. the bank account for classes).
- Link the new files from the lesson `README.md` with one or two sentences, as `03/README.md` does for `functions.md`. Leave the rest of that README alone.

### Interactive labs (instead of exercises)
`04 - OOP 1/stack_and_heap.md` uses interactive labs instead of an `_opgaven_N.md` file. Use the same approach when the owner asks for labs or when a concept is about memory or execution order.
- Labs live in `example/<topic>/lab/` and are static HTML/CSS/JS only (no PHP, no build step), so they can run on GitHub Pages. `.github/workflows/pages.yml` publishes only `04 - OOP 1/example/stack_and_heap/lab`; update its `path` and `paths` when labs are added elsewhere.
- Shared code: `lab.css`, `common.js` (class code and explanations per member) and `stepper.js` (step debugger engine). A scenario is data: a program with `// @label` markers behind lines, and steps (`at` label, Dutch `say` text, `ops` that change stack, heap, output and storage). Reuse the helpers (`ctorSteps`, `depositSteps`, `getBalanceSteps`) instead of writing new engine code.
- Always show the code of every class a lab uses and explain each member in plain language: the owner does not remember what a constructor or method does.
- Only use syntax that was already taught (for example no constructor property promotion yet).
- Because answers are baked into the pages, every lab needs a PHP script (`verify_labN.php` in `example/<topic>/`, outside the published folder) that runs the same scenarios and prints `ok` or `FOUT`. Run it after every change.
- Check labs in the browser through `php -S localhost:<port> -t <lab folder>`. Opening a `file://` page in the Claude browser pane renders a static snapshot without the CSS and JavaScript.
- UX: one concept per lab, fixed colours (stack blue, heap orange) with a text label next to them, keyboard operable, `aria-live` for explanations, respect `prefers-reduced-motion`, no ligatures in code.

### Scope and workflow
- Ask or confirm what belongs in today's lesson and what is for the next one. Do not pre-empt the next lesson (e.g. classes as models replacing associative arrays was deliberately kept for the lesson after the class basics). Keep out concepts from later lessons unless the owner asks.
- Connect to earlier lessons: recap why the previous concept exists (e.g. functions: organisation, reuse, type safety), show its limitation, then show how the new concept solves it.
- Plan first (plan mode is fine): the owner reviews and adjusts the outline before any files are written.
- Run every example (`php file.php`, from the example directory) and verify the output matches what the text says. Xdebug prints "Time-out connecting to debugging client" on stderr when no debugger is listening; this is harmless noise.
- Do not commit unless asked. The owner commits lesson material themselves. When asked, stage only the lesson files and leave unrelated dirty files (`.vscode/launch.json`, `CLAUDE.md`, other untracked folders) out.
- Git remotes: `glenn` is the owner's own `gglerum/php-lessen`, `origin` is `MKB-Cyber-Campus/php-lessen`. Claude cannot push (SSH key with passphrase), the owner pushes. The Pages workflow only runs on `main`, so a lesson branch must be merged before the labs appear online.
- Python comparisons are optional per lesson: `stack_and_heap.md` deliberately has none.

## Notes

- `.github/copilot-instructions.md` is Laravel Boost boilerplate (Laravel 12, Inertia, Livewire, Pint, PHPUnit-only). It does not describe this repo's plain-PHP examples; apply it only if actual Laravel code is added in Part 2.
- Example code is intentionally didactic: inline comments explain the "why" for students, so preserve and write comments in that style when editing examples.
