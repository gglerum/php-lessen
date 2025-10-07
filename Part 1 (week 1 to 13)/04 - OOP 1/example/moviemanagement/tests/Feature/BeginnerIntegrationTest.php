<?php

use PHPUnit\Framework\TestCase;
use Hacklabfrl\Moviemanagement\Movie;
use Hacklabfrl\Moviemanagement\MovieRepository;
use Hacklabfrl\Moviemanagement\MovieController;

/**
 * BEGINNER-FRIENDLY INTEGRATION TESTS
 *
 * 🎯 LEARNING OBJECTIVES:
 * These tests demonstrate how different classes work together (integration testing).
 * Perfect for beginners to understand:
 * 1. **Testing Object Collaboration**: How classes interact with each other
 * 2. **End-to-End Workflows**: Complete user scenarios from start to finish
 * 3. **Real-World Testing**: Testing actual application features
 * 4. **Data Flow Testing**: Following data through multiple objects
 *
 * 🔗 INTEGRATION VS UNIT TESTING:
 * - Unit Tests: Test individual methods in isolation
 * - Integration Tests: Test how multiple classes work together
 * - Feature Tests: Test complete user workflows
 */
class BeginnerIntegrationTest extends TestCase
{
    /**
     * TEST: COMPLETE MOVIE CREATION WORKFLOW
     *
     * 🎯 WHAT THIS TESTS:
     * This test follows a complete user story:
     * "As a user, I want to add a movie through the controller
     *  so that it's stored in the repository and can be retrieved later"
     *
     * 🔄 DATA FLOW:
     * User Data → Controller → Repository → Database (simulated)
     */
    public function testCompleteMovieCreationWorkflow()
    {
        // 🏗️ ARRANGE: Set up the complete system
        $repository = new MovieRepository();
        $controller = new MovieController($repository);

        // Simulate user input data (like from a web form)
        $movieData = [
            'name' => 'The Lord of the Rings',
            'Director' => 'Peter Jackson',
            'rating' => 8.9
        ];

        // Get initial state for comparison
        $initialMovieCount = count($repository->getAll());

        // ⚡ ACT: Execute the complete workflow
        $controller->store($movieData);

        // ✅ ASSERT: Verify the entire process worked
        // 1. Check repository now contains one more movie
        $finalMovieCount = count($repository->getAll());
        $this->assertEquals($initialMovieCount + 1, $finalMovieCount, "Repository should contain one more movie");

        // 2. Verify the movie can be found by title
        $storedMovie = $repository->getByTitle($movieData['name']);
        $this->assertNotNull($storedMovie, "Movie should be findable by title after storage");

        // 3. Verify all data was stored correctly
        $this->assertEquals(strtoupper($movieData['name']), $storedMovie->getName(), "Movie name should be stored correctly");
        $this->assertStringContainsString($movieData['Director'], $storedMovie->getOverviewText(), "Director should be stored correctly");
        $this->assertStringContainsString((string)$movieData['rating'], $storedMovie->getOverviewText(), "Rating should be stored correctly");
    }

    /**
     * TEST: DEPENDENCY INJECTION WORKS CORRECTLY
     *
     * 🎯 WHAT THIS TESTS:
     * - Controller and Repository are properly connected
     * - Changes made through Controller appear in Repository
     * - Dependency injection creates proper object relationships
     *
     * 💡 WHY THIS MATTERS:
     * This demonstrates that our objects work together as designed.
     * It's testing the "plumbing" of our application.
     */
    public function testControllerAndRepositoryAreProperlyConnected()
    {
        // 🏗️ ARRANGE: Create connected objects
        $repository = new MovieRepository();
        $controller = new MovieController($repository);

        // ⚡ ACT: Add movie through controller
        $controller->store(['name' => 'Test Movie', 'Director' => 'Test Director', 'rating' => 7.0]);

        // ✅ ASSERT: Verify connection works
        $allMovies = $repository->getAll();
        $this->assertCount(1, $allMovies, "Repository should reflect changes made through controller");

        $storedMovie = $allMovies[0];
        $this->assertEquals('TEST MOVIE', $storedMovie->getName(), "Movie should be accessible through repository");
    }

    /**
     * TEST: MULTIPLE MOVIES THROUGH CONTROLLER
     *
     * 🎯 WHAT THIS TESTS:
     * - System can handle multiple operations
     * - Each movie maintains its unique identity
     * - No data corruption occurs with multiple operations
     */
    public function testCanAddMultipleMoviesThroughController()
    {
        // 🏗️ ARRANGE: Set up system and test data
        $repository = new MovieRepository();
        $controller = new MovieController($repository);

        $movies = [
            ['name' => 'Movie A', 'Director' => 'Director A', 'rating' => 8.0],
            ['name' => 'Movie B', 'Director' => 'Director B', 'rating' => 7.5],
            ['name' => 'Movie C', 'Director' => 'Director C', 'rating' => 9.0]
        ];

        // ⚡ ACT: Add all movies through controller
        foreach ($movies as $movieData) {
            $controller->store($movieData);
        }

        // ✅ ASSERT: Verify all movies were stored correctly
        $allStoredMovies = $repository->getAll();
        $this->assertCount(3, $allStoredMovies, "All movies should be stored");

        // Verify each movie can be found
        foreach ($movies as $originalMovie) {
            $found = $repository->getByTitle($originalMovie['name']);
            $this->assertNotNull($found, "Movie '{$originalMovie['name']}' should be findable");
        }
    }

    /**
     * TEST: REALISTIC USER SCENARIO
     *
     * 🎯 WHAT THIS TESTS:
     * A realistic scenario: User adds a movie, then searches for it
     * This tests the complete "round trip" of data through the system
     */
    public function testRealisticUserScenario()
    {
        // 🏗️ ARRANGE: Set up the system
        $repository = new MovieRepository();
        $controller = new MovieController($repository);

        // ⚡ ACT: Simulate user adding a favorite movie
        $favoriteMovie = [
            'name' => 'Spirited Away',
            'Director' => 'Hayao Miyazaki',
            'rating' => 9.3
        ];

        $controller->store($favoriteMovie);

        // Simulate user searching for their movie later
        $foundMovie = $repository->getByTitle('Spirited Away');

        // ✅ ASSERT: User can successfully find their movie
        $this->assertNotNull($foundMovie, "User should be able to find their added movie");
        $this->assertEquals('SPIRITED AWAY', $foundMovie->getName(), "Movie title should match");

        // Verify the overview contains all expected information
        $overview = $foundMovie->getOverviewText();
        $this->assertStringContainsString('Spirited Away', $overview, "Overview should contain original title");
        $this->assertStringContainsString('Hayao Miyazaki', $overview, "Overview should contain director");
        $this->assertStringContainsString('9.3', $overview, "Overview should contain rating");
    }

    /**
     * TEST: ERROR HANDLING IN INTEGRATION
     *
     * 🎯 WHAT THIS TESTS:
     * - System handles edge cases gracefully when components interact
     * - Invalid searches return appropriate responses
     * - System remains stable after error conditions
     */
    public function testSystemHandlesInvalidSearches()
    {
        // 🏗️ ARRANGE: Set up system with some data
        $repository = new MovieRepository();
        $controller = new MovieController($repository);

        $controller->store(['name' => 'Real Movie', 'Director' => 'Real Director', 'rating' => 8.0]);

        // ⚡ ACT: Search for movies that don't exist
        $nonExistentMovie = $repository->getByTitle('Movie That Does Not Exist');
        $nonExistentById = $repository->getById(99999);

        // ✅ ASSERT: System handles invalid searches gracefully
        $this->assertNull($nonExistentMovie, "Non-existent movie search should return null");
        $this->assertNull($nonExistentById, "Non-existent ID search should return null");

        // Verify system is still functional after failed searches
        $existingMovie = $repository->getByTitle('Real Movie');
        $this->assertNotNull($existingMovie, "System should still work after failed searches");
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM INTEGRATION TESTING:
 *
 * 1. **Object Collaboration**: How Controller, Repository, and Movie work together
 * 2. **Data Flow**: Following data from input through storage to retrieval
 * 3. **Realistic Scenarios**: Testing actual user workflows, not just individual methods
 * 4. **System Stability**: Ensuring the whole system works, not just individual parts
 * 5. **End-to-End Verification**: Complete feature testing from start to finish
 *
 * 💡 WHY INTEGRATION TESTING MATTERS:
 * - Unit tests verify individual components work
 * - Integration tests verify components work TOGETHER
 * - Catches bugs that only appear when objects interact
 * - Tests the actual user experience
 * - Validates architectural decisions (like dependency injection)
 *
 * 🔧 WHEN TO USE EACH TYPE:
 * - Unit Tests: Fast, focused, test individual methods
 * - Integration Tests: Test object interactions and workflows
 * - Feature Tests: Test complete user stories and business requirements
 */
