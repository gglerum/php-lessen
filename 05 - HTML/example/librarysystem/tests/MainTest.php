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
        //we check the output with regex, so we don't have to worry about the order of the information, or the html tags
        $this->expectOutputRegex('/The Hobbit/');
        $this->expectOutputRegex('/J.R.R. Tolkien/');
        $this->expectOutputRegex('/978-0-395-07122-1/');
        $this->expectOutputRegex('/Fantasy/');
        $this->expectOutputRegex('/PG-13/');
        $this->expectOutputRegex('/310/');
        $this->expectOutputRegex('/Houghton Mifflin/');
        $this->expectOutputRegex('/1937/');
    }
}
