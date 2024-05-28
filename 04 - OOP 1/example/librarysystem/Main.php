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
     * Prints the book details to the console
     *
     * @return void
     */
    public function showBook(): void
    {
        $book = $this->createBook();
        echo "Title: {$book->title}\n";
        echo "Author: {$book->author}\n";
        echo "ISBN: {$book->isbn}\n";
        echo "Genre: {$book->genre}\n";
        echo "Age Rating: {$book->ageRating}\n";
        echo "Pages: {$book->pages}\n";
        echo "Publisher: {$book->publisher}\n";
        echo "Year Published: {$book->getYearPublished()}\n";
    }
}
