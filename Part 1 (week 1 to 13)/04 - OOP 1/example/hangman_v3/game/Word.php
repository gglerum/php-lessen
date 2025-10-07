<?php

/**
 * 📝 WORD CLASS - ENCAPSULATION AND DATA MANAGEMENT EXAMPLE
 * 
 * This class demonstrates fundamental OOP concepts:
 * - ENCAPSULATION: Private data with controlled public access
 * - DATA INTEGRITY: Protecting internal state from corruption
 * - RESPONSIBILITY: One class handles all word-related operations
 * 
 * 🎯 LEARNING OBJECTIVES:
 * 1. Understand how PRIVATE properties protect data
 * 2. See how PUBLIC methods provide controlled access
 * 3. Learn about CLASS RESPONSIBILITY and logical organization
 * 4. Practice ARRAY MANIPULATION within objects
 * 
 * 💡 WHY IS THIS IMPORTANT?
 * In procedural code, the word and guessed letters might be separate variables
 * that could accidentally be modified anywhere. Objects keep related data together
 * and control how it can be changed.
 */
class Word
{
    /**
     * 🔒 PRIVATE PROPERTIES - ENCAPSULATION IN ACTION
     * 
     * These properties are PRIVATE, meaning they can only be accessed
     * from within this class. This is ENCAPSULATION - protecting data
     * from being accidentally modified by outside code.
     */

    /** @var string The secret word to be guessed (PRIVATE = protected data) */
    private string $word;

    /** @var array List of letters the player has guessed (PRIVATE = controlled access) */
    private array $guessedLetters = [];

    /**
     * 🏗️ CONSTRUCTOR - SETTING UP THE OBJECT
     * 
     * The constructor initializes our object with the data it needs.
     * Notice how we take a parameter and store it in our PRIVATE property.
     * 
     * 💡 ENCAPSULATION EXAMPLE: The word is set once and can't be changed
     * accidentally from outside the class.
     *
     * @param string $word The word to be guessed
     */
    public function __construct(string $word)
    {
        // Store the word in our private property
        $this->word = $word;

        // guessedLetters starts as empty array (already initialized above)
        // This ensures every Word object starts in a clean state
    }

    /**
     * 🔍 PUBLIC ACCESS METHOD - GETTER
     * 
     * This is a GETTER method - it provides READ-ONLY access to our private data.
     * External code can see the word but cannot change it directly.
     * 
     * 💡 CONTROLLED ACCESS: We decide what data to expose and how.
     *
     * @return string The complete word (usually shown when game ends)
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * 🎮 BUSINESS LOGIC - COMPLEX DATA TRANSFORMATION
     * 
     * This method shows how objects can provide COMPUTED VALUES based on their
     * internal state. It combines the secret word with guessed letters to
     * create the display version (e.g., "h_ngm_n").
     * 
     * 💡 ENCAPSULATION BENEFIT: All the logic for displaying the word is
     * contained in the Word class where it belongs.
     *
     * @return string The word with guessed letters revealed and others as underscores
     */
    public function getDisplayWord(): string
    {
        $displayWord = '';

        // 🔄 ITERATE through each letter of the secret word
        foreach (str_split($this->word) as $letter) {
            // 🤔 CONDITIONAL LOGIC: Show letter if guessed, otherwise show underscore
            if (in_array($letter, $this->guessedLetters)) {
                $displayWord .= $letter;  // Letter was guessed - show it
            } else {
                $displayWord .= '_';      // Letter not guessed - hide it
            }
        }

        return $displayWord;

        // 💡 LEARNING NOTE: This method reads our private data but doesn't
        // expose it directly. It processes it and returns a computed result.
    }

    /**
     * 🎯 STATE MODIFICATION - CONTROLLED DATA CHANGES
     * 
     * This method demonstrates how to SAFELY MODIFY private data through
     * public methods. It also shows VALIDATION and STATE MANAGEMENT.
     * 
     * 💡 ENCAPSULATION BENEFIT: We control HOW the guessedLetters array
     * is modified, preventing duplicates and invalid data.
     *
     * @param string $letter The letter to guess
     * @return bool True if the letter is in the word, false otherwise
     */
    public function guessLetter(string $letter): bool
    {
        // 🛡️ DUPLICATE PREVENTION: Only add letter if not already guessed
        if (!in_array($letter, $this->guessedLetters)) {
            $this->guessedLetters[] = $letter;
        }

        // 🔍 CHECK AND RETURN: Is this letter in our secret word?
        return str_contains($this->word, $letter);

        // 💡 LEARNING NOTE: We modify our private array in a controlled way,
        // preventing duplicates and ensuring data integrity.
    }

    /**
     * 🎯 SIMPLE VALIDATION - DIRECT COMPARISON
     * 
     * This method shows simple business logic - comparing the guess
     * against our private data. No state is modified here.
     * 
     * 💡 SINGLE RESPONSIBILITY: This method has ONE job - check if
     * the guessed word matches our secret word.
     *
     * @param string $word The complete word guess
     * @return bool True if the guessed word matches exactly, false otherwise
     */
    public function guessWord(string $word): bool
    {
        // Simple comparison - does the guess match our secret word?
        return $this->word === $word;

        // 💡 LEARNING NOTE: We use === for EXACT comparison
        // This ensures case-sensitive matching
    }

    /**
     * 🏁 WIN CONDITION CHECK - COMPUTED STATE
     * 
     * This method demonstrates how objects can determine their own state
     * by using their own methods. It shows METHOD REUSE and LOGICAL THINKING.
     * 
     * 💡 METHOD REUSE: Instead of duplicating logic, we call our own
     * getDisplayWord() method and compare results.
     *
     * @return bool True if all letters have been guessed, false otherwise
     */
    public function hasWordBeenGuessed(): bool
    {
        // 🧠 CLEVER LOGIC: If display word equals real word, 
        // then all letters must have been guessed!
        return $this->word === $this->getDisplayWord();

        // 💡 LEARNING NOTE: We reuse our own method instead of duplicating
        // the logic. This is DRY principle (Don't Repeat Yourself).
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM THE WORD CLASS:
 * 
 * 1. **ENCAPSULATION**: Private properties protect data from external modification
 *    - $word and $guessedLetters are private and safe
 * 
 * 2. **CONTROLLED ACCESS**: Public methods provide safe ways to interact with data
 *    - getWord() for reading, guessLetter() for safe modification
 * 
 * 3. **DATA INTEGRITY**: Methods ensure data remains valid
 *    - No duplicate letters in guessedLetters array
 * 
 * 4. **COMPUTED VALUES**: Objects can provide processed versions of their data
 *    - getDisplayWord() creates a view of the data without exposing internals
 * 
 * 5. **SINGLE RESPONSIBILITY**: Each method has one clear purpose
 *    - guessLetter() handles letter guessing, guessWord() handles word guessing
 * 
 * 6. **METHOD REUSE**: Objects can use their own methods to avoid code duplication
 *    - hasWordBeenGuessed() reuses getDisplayWord()
 * 
 * 💡 COMPARISON TO PROCEDURAL CODE:
 * In hangman_v2.php, word and guessed letters were separate variables that
 * could be accidentally modified anywhere. Now they're safely encapsulated
 * in an object with controlled access methods.
 */
