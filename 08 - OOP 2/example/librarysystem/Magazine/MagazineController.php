<?php
class MagazineController
{
    /**
     * Show magazine creation form
     *
     * @return void
     */
    public static function create(): void
    {
        include_once 'html/form.html';
    }

    /**
     * Store a newly created magazine in storage
     *
     * @return void
     */
    public static function store(): void
    {
        $id = Magazine::insert($_POST);
        header('location: /?type=magazine&action=show&id=' . $id);
    }

    /**
     * Show the specified magazine, if no id supplied redirect to home
     *
     * @return void
     */
    public static function show(): void
    {
        if (!isset($_GET['id'])) {
            header('location: /');
        }
        $magazine = Magazine::load($_GET['id']);
        require_once 'html/show.html';
    }

    /**
     * Show all magazines
     *
     * @return void
     */
    public static function index()
    {
        $magazines = Magazine::all();
        require_once 'html/index.html';
    }
}
