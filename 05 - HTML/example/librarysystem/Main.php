<?php
class Main
{
    /**
     * Creates a new book (the hobbit by J.R.R. Tolkien)
     * @return Book
     */
    public function createBook(): Book
    {
        /* 
            We use named parameters, these are optional. You can use them to pass parameters out of order.
            It also helps to make the code more readable when you have a lot of parameters to pass.
        */
        return new Book(
            title: 'The Hobbit',
            author: 'J.R.R. Tolkien',
            isbn: '978-0-395-07122-1',
            genre: 'Fantasy',
            ageRating: 'PG-13',
            pages: 310,
            publisher: 'Houghton Mifflin',
            publishedAt: new DateTime('1937-09-21')
        );
    }

    /**
     * Gets the book and includes the HTML file. The $book variable is available in the included file.
     *
     * @return void
     */
    public function showBook(): void
    {
        $book = $this->createBook();
        include_once 'html/book.html';
    }
}
