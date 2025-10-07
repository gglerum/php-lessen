<?php

/**
 * 🎯 GAMESTATUS ENUM - TYPE SAFETY AND CONTROLLED VALUES
 * 
 * This enum demonstrates modern PHP features and important programming concepts:
 * - ENUM USAGE: Limited set of valid values (PHP 8.1+ feature)
 * - TYPE SAFETY: Prevents invalid game states
 * - SELF-DOCUMENTING CODE: Values clearly show all possible game states
 * - IMMUTABLE CONSTANTS: Values cannot be changed or corrupted
 * 
 * 🎯 LEARNING OBJECTIVES:
 * 1. Understand ENUM concept and benefits
 * 2. See how ENUMs provide TYPE SAFETY
 * 3. Learn about IMMUTABLE VALUES
 * 4. Practice using MODERN PHP FEATURES (PHP 8.1+)
 * 
 * 💡 WHY USE ENUMS?
 * Instead of using strings like "in_progress", "won", "lost" (which could
 * be mistyped), enums provide a fixed set of valid values. This prevents
 * bugs and makes code more reliable and self-documenting.
 * 
 * 📚 BEFORE ENUMS (OLD WAY):
 * ```php
 * const GAME_IN_PROGRESS = 'in_progress';  // Could be mistyped
 * const GAME_WON = 'won';                  // Magic strings everywhere
 * const GAME_LOST = 'lost';                // No IDE autocomplete
 * ```
 * 
 * 🆕 WITH ENUMS (MODERN WAY):
 * ```php
 * GameStatus::IN_PROGRESS  // IDE autocomplete + type safety
 * GameStatus::WON          // No typos possible
 * GameStatus::LOST         // Clear, self-documenting
 * ```
 */
enum GameStatus
{
/**
     * 🚀 GAME IN PROGRESS - Active gameplay state
     * 
     * This case represents the default state when:
     * - Game has just started
     * - Player is actively guessing letters/words
     * - Game has not reached win or lose condition
     * - Word is partially guessed but not complete
     * 
     * 💡 USAGE: Set when game starts, continues until win/lose
     */
    case IN_PROGRESS;

/**
     * 🏆 GAME WON - Victory state
     * 
     * This case represents the winning condition when:
     * - Player has guessed all letters in the word
     * - Player has guessed the complete word correctly
     * - Game should stop accepting new guesses
     * - Victory message should be displayed
     * 
     * 💡 USAGE: Set when word is completely guessed or correct word entered
     */
    case WON;

/**
     * 💀 GAME LOST - Defeat state
     * 
     * This case represents the losing condition when:
     * - Player has used all available attempts (typically 7)
     * - Hangman drawing is complete
     * - Game should stop accepting new guesses
     * - Reveal the correct word to player
     * 
     * 💡 USAGE: Set automatically when attempts reach zero
     */
    case LOST;
}

/**
 * 🎓 WHAT YOU LEARNED FROM GAMESTATUS ENUM:
 * 
 * 1. **ENUM CONCEPT**: Fixed set of named values representing all possible states
 *    - Only these three states are possible for a hangman game
 *    - No invalid states like "paused", "error", or typos possible
 * 
 * 2. **TYPE SAFETY**: PHP enforces that only valid enum values can be used
 *    - IDE provides autocomplete for enum cases
 *    - Typos in enum usage cause immediate errors (fail fast)
 * 
 * 3. **SELF-DOCUMENTING**: Code clearly shows all possible game states
 *    - New developers can immediately understand game flow
 *    - No need to hunt through code to find all possible states
 * 
 * 4. **MODERN PHP**: Enums are a PHP 8.1+ feature for better code quality
 *    - Replaces old constants and magic strings
 *    - Provides better developer experience and fewer bugs
 * 
 * 5. **IMMUTABLE VALUES**: Enum cases cannot be changed at runtime
 *    - Game states are stable and predictable
 *    - No accidental modification of state values
 * 
 * 💡 BENEFITS IN THE HANGMAN GAME:
 * 
 * - **Game.php** uses this enum to track current state
 * - **GameService.php** checks status to determine game flow
 * - **console.php** displays different messages based on status
 * - **No magic strings** scattered throughout the codebase
 * 
 * 💡 COMPARISON TO PROCEDURAL CODE:
 * In hangman_v2.php, game status might be tracked with strings or numbers
 * that could be mistyped or have invalid values. Enums eliminate these
 * entire categories of bugs and make the code much more reliable.
 * 
 * 💡 ENUM USAGE EXAMPLES:
 * ```php
 * // Setting status
 * $game->setStatus(GameStatus::WON);
 * 
 * // Checking status
 * if ($game->getStatus() === GameStatus::IN_PROGRESS) {
 *     // Continue game logic
 * }
 * 
 * // Switch statement
 * switch ($game->getStatus()) {
 *     case GameStatus::IN_PROGRESS:
 *         echo "Keep guessing!";
 *         break;
 *     case GameStatus::WON:
 *         echo "Congratulations!";
 *         break;
 *     case GameStatus::LOST:
 *         echo "Game over!";
 *         break;
 * }
 * ```
 */
