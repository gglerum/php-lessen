<?php

use PHPUnit\Framework\TestCase;
use Hacklabfrl\Moviemanagement\Movie;

/**
 * UNIT TESTS FOR MOVIE CLASS - TESTING INDIVIDUAL METHODS
 * 
 * 🎯 LEARNING OBJECTIVES:
 * Unit tests focus on testing individual methods of a single class in isolation.
 * This is different from Feature tests which test complete workflows.
 * 
 * 📚 WHAT YOU'LL LEARN FROM THESE TESTS:
 * 1. **Arrange-Act-Assert Pattern**: The standard structure for writing tests
 * 2. **Testing Object Properties**: How to verify object state after creation
 * 3. **Testing Method Behavior**: Ensuring methods return expected values
 * 4. **Edge Cases**: Testing boundary conditions and special scenarios
 * 5. **Descriptive Test Names**: Self-documenting test method names
 * 
 * 🔧 TEST STRUCTURE:
 * Each test method follows the same pattern:
 * - Arrange: Set up the data needed for the test
 * - Act: Call the method you want to test
 * - Assert: Check that the result matches expectations
 */
class MovieTest extends TestCase
{
    /**
     * TEST: MOVIE OBJECT CREATION AND ID GENERATION
     * 
     * 🎯 WHAT THIS TESTS:
     * - Constructor properly initializes a Movie object
     * - Each movie gets a unique, auto-incrementing ID
     * - Object properties are set correctly
     * 
     * 📝 ARRANGE-ACT-ASSERT PATTERN:
     * This test clearly shows the three phases of testing
     */
    public function testMovieCanBeCreatedWithUniqueId()
    {
        // 🏗️ ARRANGE: Set up the test data
        $title = "The Matrix";
        $director = "The Wachowskis";
        $rating = 8.7;

        // ⚡ ACT: Create the object we want to test
        $movie = new Movie($title, $director, $rating);

        // ✅ ASSERT: Check that everything worked as expected
        $this->assertInstanceOf(Movie::class, $movie, "Should create a Movie object");
        $this->assertIsInt($movie->getId(), "ID should be an integer");
        $this->assertGreaterThan(0, $movie->getId(), "ID should be positive");
    }

    /**
     * TEST: MOVIE NAME FORMATTING BEHAVIOR
     * 
     * 🎯 WHAT THIS TESTS:
     * - getName() method applies uppercase formatting
     * - Original data is preserved while presentation is transformed
     * - Encapsulation principle: objects control their own presentation
     */
    public function testGetNameReturnsUppercaseTitle()
    {
        // 🏗️ ARRANGE: Create a movie with mixed-case title
        $movie = new Movie("The Dark Knight", "Christopher Nolan", 9.0);

        // ⚡ ACT: Get the formatted name
        $formattedName = $movie->getName();

        // ✅ ASSERT: Verify uppercase conversion
        $this->assertEquals("THE DARK KNIGHT", $formattedName, "Name should be converted to uppercase");
        $this->assertIsString($formattedName, "getName() should return a string");
    }

    /**
     * TEST: OVERVIEW TEXT FORMATTING
     * 
     * 🎯 WHAT THIS TESTS:
     * - getOverviewText() creates properly formatted display string
     * - All movie data is included in the correct format
     * - String concatenation works as expected
     */
    public function testGetOverviewTextFormatsMovieCorrectly()
    {
        // 🏗️ ARRANGE: Create a movie with known data
        $movie = new Movie("Inception", "Christopher Nolan", 8.8);

        // ⚡ ACT: Get the overview text
        $overview = $movie->getOverviewText();

        // ✅ ASSERT: Check the exact format
        $expectedText = "Inception - Christopher Nolan - 8.8";
        $this->assertEquals($expectedText, $overview, "Overview should follow 'title - director - rating' format");
        $this->assertStringContainsString("Inception", $overview, "Should contain movie title");
        $this->assertStringContainsString("Christopher Nolan", $overview, "Should contain director name");
        $this->assertStringContainsString("8.8", $overview, "Should contain rating");
    }

    /**
     * TEST: EDGE CASE - EMPTY STRING VALUES
     * 
     * 🎯 WHAT THIS TESTS:
     * - How the Movie class handles edge cases
     * - Behavior with unusual but possible input
     * - Defensive programming practices
     * 
     * 💡 WHY TEST EDGE CASES:
     * Real applications need to handle unexpected input gracefully
     */
    public function testMovieHandlesEmptyStrings()
    {
        // 🏗️ ARRANGE: Create movie with empty strings (edge case)
        $movie = new Movie("", "", 0.0);

        // ⚡ ACT: Test methods with empty data
        $name = $movie->getName();
        $overview = $movie->getOverviewText();

        // ✅ ASSERT: Verify behavior with empty input
        $this->assertEquals("", $name, "Empty title should remain empty after uppercase conversion");
        $this->assertEquals(" -  - 0", $overview, "Overview should handle empty strings gracefully");
        $this->assertGreaterThan(0, $movie->getId(), "ID should still be generated even with empty data");
    }

    /**
     * TEST: MULTIPLE MOVIES GET UNIQUE IDS
     * 
     * 🎯 WHAT THIS TESTS:
     * - Static property $idIncrement works correctly
     * - Each movie instance gets a different ID
     * - ID generation is consistent and predictable
     * 
     * 🔢 STATIC PROPERTY TESTING:
     * This demonstrates testing class-level behavior, not just instance behavior
     */
    public function testMultipleMoviesGetUniqueIds()
    {
        // 🏗️ ARRANGE: Create multiple movies
        $movie1 = new Movie("Movie One", "Director One", 1.0);
        $movie2 = new Movie("Movie Two", "Director Two", 2.0);
        $movie3 = new Movie("Movie Three", "Director Three", 3.0);

        // ⚡ ACT: Get their IDs
        $id1 = $movie1->getId();
        $id2 = $movie2->getId();
        $id3 = $movie3->getId();

        // ✅ ASSERT: Verify all IDs are unique and sequential
        $this->assertNotEquals($id1, $id2, "Movie IDs should be unique");
        $this->assertNotEquals($id2, $id3, "Movie IDs should be unique");
        $this->assertNotEquals($id1, $id3, "Movie IDs should be unique");

        // Test that IDs increment sequentially
        $this->assertEquals($id1 + 1, $id2, "IDs should increment by 1");
        $this->assertEquals($id2 + 1, $id3, "IDs should increment by 1");
    }

    /**
     * TEST: RATING VALUES ARE PRESERVED
     * 
     * 🎯 WHAT THIS TESTS:
     * - Float values are stored and retrieved correctly
     * - No unexpected rounding or precision loss
     * - Data integrity is maintained
     */
    public function testMoviePreservesRatingPrecision()
    {
        // 🏗️ ARRANGE: Create movie with precise rating
        $preciseRating = 8.75;
        $movie = new Movie("Test Movie", "Test Director", $preciseRating);

        // ⚡ ACT: Get the overview (which includes the rating)
        $overview = $movie->getOverviewText();

        // ✅ ASSERT: Verify rating precision is maintained
        $this->assertStringContainsString("8.75", $overview, "Rating precision should be preserved");
        $this->assertStringNotContainsString("8.7", $overview, "Rating should not be rounded unexpectedly");
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM THESE UNIT TESTS:
 * 
 * 1. **Test Organization**: Each test focuses on one specific behavior
 * 2. **Descriptive Names**: Test method names clearly explain what's being tested
 * 3. **Multiple Assertions**: Tests can check several related things
 * 4. **Edge Case Testing**: Important to test unusual but possible scenarios
 * 5. **Data Integrity**: Verify that objects maintain their state correctly
 * 6. **Method Behavior**: Test both the "happy path" and edge cases
 * 
 * 💡 TESTING BEST PRACTICES DEMONSTRATED:
 * - Clear Arrange-Act-Assert structure
 * - Meaningful assertion messages for better debugging
 * - Testing both normal and edge cases
 * - Isolated tests that don't depend on each other
 * - Comprehensive coverage of class functionality
 */
