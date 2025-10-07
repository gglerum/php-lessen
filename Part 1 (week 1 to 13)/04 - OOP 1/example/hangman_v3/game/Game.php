<?php

/**
 * 🎮 GAME CLASS - STATE MANAGEMENT AND BUSINESS LOGIC
 * 
 * This class demonstrates crucial OOP concepts:
 * - STATE MANAGEMENT: Keeping track of game progress and status
 * - AUTOMATIC BEHAVIOR: Methods that trigger other actions
 * - VALIDATION: Ensuring game rules are enforced
 * - ENCAPSULATION: Protecting game state from invalid changes
 * 
 * 🎯 LEARNING OBJECTIVES:
 * 1. Understand how objects manage INTERNAL STATE
 * 2. See how methods can have SIDE EFFECTS (one action triggers another)
 * 3. Learn about ENUM USAGE for limited, valid values
 * 4. Practice CONDITIONAL LOGIC within object methods
 * 
 * 💡 WHY IS THIS IMPORTANT?
 * Games need to track state (score, lives, status). Objects provide a perfect
 * way to group related data and ensure it changes according to rules.
 */
class Game
{
    /**
     * 📊 PRIVATE STATE PROPERTIES - CONTROLLED DATA ACCESS
     * 
     * These properties represent the CURRENT STATE of our game.
     * They're private so external code can't accidentally break game rules.
     */

    /** 
     * 🏁 Game status using an ENUM for type safety
     * 
     * ENUM BENEFIT: Only valid statuses are allowed (IN_PROGRESS, WON, LOST)
     * This prevents bugs like accidentally setting status to "finished" or "done"
     * 
     * @var GameStatus The current game status (controlled by enum)
     */
    private GameStatus $status;

    /** 
     * ❤️ Player's remaining attempts (lives/chances)
     * 
     * DEFAULT VALUE: Set to 7 attempts (standard hangman rules)
     * This shows how properties can have sensible defaults
     * 
     * @var int Number of wrong guesses allowed before game ends
     */
    private int $attempts = 7;

    /**
     * 🏗️ CONSTRUCTOR - INITIALIZING GAME STATE
     * 
     * The constructor sets up a new game in its starting state.
     * This ensures every Game object begins consistently.
     * 
     * 💡 CONSISTENT INITIALIZATION: Every new game starts the same way,
     * preventing bugs from uninitialized or inconsistent state.
     */
    public function __construct()
    {
        // 🚀 Start every new game in Progressive
        $this->status = GameStatus::IN_PROGRESS;

        // 💡 LEARNING NOTE: $attempts already has default value of 7,
        // so we don't need to set it here. This shows different ways
        // to initialize object properties.
    }

    /**
     * ❓ COMPUTED STATE CHECK - DERIVED INFORMATION
     * 
     * This method shows how objects can provide COMPUTED VALUES based on
     * their internal state. Instead of storing "isGameOver" separately,
     * we calculate it from the current status.
     * 
     * 💡 SINGLE SOURCE OF TRUTH: We derive this from $status rather than
     * storing it separately, preventing data inconsistency.
     *
     * @return bool True if game has ended (won or lost), false if still playing
     */
    public function isGameOver(): bool
    {
        // 🧠 LOGICAL THINKING: Game is over if status is NOT "in progress"
        return $this->status !== GameStatus::IN_PROGRESS;

        // 💡 LEARNING NOTE: This method doesn't store its result anywhere.
        // It calculates the answer fresh each time it's called.
        // This ensures it's always accurate.
    }

    /**
     * 🔍 GETTER METHOD - SAFE DATA ACCESS
     * 
     * Simple getter providing READ-ONLY access to our private status.
     * External code can check the status but can't change it directly.
     * 
     * 💡 CONTROLLED ACCESS: We expose what others need to know
     * without allowing them to break our internal rules.
     *
     * @return GameStatus The current game status
     */
    public function getStatus(): GameStatus
    {
        return $this->status;
    }

    /**
     * 🔧 SETTER METHOD - CONTROLLED DATA MODIFICATION
     * 
     * This method allows controlled changes to game status.
     * Notice it's PUBLIC, meaning other objects can change the status,
     * but only through this controlled method.
     * 
     * 💡 VALIDATION OPPORTUNITY: If needed, we could add validation
     * here to ensure only valid status transitions are allowed.
     *
     * @param GameStatus $status The new game status to set
     * @return void
     */
    public function setStatus(GameStatus $status): void
    {
        $this->status = $status;

        // 💡 FUTURE ENHANCEMENT: We could add validation here:
        // - Can't go from LOST back to IN_PROGRESS
        // - Can't change from WON to LOST directly
        // This shows how encapsulation enables future improvements
    }

    /**
     * 📊 GETTER METHOD - SAFE NUMERIC ACCESS
     * 
     * Provides read-only access to remaining attempts.
     * Other objects can check how many attempts are left
     * without being able to cheat by adding more attempts.
     *
     * @return int Number of attempts remaining
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * 💥 COMPLEX BUSINESS LOGIC - AUTOMATIC STATE MANAGEMENT
     * 
     * This method demonstrates SIDE EFFECTS and AUTOMATIC BEHAVIOR.
     * One action (decreasing attempts) can trigger another action
     * (ending the game). This is powerful object-oriented design.
     * 
     * 💡 BUSINESS RULES ENFORCEMENT: The game automatically ends
     * when attempts reach zero. No external code needs to remember
     * to check this - the object handles it internally.
     *
     * @return void
     */
    public function decreaseAttempts(): void
    {
        // 📉 Reduce attempts by one
        $this->attempts--;

        // 🎯 AUTOMATIC GAME ENDING: Check if game should end
        if ($this->attempts === 0) {
            // 💀 No attempts left = player loses
            $this->status = GameStatus::LOST;
        }

        // 💡 LEARNING NOTE: This shows how object methods can have
        // SIDE EFFECTS. Calling decreaseAttempts() might also change
        // the game status. This is powerful but should be documented!
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM THE GAME CLASS:
 * 
 * 1. **STATE MANAGEMENT**: Objects are perfect for tracking changing state
 *    - Game status and attempts are kept together and consistent
 * 
 * 2. **AUTOMATIC BEHAVIOR**: Methods can trigger additional actions
 *    - decreaseAttempts() automatically ends game when attempts reach zero
 * 
 * 3. **COMPUTED VALUES**: Objects can calculate information from their state
 *    - isGameOver() is computed from status, not stored separately
 * 
 * 4. **CONTROLLED ACCESS**: Private properties with public getter/setter methods
 *    - External code can read/modify state but only through controlled methods
 * 
 * 5. **ENUM USAGE**: Using enums for type safety and clear valid values
 *    - GameStatus enum prevents invalid status values
 * 
 * 6. **CONSISTENT INITIALIZATION**: Constructor ensures every game starts correctly
 *    - No uninitialized or inconsistent starting state possible
 * 
 * 💡 COMPARISON TO PROCEDURAL CODE:
 * In hangman_v2.php, game status and attempts were separate variables that
 * had to be manually kept in sync. Now the Game object automatically
 * maintains consistency and enforces game rules.
 */
