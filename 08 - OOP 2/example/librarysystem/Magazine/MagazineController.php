<?php
class MagazineController
{
    public static function create(): void
    {
        include_once 'html/form.html';
    }

    public static function store(): void
    {
        $id = Magazine::insert($_POST);
        header('location: /?type=magazine&action=show&id=' . $id);
    }

    public static function show(): void
    {
        if (!isset($_GET['id'])) {
            header('location: /');
        }
        $magazine = Magazine::load($_GET['id']);
        require_once 'html/show.html';
    }

    public static function index()
    {
        $magazines = Magazine::all();
        require_once 'html/index.html';
    }
}
