<?php

/**
 * 📚 WORDS CLASS - SIMPLE DATA CONTAINER EXAMPLE
 *
 * This class demonstrates basic OOP concepts:
 * - DATA ENCAPSULATION: Private data with public access method
 * - SIMPLE RESPONSIBILITY: Store and provide word collection
 * - ARRAY MANAGEMENT: Organizing related data in one place
 * - GETTER PATTERN: Controlled access to private data
 * 
 * 🎯 LEARNING OBJECTIVES:
 * 1. Understand basic ENCAPSULATION with private arrays
 * 2. See how classes can serve as DATA CONTAINERS
 * 3. Learn the GETTER METHOD pattern
 * 4. Practice organizing related data in objects
 * 
 * 💡 WHY IS THIS IMPORTANT?
 * This class shows the simplest form of object-oriented programming:
 * keeping related data together and providing controlled access to it.
 * Even simple data benefits from being organized in objects.
 */
class Words
{
    /**
     * 📖 PRIVATE DATA COLLECTION - ENCAPSULATED WORD LIST
     * 
     * This private property demonstrates DATA ENCAPSULATION.
     * The word list is protected from external modification while
     * still being accessible through controlled methods.
     * 
     * 💡 EDUCATIONAL WORD LIST: Fruits and foods from A-Z
     * This provides a good variety of words with different lengths
     * and complexity levels for the hangman game.
     * 
     * 🔒 ENCAPSULATION BENEFIT: External code can't accidentally
     * modify, delete, or corrupt our word list.
     * 
     * @var array Collection of words for the hangman game
     */
    private $words = [
        "apple",        // 🍎 Simple 5-letter fruit
        "banana",       // 🍌 6 letters with repeated 'a'
        "cherry",       // 🍒 6 letters with double 'r'
        "date",         // 📅 Short 4-letter word
        "elderberry",   // 🫐 Longer compound word (11 letters)
        "fig",          // 🥭 Very short 3-letter word
        "grape",        // 🍇 5 letters, good starter word
        "honeydew",     // 🍈 8 letters with repeated letters
        "kiwi",         // 🥝 4 letters with tricky 'w'
        "lemon",        // 🍋 5 letters, common word
        "mango",        // 🥭 5 letters with 'g'
        "nectarine",    // 🍑 9 letters, more challenging
        "orange",       // 🍊 6 letters, common fruit
        "pear",         // 🍐 4 letters, simple
        "quince",       // 5 letters with uncommon 'q'
        "raspberry",    // 🫐 9 letters with double letters
        "strawberry",   // 🍓 10 letters, compound word
        "tangerine",    // 🍊 9 letters, similar to orange
        "ugli",         // 4 letters, unusual word
        "vanilla",      // 7 letters with double 'l'
        "watermelon",   // 🍉 10 letters, compound word
        "xigua",        // 5 letters, uncommon word
        "yellow",       // 6 letters with double 'l'
        "zucchini"      // 🥒 8 letters, ends with 'i'
    ];

    /**
     * 🔍 GETTER METHOD - CONTROLLED DATA ACCESS
     * 
     * This method provides READ-ONLY access to our private word collection.
     * It's the standard way to expose private data in object-oriented programming.
     * 
     * 💡 DESIGN PATTERN: This is the GETTER PATTERN
     * - Private data for protection
     * - Public method for controlled access
     * - Returns copy/reference of the data
     * 
     * 🛡️ SAFETY CONSIDERATION: We return the array directly here,
     * which means external code could modify it. In more advanced
     * implementations, we might return a copy to prevent modification.
     *
     * @return array The complete collection of words for hangman game
     */
    public function get(): array
    {
        // Return our private word collection
        return $this->words;

        // 💡 LEARNING NOTE: This gives external code access to our
        // word list while keeping the property itself private.
        // This is the most basic form of controlled access.
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM THE WORDS CLASS:
 * 
 * 1. **SIMPLE ENCAPSULATION**: Basic private property with public getter
 *    - Data is protected but accessible through controlled methods
 * 
 * 2. **DATA CONTAINER PATTERN**: Classes can serve as organized storage
 *    - Related data (words for hangman) kept together in one place
 *    - Better than scattered variables throughout the application
 * 
 * 3. **SINGLE RESPONSIBILITY**: This class has one clear job
 *    - Store and provide access to word collection
 *    - Doesn't handle game logic, random selection, or display
 * 
 * 4. **ORGANIZED DATA**: Logical grouping of related information
 *    - All words are fruits/foods, providing thematic consistency
 *    - Different word lengths provide varied difficulty levels
 * 
 * 5. **FOUNDATION FOR EXTENSION**: Simple design allows easy enhancement
 *    - Could add methods to filter by length, category, difficulty
 *    - Could load words from files, databases, or APIs
 * 
 * 💡 BENEFITS OF THIS DESIGN:
 * 
 * - **CENTRALIZED**: All game words in one logical place
 * - **PROTECTED**: Private storage prevents accidental modification
 * - **ACCESSIBLE**: Public method provides easy access when needed
 * - **EXPANDABLE**: Easy to add more words or additional methods
 * 
 * 💡 COMPARISON TO PROCEDURAL CODE:
 * In procedural code, this might just be a global array variable
 * that could be modified anywhere. Objects provide better organization
 * and protection for our data.
 */
