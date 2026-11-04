# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this repo is

Teaching material for a 39-week PHP course (lesson text is largely Dutch, with some English). It is not one application: it is markdown lessons plus many small, independent example projects.

- `Part 1 (week 1 to 13)/` – PHP fundamentals, numbered lesson folders (`02 - Basics`, `03 - Functions & Arrays`, `04 - OOP 1`, `05 - HTML & Forms & Sessions`, `07 - Databases`, `08/09 - OOP 2/3`). Each has a `README.md` and usually an `example/` folder.
- `Part 2 (week 14 to 26)/` – Laravel; currently only a README (the owner's breakdown of the Laravel documentation).
- `Part 3 (week 27 to 39)/` – design principles, rewritten in Dutch in the owner's style: `01` general principles, `02` clean code, `03` SOLID (2 lessons), `04` GRASP, `05` refactoring, `06` design patterns (3 lessons), `07` DDD (2 lessons), plus `example/` with the Laravel `bad` and `better` code. See "Part 3 lessons" below.
- `_archive/`, `course-analysis-knowledge-gaps.md` – old material / curriculum analysis, not course content. `_archive/part3_ai_generated/` holds the old AI generated Part 3 text; do not use it as a source or style reference.

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
- The READMEs of the repo root, Part 1, Part 2 and Part 3 and the Part 3 lessons `01` to `07` were rewritten (Dutch, owner's style) after the old AI text was removed, and can be used as a reference. Part 3 lessons were drafted by Claude and reviewed in outline by the owner, but the lesson text itself has not been reviewed yet.
- Never use anything in `_archive/` as a source or style reference: that text is AI generated.
- When asked to write new material, check `git log` first and only imitate text from the owner's period. The newest lesson (check the latest commits) is the best template.

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

### Part 3 lessons
- Every lesson folder has a short `README.md` (reading list plus links), the explanation (`<topic>.md`, split in `_1`, `_2`, `_3` for `solid`, `patterns` and `ddd`), one `_opgaven_N.md` per explanation file and an `example/<topic>/` folder.
- Examples are pairs: `<name>_voor.php` (code with the problem) and `<name>_na.php` (code with the principle) that print the same output unless the lesson says otherwise. Most examples use the library system or Hangman from Part 1. Run them with `php file.php` from their folder. The refactoring example has `check.php`, which compares every step with the starting output.
- Each explanation ends with a "Samengevat" table (principle, question to ask, red flag) and a "Wanneer gebruik je het niet?" paragraph that links back to YAGNI.
- Exercises include the starting code in the markdown. When writing them, extract the PHP blocks and run each one, so the output the text mentions is what the code prints.
- The outline was reviewed with the owner before each lesson. Decisions: Law of Demeter and Tell Don't Ask live in `05`, GRASP covers all nine patterns (four briefly), Laravel Facades are left to Part 2, and there is no final assignment.
- Not verified: the exercises have not been worked out; the statements about Laravel in `06 - Design Patterns/patterns_3.md` (Query Builder, middleware, jobs, Form Request) come from general framework knowledge and were not checked against a Laravel project; `example/better/.../AnswerAction.php` uses `Application` without a `use` statement.

### Interactive labs (instead of exercises)
`04 - OOP 1/stack_and_heap.md` uses interactive labs instead of an `_opgaven_N.md` file. Use the same approach when the owner asks for labs or when a concept is about memory or execution order.
- Labs live in `example/<topic>/lab/` and are static HTML/CSS/JS only (no PHP, no build step), so they can run on GitHub Pages. The whole course is published by `scripts/prepare-site.py` (Jekyll, `.github/workflows/pages.yml`), which copies only git-tracked files: new labs go online once committed and merged to `main`, no workflow change needed. Because every tracked file is copied, anything in a `lab/` folder is published too (keep `verify_labN.php` out of it).
- Labs exist for: `02 - Basics/example/basics`, `03/example/functions`, `03/example/callbacks`, `04/example/classes`, `04/example/stack_and_heap`. Each has an `index.html` and 3 to 4 labs: a step debugger, a predict-the-output quiz (radio buttons) and a bug hunt (quiz plus a "Gecorrigeerd" variant). New labs should follow the same shape.
- Shared code is copied per lab folder, not shared: `lab.css`, `stepper.js` (step debugger engine) and a lab-specific `common.js` (class listings, member explanations, step helpers such as `ctorSteps`, `filterSteps`, `loopSteps`). The copies have diverged (`stack_and_heap` has BankAccount and storage built in; the others have an optional heap panel, array values and `warn` / `fatal` ops, and `classes` shows an "Objecten" panel instead of "Heap" so it does not pre-empt `stack_and_heap.md`). When fixing the engine, apply the fix to every copy. A scenario is data: a program with `// @label` markers behind lines (one label per line), and steps (`at` label, Dutch `say` text, `ops` that change stack, heap, output and storage). Reuse the helpers instead of writing new engine code.
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

## Handoff: labs for earlier lessons (session of 2026-11-04)

Done in that session, all uncommitted: labs for Basics, Functions, callbacks and Classes (see "Interactive labs"), each with `verify_labN.php` (all `ok`) and a "Labs" section in the lesson text / README. Browser-checked: every scenario of every lab, including the fixed variants. The scenario/page generator was a throwaway script outside the repo, so the `.html` files are now the source of truth: edit them directly.

Recommendations for the next session, in priority order:
1. **Owner review before anything is committed.** The "Labs" sections in `functions.md`, `classes.md`, `callback_functions.md` and `02 - Basics/README.md` and all lab `say` / `why` texts are written by Claude (imitating the owner's style). The owner presents these lessons, so read them and adjust. For the Classes, Basics and callbacks labs the outline was not reviewed first (the owner only said "ja" to an offer), which `Scope and workflow` says should happen.
2. **Not verified:** narrow/mobile layout, keyboard-only use and `prefers-reduced-motion` for the new labs (only checked in a small browser pane), and that the Pages build really publishes the new `lab/` folders (check after merging to `main`).
3. **Engine duplication.** `stepper.js` and `lab.css` now exist in five copies and have already diverged (a missing `fatal` op in the callbacks copy was found only in the browser check). Consider one shared `labs/shared/` folder referenced with relative paths (works on Pages), or at least a script that diffs the copies.
4. **Verify scripts duplicate the expected values by hand** from the lab HTML, so they can drift. Consider extracting each lab's scenarios to a JSON file that both the page and a runner read, and a single `run_all_verify` script. Also check `04 - OOP 1/example/stack_and_heap/lab/` which contains `verify_lab1.php` and `verify_lab4.php` next to the copies in `example/stack_and_heap/`: they get published and CLAUDE.md says they belong outside the lab folder.
5. **Syntax beyond what was taught.** `functions/lab/predict_output.html` uses `var_dump`; the Basics and callbacks labs use `implode`, `strpos`, `in_array` and `strlen` (the Hangman example already uses the last three, `implode` is new). Either mention them in the lesson or swap them out.
6. **Lesson text issues found in `callback_functions.md`** (not changed): the "achter de schermen" version of `array_filter` uses `$newArray[] = ...`, but the real `array_filter` keeps the original keys (so `[1, 'twee', 3]` gives `[0 => 1, 2 => 3]`, not `[1, 3]`); the example declares `function array_filter`, which cannot be run next to the built-in; two statements lack a `;`; and the "use" section says arrow functions "always have access" to outer variables, which is true only by value at creation. The labs call the demo function `myArrayFilter` and hide keys with `implode`.
7. **Not yet covered:** `05 - HTML & Forms & Sessions`, `07 - Databases`, `08` and `09 - OOP 2/3`. A step debugger fits these less well. Ideas: request/response and session lifetime for 05 (extend `lifetime_and_request.html`), a query/result-table lab for 07 (needs a "table" panel in the engine), inheritance and polymorphism object diagrams for 08/09. Ask the owner which lesson is next before building.
8. **Labs next to or instead of exercises.** For Functions the labs sit next to the existing exercises; `stack_and_heap.md` uses labs instead. Decide per lesson, and consider telling students when to use them (before the exercises as explanation, or after as self-check).
9. **Housekeeping:** PHP test servers from that session may still run on ports 8093, 8094 and 8095 (`taskkill` was blocked because it also kills other PHP processes); stop them from the Terminal tab or Task Manager.

## Notes

- `.github/copilot-instructions.md` is Laravel Boost boilerplate (Laravel 12, Inertia, Livewire, Pint, PHPUnit-only). It does not describe this repo's plain-PHP examples; apply it only if actual Laravel code is added in Part 2.
- Example code is intentionally didactic: inline comments explain the "why" for students, so preserve and write comments in that style when editing examples.
