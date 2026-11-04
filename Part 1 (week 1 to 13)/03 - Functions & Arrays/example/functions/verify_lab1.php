<?php

// Controleert de uitvoer in lab/call_and_return.html door dezelfde code echt uit te voeren.
// Run met: php verify_lab1.php (vanuit deze map). Alle regels moeten "ok" zeggen.
// De uitvoerregels van het lab worden hier aan elkaar geplakt, omdat echo zelf geen regeleinde toevoegt.

function check(string $name, string $actual, string $expected): void
{
    echo $actual === $expected ? "ok    " : "FOUT  ", $name, $actual === $expected ? "" : " (kreeg '$actual', verwacht '$expected')", "\n";
}

// scenario 1
function calculateTotal(int $price, int $quantity): int
{
    return $price * $quantity;
}
$result = calculateTotal(15, 4);
check('1', (string) $result, '60');

// scenario 2
function showTotal(int $price, int $quantity): void
{
    echo $price * $quantity;
}
ob_start();
$total = showTotal(15, 4);
echo $total;
check('2 uitvoer', ob_get_clean(), '60');
check('2 $total is null', var_export($total, true), 'NULL');

// scenario 3
function describeStudent(array $student): string
{
    return $student["name"] . ": " . $student["score"];
}
$student = ["name" => "Sam", "score" => 72];
ob_start();
echo describeStudent($student);
check('3', ob_get_clean(), 'Sam: 72');

// scenario 4
function calculateSubtotal(int $price, int $quantity): int
{
    return $price * $quantity;
}
function getShippingCost(int $subtotal): int
{
    if ($subtotal >= 50) {
        return 0;
    }

    return 5;
}
$subtotal = calculateSubtotal(15, 3);
$shipping = getShippingCost($subtotal);
check('4 subtotal', (string) $subtotal, '45');
check('4 shipping', (string) $shipping, '5');
check('4 uitvoer', (string) ($subtotal + $shipping), '50');
