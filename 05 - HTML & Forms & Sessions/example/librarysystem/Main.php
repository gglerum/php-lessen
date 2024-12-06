<?php
class Main
{
    /**
     * Creates a new book (the hobbit by J.R.R. Tolkien)
     * @param array $data Data used to create the book
     * @return Book
     */
    public function createBook(array $data): Book
    {
        /* 
            We use named parameters, these are optional. You can use them to pass parameters out of order.
            It also helps to make the code more readable when you have a lot of parameters to pass.
        */
        return new Book(
            title: $data['title'],
            author: $data['author'],
            isbn: $data['isbn'],
            genre: $data['genre'],
            ageRating: $data['ageRating'],
            pages: $data['pages'],
            publisher: $data['publisher'],
            publishedAt: DateTime::createFromFormat('Y-m-d', $data['publishedAt'])
        );
    }

    /**
     * Gets the book and includes the HTML file. The $book variable is available in the included file.
     *
     * @param Book $book The book to show
     * @return void
     */
    public function showBook(Book $book): void
    {
        include_once 'html/book.html';
    }
}
