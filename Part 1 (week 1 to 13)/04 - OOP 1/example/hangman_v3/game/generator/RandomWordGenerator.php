<?php

/**
 * 🎲 RANDOMWORDGENERATOR CLASS - DEPENDENCY INJECTION EXAMPLE
 * 
 * This class demonstrates fundamental OOP concepts:
 * - DEPENDENCY INJECTION: Receiving dependencies instead of creating them
 * - COMPOSITION: Using another object (Words) to provide functionality
 * - SINGLE RESPONSIBILITY: One job - provide random words
 * - DATA TRANSFORMATION: Converting word collection to random selection
 * 
 * 🎯 LEARNING OBJECTIVES:
 * 1. Understand DEPENDENCY INJECTION principle
 * 2. See how objects can DEPEND ON other objects
 * 3. Learn about CONSTRUCTOR INJECTION pattern
 * 4. Practice RANDOM SELECTION algorithms
 * 
 * 💡 WHY IS THIS IMPORTANT?
 * Instead of creating a Words object inside this class, we receive it
 * from outside. This makes the code flexible - we can use different
 * word sources without changing this class.
 */
class RandomWordGenerator
{
    /**
     * 📚 PRIVATE PROPERTY - INJECTED DEPENDENCY
     * 
     * This stores the word list that was provided to us during construction.
     * Notice we don't create the Words object ourselves - it's INJECTED.
     * 
     * 💡 ENCAPSULATION: Private property keeps our word list safe
     * from external modification.
     * 
     * @var array Array of words for random selection
     */
    private $words;

    /**
     * 🏗️ CONSTRUCTOR WITH DEPENDENCY INJECTION
     * 
     * This is DEPENDENCY INJECTION in action! Instead of creating
     * a Words object ourselves, we accept one as a parameter.
     * This makes our class more flexible and easier to test.
     * 
     * 💡 BENEFITS OF DEPENDENCY INJECTION:
     * - We can use different word sources (file, database, API)
     * - Easy to test (we can inject mock data)
     * - Loose coupling (we don't depend on specific Words implementation)
     * 
     * @param Words $source The word source object (INJECTED DEPENDENCY)
     */
    public function __construct(Words $source)
    {
        // 📥 EXTRACT DATA: Get the words from our dependency
        $this->words = $source->get();

        // 💡 LEARNING NOTE: We're calling a method on the injected object
        // to get the data we need. This is DELEGATION - asking another
        // object to do work for us.
    }

    /**
     * 🎲 RANDOM SELECTION - CORE BUSINESS LOGIC
     * 
     * This method provides our main service: selecting a random word.
     * It demonstrates array manipulation and random number generation.
     * 
     * 💡 ALGORITHM EXPLANATION:
     * 1. Generate random number between 0 and (array length - 1)
     * 2. Use that number as array index
     * 3. Return the word at that position
     * 
     * @return string A randomly selected word from our collection
     */
    public function get(): string
    {
        // 🎯 RANDOM ALGORITHM: Pick a random index within array bounds
        $randomIndex = rand(0, count($this->words) - 1);

        // 🎪 RETURN RANDOM WORD: Use the random index to select word
        return $this->words[$randomIndex];

        // 💡 LEARNING NOTE: We use count($this->words) - 1 because
        // arrays are zero-indexed. If we have 10 words, valid indexes
        // are 0-9, not 1-10.
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM RANDOMWORDGENERATOR CLASS:
 * 
 * 1. **DEPENDENCY INJECTION**: Receiving dependencies instead of creating them
 *    - Constructor accepts Words object instead of creating it
 *    - Makes the class flexible and testable
 * 
 * 2. **COMPOSITION OVER INHERITANCE**: Using other objects' capabilities
 *    - We use a Words object to get word data
 *    - We don't inherit from Words, we use it as a component
 * 
 * 3. **SINGLE RESPONSIBILITY**: Clear, focused purpose
 *    - One job: provide random word selection
 *    - Doesn't handle word storage, file reading, or game logic
 * 
 * 4. **LOOSE COUPLING**: Minimal dependencies on specific implementations
 *    - Depends on Words interface, not specific word source
 *    - Could work with FileWords, DatabaseWords, APIWords, etc.
 * 
 * 5. **DATA TRANSFORMATION**: Converting collection to single selection
 *    - Takes array of words, returns one random word
 *    - Encapsulates the random selection logic
 * 
 * 💡 BENEFITS OF THIS DESIGN:
 * 
 * - **FLEXIBLE**: Can use different word sources without code changes
 * - **TESTABLE**: Easy to inject test data for unit testing
 * - **REUSABLE**: Could be used in other word games
 * - **MAINTAINABLE**: Changes to word sources don't affect this class
 * 
 * 💡 DEPENDENCY INJECTION EXAMPLE:
 * 
 * ```php
 * // We can inject different word sources:
 * $fileWords = new Words();
 * $generator1 = new RandomWordGenerator($fileWords);
 * 
 * $testWords = new TestWords(['cat', 'dog', 'bird']);
 * $generator2 = new RandomWordGenerator($testWords);
 * ```
 * 
 * 💡 COMPARISON TO PROCEDURAL CODE:
 * In procedural code, we might have a function that directly reads
 * from a specific file. This class is flexible - it works with
 * any word source that implements the Words interface.
 */
