<?php
class HomeController
{
    public static function render(?string $name): void
    {
        if (empty($name)) {
            $name = "Piet";
        }
        include_once 'html/home.php';
    }
}
