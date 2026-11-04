<?php

declare(strict_types=1);

require_once './Word.php';

// dezelfde Word class, nu met een webpagina als invoer en uitvoer
// start met `php -S localhost:8090` in deze map en open http://localhost:8090/web.php
$word = new Word('banana');

// de geraden letters houden we voor de eenvoud bij in de url, bijvoorbeeld ?letters=an
// de nieuwe letter uit het formulier plakken we er achter
$letters = ($_GET['letters'] ?? '') . ($_GET['letter'] ?? '');

for ($i = 0; $i < strlen($letters); $i++) {
    $word->guessLetter($letters[$i]);
}

?>
<h1><?= htmlspecialchars($word->getDisplayWord()) ?></h1>

<?php if ($word->isGuessed()): ?>
    <p>Gewonnen!</p>
<?php else: ?>
    <form method="get">
        <input type="hidden" name="letters" value="<?= htmlspecialchars($letters) ?>">
        <label>Raad een letter: <input name="letter" maxlength="1"></label>
        <button>Raad</button>
    </form>
<?php endif; ?>
