<?php
require_once './game/Word.php';
require_once './game/Game.php';
require_once './game/GameStatus.php';
require_once './game/DrawnHangman.php';

/**
 * GAMESERVICE CLASS - THE COORDINATOR
 * 
 * 🎯 LEARNING OBJECTIVES:
 * This class demonstrates several key Object-Oriented Programming concepts:
 * 
 * 1. **COMPOSITION**: GameService is composed of other objects (Game, Word, DrawnHangman)
 *    instead of doing everything itself. This is a fundamental OOP pattern.
 * 
 * 2. **DEPENDENCY INJECTION**: The RandomWordGenerator is passed in through the constructor
 *    rather than created inside this class. This makes the code more flexible and testable.
 * 
 * 3. **SINGLE RESPONSIBILITY**: This class has ONE clear job - coordinate the game flow.
 *    It doesn't handle word management, drawing hangman, or storing game state directly.
 * 
 * 4. **ENCAPSULATION**: Private properties protect the internal objects from direct access.
 *    Public methods provide controlled access to game functionality.
 * 
 * 🏗️ ARCHITECTURE ROLE:
 * GameService acts as the "coordinator" or "orchestrator" of the game. It:
 * - Manages the relationships between different game components
 * - Provides a simple interface for the console application to use
 * - Delegates specific tasks to specialized classes
 * 
 * Compare this to hangman_v2.php where all this logic was mixed together!
 */
class GameService
{
    // 🔒 PRIVATE PROPERTIES (Encapsulation)
    // These are the "building blocks" that make up our game.
    // They're private so only this class can directly access them.
    private Game $game;           // Stores game state (attempts, status, etc.)
    private Word $word;           // Handles word-related operations
    private DrawnHangman $drawnHangman; // Manages hangman ASCII art

    /**
     * 🏗️ CONSTRUCTOR - OBJECT INITIALIZATION
     * 
     * This is where we set up all the components our GameService needs.
     * Notice the DEPENDENCY INJECTION pattern: we receive the RandomWordGenerator
     * instead of creating it ourselves. This makes our code more flexible!
     * 
     * @param RandomWordGenerator $randomWordGenerator The word generator (injected dependency)
     */
    public function __construct(RandomWordGenerator $randomWordGenerator)
    {
        // Create the components we need
        $this->game = new Game();                                    // Game state manager
        $this->word = new Word($randomWordGenerator->get());         // Word with its operations
        $this->drawnHangman = new DrawnHangman();                   // ASCII art handler

        // 💡 LEARNING NOTE: See how we create objects and store them as properties?
        // This is COMPOSITION - building complex objects from simpler ones.
    }

    /**
     * 🎯 PUBLIC INTERFACE - GAME STATE CHECKING
     * 
     * This method demonstrates DELEGATION - we don't implement the logic ourselves,
     * we ask the appropriate object (Game) to provide the information.
     * 
     * @return bool True if the game is over, false otherwise.
     */
    public function isGameOver(): bool
    {
        // Delegate to the Game object - it knows about game state
        return $this->game->isGameOver();
    }

    /**
     * 🎮 PUBLIC INTERFACE - USER INPUT PROCESSING
     * 
     * This method shows how objects COLLABORATE. We delegate specific tasks
     * to the appropriate objects instead of handling everything ourselves.
     * 
     * @param string $input The user input (letter or word guess)
     * @return bool True if the guess was correct, false otherwise
     */
    public function userInput(string $input): bool
    {
        // 🎯 DELEGATION PATTERN: Ask the Word object to handle guessing
        $hasWordBeenGuessed = $this->word->guessWord($input);     // Try whole word
        $result = $this->word->guessLetter($input) || $hasWordBeenGuessed; // Try single letter

        // Handle incorrect guesses - decrease attempts
        if ($result === false) {
            // 🎯 DELEGATION: Ask the Game object to manage attempts
            $this->game->decreaseAttempts();
            return false;
        }

        // Handle correct guesses - check for win condition
        if ($this->word->hasWordBeenGuessed() || $hasWordBeenGuessed) {
            // 🎯 DELEGATION: Ask the Game object to update status
            $this->game->setStatus(GameStatus::WON);
        }

        return true;

        // 💡 LEARNING NOTE: See how we don't implement guessing logic here?
        // We ask the Word object to handle it. This is SEPARATION OF CONCERNS.
    }

    /**
     * 📝 PUBLIC INTERFACE - DISPLAY INFORMATION
     * 
     * This method demonstrates CONDITIONAL LOGIC and OBJECT COLLABORATION.
     * Notice how we ask multiple objects for information to make decisions.
     * 
     * @return string The word to display (partial or complete)
     */
    public function getDisplayWord(): string
    {
        // Conditional logic: Show different things based on game state
        if ($this->game->getStatus() == GameStatus::IN_PROGRESS) {
            // Game ongoing: show partial word (e.g., "h_ngm_n")
            return $this->word->getDisplayWord();
        } else {
            // Game over: reveal the full word
            return $this->word->getWord();
        }

        // 💡 LEARNING NOTE: We ask objects for information and make decisions
        // based on that information. This is how objects work together!
    }

    /**
     * 📊 PUBLIC INTERFACE - GAME STATE ACCESS
     * 
     * Simple delegation to the Game object. We provide a clean interface
     * while the Game object handles the actual data storage.
     * 
     * @return int The number of attempts remaining
     */
    public function getAttempts(): int
    {
        // Direct delegation to the Game object
        return $this->game->getAttempts();
    }

    /**
     * 🎨 PUBLIC INTERFACE - VISUAL DISPLAY
     * 
     * This shows how objects can work together to provide complex functionality.
     * We ask the Game for attempts, then ask DrawnHangman to draw based on that.
     * 
     * @return string The ASCII art hangman drawing
     */
    public function getDrawnHangman(): string
    {
        // Get data from one object, pass it to another object for processing
        $attemptsRemaining = $this->game->getAttempts();
        return $this->drawnHangman->get($attemptsRemaining);

        // 💡 LEARNING NOTE: This is COMPOSITION - using multiple objects
        // together to create more complex behavior than any single object could provide.
    }

    /**
     * 🏁 PUBLIC INTERFACE - STATUS ACCESS
     * 
     * Simple delegation method. Notice the pattern: we provide public methods
     * that give controlled access to our private objects' information.
     * 
     * @return GameStatus The current game status (IN_PROGRESS, WON, LOST)
     */
    public function getStatus(): GameStatus
    {
        // Direct delegation to the Game object
        return $this->game->getStatus();
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM GAMESERVICE:
 * 
 * 1. **COMPOSITION**: GameService is built from other objects (Game, Word, DrawnHangman)
 *    instead of implementing everything itself.
 * 
 * 2. **DELEGATION**: Methods ask appropriate objects to handle specific tasks
 *    rather than implementing the logic themselves.
 * 
 * 3. **COORDINATION**: GameService orchestrates the interaction between objects
 *    without getting involved in their internal details.
 * 
 * 4. **CLEAN INTERFACE**: Public methods provide a simple way for other code
 *    to interact with the complex game system.
 * 
 * 5. **SINGLE RESPONSIBILITY**: This class has ONE job - coordinate game flow.
 *    It doesn't draw hangman, manage words, or store game state directly.
 * 
 * 💡 COMPARISON TO PROCEDURAL CODE:
 * In hangman_v2.php, all this logic was mixed together in functions.
 * Now it's organized into logical objects that work together professionally.
 */
