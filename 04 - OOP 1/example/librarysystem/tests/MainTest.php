<?php

declare(strict_types=1);
require_once 'Book.php';
require_once 'Main.php';

use PHPUnit\Framework\TestCase;

/**
 * We are going to test if our methods work as expected
 */
final class MainTest extends TestCase
{
    /**
     * We test if $main->createBook returns a Book object and if it is the correct book
     */
    public function testCanCreateBook(): void
    {
        $main = new Main();
        $book = $main->createBook();
        $this->assertInstanceOf(Book::class, $book);
        $this->assertEquals('The Hobbit', $book->title);
    }

    /**
     * We test if $main->showBook prints the correct information
     */
    public function testCanShowBook(): void
    {
        $main = new Main();
        $book = $main->showBook();
        $this->expectOutputString(
            "Title: The Hobbit\n" .
                "Author: J.R.R. Tolkien\n" .
                "ISBN: 978-0-395-07122-1\n" .
                "Genre: Fantasy\n" .
                "Age Rating: PG-13\n" .
                "Pages: 310\n" .
                "Publisher: Houghton Mifflin\n" .
                "Year Published: 1937\n"
        );
    }
}
