<?php
require_once 'Book.php';
require_once 'Main.php';
$main = new Main();
?>
<html>

<head>
    <title>Library System</title>
</head>

<body>
    <?php $main->showBook(); ?>
</body>

</html>