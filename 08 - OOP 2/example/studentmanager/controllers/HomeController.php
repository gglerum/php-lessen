<?php
class HomeController
{
    public static function render(): void
    {
        if (!isset($_GET['name'])) {
            $name = "Piet";
        }
        include_once 'html/home.html';
    }
}
