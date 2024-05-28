<?php
class BookController
{
    public static function create()
    {
        include_once 'html/form.html';
    }

    public static function store()
    {
        $id = Book::insert($_POST);
        header('location: /?type=book&action=show&id=' . $id);
    }

    public static function show()
    {
        if (!isset($_GET['id'])) {
            header('location: /');
        }
        $book = Book::load($_GET['id']);
        require_once 'html/show.html';
    }

    public static function index()
    {
        $books = Book::all();
        require_once 'html/index.html';
    }
}
