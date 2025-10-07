<?php

use PHPUnit\Framework\TestCase;
use Hacklabfrl\Moviemanagement\Movie;
use Hacklabfrl\Moviemanagement\MovieRepository;

/**
 * ENHANCED FEATURE TESTS FOR MOVIE REPOSITORY
 *
 * 🎯 LEARNING OBJECTIVES:
 * These tests demonstrate comprehensive testing strategies for beginners:
 * 1. **Testing Happy Paths**: Normal, expected usage
 * 2. **Testing Edge Cases**: Unusual but valid scenarios
 * 3. **Testing Error Conditions**: How code handles problems
 * 4. **Test Data Management**: Using setUp() for clean test environments
 * 5. **Multiple Assertions**: Thorough verification of behavior
 *
 * 🏗️ FEATURE TESTING VS UNIT TESTING:
 * Feature tests verify complete workflows and object interactions,
 * while unit tests focus on individual methods in isolation.
 */
class MovieRepositoryEnhancedTest extends TestCase
{
    private MovieRepository $repository;

    /**
     * SETUP METHOD - CREATING CLEAN TEST ENVIRONMENT
     *
     * 🧹 WHY USE setUp():
     * This method runs before EACH test, ensuring every test starts
     * with a fresh, clean repository. This prevents tests from
     * interfering with each other.
     *
     * 🔄 TEST ISOLATION:
     * Each test should be independent - this setup ensures that.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Create a fresh repository for each test
        $this->repository = new MovieRepository();
    }

    /**
     * TEST: BASIC ADD AND RETRIEVE FUNCTIONALITY
     *
     * 🎯 WHAT THIS TESTS:
     * - Adding a movie to the repository
     * - Retrieving a movie by ID
     * - Verifying the retrieved movie matches the original
     */
    public function testCanAddAndRetrieveMovieById()
    {
        // 🏗️ ARRANGE: Create a movie with known data
        $originalMovie = new Movie('Pulp Fiction', 'Quentin Tarantino', 8.9);

        // ⚡ ACT: Add to repository and retrieve
        $returnedId = $this->repository->add($originalMovie);
        $retrievedMovie = $this->repository->getById($returnedId);

        // ✅ ASSERT: Verify everything worked correctly
        $this->assertNotNull($retrievedMovie, "Movie should be retrievable after adding");
        $this->assertEquals($originalMovie->getId(), $retrievedMovie->getId(), "Retrieved movie should have same ID");
        $this->assertEquals($originalMovie->getName(), $retrievedMovie->getName(), "Retrieved movie should have same name");
        $this->assertEquals($originalMovie->getOverviewText(), $retrievedMovie->getOverviewText(), "Retrieved movie should match original");
    }

    /**
     * TEST: REPOSITORY STORES MULTIPLE MOVIES
     *
     * 🎯 WHAT THIS TESTS:
     * - Repository can handle multiple movies
     * - Each movie maintains its unique identity
     * - getAll() returns all stored movies
     */
    public function testCanStoreMultipleMovies()
    {
        // 🏗️ ARRANGE: Create several movies
        $movie1 = new Movie('The Godfather', 'Francis Ford Coppola', 9.2);
        $movie2 = new Movie('The Shawshank Redemption', 'Frank Darabont', 9.3);
        $movie3 = new Movie('Schindler\'s List', 'Steven Spielberg', 9.0);

        // ⚡ ACT: Add all movies to repository
        $this->repository->add($movie1);
        $this->repository->add($movie2);
        $this->repository->add($movie3);

        // ✅ ASSERT: Verify all movies are stored
        $allMovies = $this->repository->getAll();
        $this->assertCount(3, $allMovies, "Repository should contain exactly 3 movies");

        // Verify each movie can be retrieved individually
        $this->assertNotNull($this->repository->getById($movie1->getId()), "First movie should be retrievable");
        $this->assertNotNull($this->repository->getById($movie2->getId()), "Second movie should be retrievable");
        $this->assertNotNull($this->repository->getById($movie3->getId()), "Third movie should be retrievable");
    }

    /**
     * TEST: REMOVING MOVIES FROM REPOSITORY
     *
     * 🎯 WHAT THIS TESTS:
     * - Movies can be removed by ID
     * - Removed movies are no longer retrievable
     * - Repository size decreases after removal
     */
    public function testCanRemoveMoviesById()
    {
        // 🏗️ ARRANGE: Add movies to repository
        $movie1 = new Movie('Movie to Keep', 'Director 1', 7.0);
        $movie2 = new Movie('Movie to Remove', 'Director 2', 6.0);
        $this->repository->add($movie1);
        $this->repository->add($movie2);

        // Verify both movies are initially present
        $this->assertCount(2, $this->repository->getAll(), "Should have 2 movies initially");

        // ⚡ ACT: Remove one movie
        $this->repository->remove($movie2->getId());

        // ✅ ASSERT: Verify removal worked
        $this->assertCount(1, $this->repository->getAll(), "Should have 1 movie after removal");
        $this->assertNotNull($this->repository->getById($movie1->getId()), "Remaining movie should still be accessible");
        $this->assertNull($this->repository->getById($movie2->getId()), "Removed movie should not be accessible");
    }

    /**
     * TEST: SEARCHING MOVIES BY TITLE
     *
     * 🎯 WHAT THIS TESTS:
     * - getByTitle() finds movies correctly
     * - Search handles uppercase conversion properly
     * - Returns null when movie not found
     */
    public function testCanFindMovieByTitle()
    {
        // 🏗️ ARRANGE: Add a movie with known title
        $movie = new Movie('Inception', 'Christopher Nolan', 8.8);
        $this->repository->add($movie);

        // ⚡ ACT: Search for movie by title
        $foundMovie = $this->repository->getByTitle('INCEPTION'); // Note: searching with uppercase

        // ✅ ASSERT: Verify movie was found correctly
        $this->assertNotNull($foundMovie, "Movie should be found by title");
        $this->assertEquals($movie->getId(), $foundMovie->getId(), "Found movie should match original");
    }

    /**
     * TEST: EDGE CASE - EMPTY REPOSITORY BEHAVIOR
     *
     * 🎯 WHAT THIS TESTS:
     * - Repository handles empty state gracefully
     * - Methods return appropriate values when no data exists
     * - No errors occur with empty repository operations
     */
    public function testEmptyRepositoryBehavior()
    {
        // ⚡ ACT: Test operations on empty repository
        $allMovies = $this->repository->getAll();
        $movieById = $this->repository->getById(999);
        $movieByTitle = $this->repository->getByTitle('Non-existent Movie');

        // ✅ ASSERT: Verify appropriate empty responses
        $this->assertIsArray($allMovies, "getAll() should return an array even when empty");
        $this->assertEmpty($allMovies, "Empty repository should return empty array");
        $this->assertNull($movieById, "Non-existent ID should return null");
        $this->assertNull($movieByTitle, "Non-existent title should return null");
    }

    /**
     * TEST: EDGE CASE - REMOVING NON-EXISTENT MOVIE
     *
     * 🎯 WHAT THIS TESTS:
     * - Repository handles invalid removal gracefully
     * - No errors occur when trying to remove non-existent movies
     * - Repository state remains unchanged after invalid removal
     */
    public function testRemoveNonExistentMovieDoesNotCauseError()
    {
        // 🏗️ ARRANGE: Add one movie to repository
        $movie = new Movie('Test Movie', 'Test Director', 5.0);
        $this->repository->add($movie);
        $initialCount = count($this->repository->getAll());

        // ⚡ ACT: Try to remove a movie that doesn't exist
        $this->repository->remove(99999); // ID that doesn't exist

        // ✅ ASSERT: Verify repository is unchanged
        $finalCount = count($this->repository->getAll());
        $this->assertEquals($initialCount, $finalCount, "Repository size should be unchanged after invalid removal");
        $this->assertNotNull($this->repository->getById($movie->getId()), "Original movie should still exist");
    }

    /**
     * TEST: REPOSITORY MAINTAINS OBJECT REFERENCES
     *
     * 🎯 WHAT THIS TESTS:
     * - Repository stores actual object references, not copies
     * - Changes to stored objects are reflected in repository
     * - Object identity is preserved
     */
    public function testRepositoryMaintainsObjectReferences()
    {
        // 🏗️ ARRANGE: Create and add a movie
        $movie = new Movie('Reference Test', 'Test Director', 7.5);
        $this->repository->add($movie);

        // ⚡ ACT: Retrieve the movie from repository
        $retrievedMovie = $this->repository->getById($movie->getId());

        // ✅ ASSERT: Verify it's the same object reference
        $this->assertSame($movie, $retrievedMovie, "Repository should return the exact same object reference");
    }

    /**
     * TEST: ADD METHOD RETURNS CORRECT ID
     *
     * 🎯 WHAT THIS TESTS:
     * - add() method returns the movie's ID for confirmation
     * - Returned ID can be used to retrieve the movie
     * - This enables fluent workflow: add and immediately reference
     */
    public function testAddMethodReturnsUsableId()
    {
        // 🏗️ ARRANGE: Create a movie
        $movie = new Movie('ID Test Movie', 'ID Director', 6.5);

        // ⚡ ACT: Add movie and capture returned ID
        $returnedId = $this->repository->add($movie);

        // ✅ ASSERT: Verify returned ID is useful
        $this->assertIsInt($returnedId, "add() should return an integer ID");
        $this->assertEquals($movie->getId(), $returnedId, "Returned ID should match movie's ID");

        // Verify the returned ID can be used to retrieve the movie
        $retrievedMovie = $this->repository->getById($returnedId);
        $this->assertNotNull($retrievedMovie, "Movie should be retrievable using returned ID");
        $this->assertSame($movie, $retrievedMovie, "Retrieved movie should be the same object");
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM THESE ENHANCED TESTS:
 *
 * 1. **Test Organization**: setUp() method for clean test environments
 * 2. **Comprehensive Coverage**: Testing happy paths, edge cases, and error conditions
 * 3. **Multiple Assertions**: Thorough verification within each test
 * 4. **Descriptive Names**: Test names that clearly explain what's being verified
 * 5. **Edge Case Testing**: Important for robust applications
 * 6. **Object Reference Testing**: Understanding how objects are stored
 * 7. **Return Value Testing**: Verifying methods return useful information
 *
 * 💡 PROFESSIONAL TESTING PRACTICES:
 * - Each test is independent and isolated
 * - Tests verify both positive and negative scenarios
 * - Assertion messages help with debugging when tests fail
 * - Tests document expected behavior through examples
 * - Edge cases are explicitly tested, not ignored
 */
