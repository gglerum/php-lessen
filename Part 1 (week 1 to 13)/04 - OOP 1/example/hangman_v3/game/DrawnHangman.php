<?php

/**
 * 🎨 DRAWNHANGMAN CLASS - SINGLE RESPONSIBILITY PRINCIPLE
 * 
 * This class demonstrates a crucial OOP principle:
 * - SINGLE RESPONSIBILITY: One class, one job - drawing hangman art
 * - PURE FUNCTION: Same input always produces same output
 * - NO STATE: No properties to track, just behavior
 * - CLEAR PURPOSE: Name tells you exactly what it does
 * 
 * 🎯 LEARNING OBJECTIVES:
 * 1. Understand SINGLE RESPONSIBILITY PRINCIPLE
 * 2. See how classes can provide UTILITY FUNCTIONS
 * 3. Learn about STATELESS objects (no properties)
 * 4. Practice SWITCH STATEMENT for multiple conditions
 * 
 * 💡 WHY IS THIS IMPORTANT?
 * By giving the drawing logic its own class, we separate concerns.
 * The Game class doesn't need to know HOW to draw - it just asks
 * DrawnHangman to do it. This makes code easier to modify and test.
 */
class DrawnHangman
{
    /**
     * 🎭 SINGLE RESPONSIBILITY METHOD - PURE VISUAL FUNCTION
     * 
     * This method has ONE clear job: convert an attempt number into
     * ASCII art. It demonstrates several important concepts:
     * 
     * - STATELESS: No class properties needed
     * - PREDICTABLE: Same input always gives same output
     * - ISOLATED: Doesn't depend on anything else
     * - FOCUSED: Only handles visual representation
     * 
     * 💡 DESIGN PATTERN: This is a UTILITY METHOD - it provides
     * a service (drawing) without maintaining state.
     *
     * @param int $attempts The number of attempts remaining (0-7)
     * @return string ASCII art hangman drawing corresponding to attempts
     */
    public function get(int $attempts): string
    {
        $display = '';

        // 🎯 SWITCH STATEMENT: Clean way to handle multiple specific values
        // Each case represents a different stage of the hangman drawing
        switch ($attempts) {
            case 6:
                // 🏗️ STAGE 1: Just the gallows (first wrong guess)
                $display = "  +---+\n  |   |\n      |\n      |\n      |\n      |\n=========\n";
                break;
            case 5:
                // 😵 STAGE 2: Add the head (second wrong guess)
                $display = "  +---+\n  |   |\n  O   |\n      |\n      |\n      |\n=========\n";
                break;
            case 4:
                // 🫨 STAGE 3: Add the body (third wrong guess)
                $display = "  +---+\n  |   |\n  O   |\n  |   |\n      |\n      |\n=========\n";
                break;
            case 3:
                // 🤚 STAGE 4: Add left arm (fourth wrong guess)
                $display = "  +---+\n  |   |\n  O   |\n /|   |\n      |\n      |\n=========\n";
                break;
            case 2:
                // 🙌 STAGE 5: Add right arm (fifth wrong guess)
                $display = "  +---+\n  |   |\n  O   |\n /|\\  |\n      |\n      |\n=========\n";
                break;
            case 1:
                // 🦵 STAGE 6: Add left leg (sixth wrong guess)
                $display = "  +---+\n  |   |\n  O   |\n /|\\  |\n /    |\n      |\n=========\n";
                break;
            case 0:
                // 💀 STAGE 7: Add right leg - GAME OVER! (seventh wrong guess)
                $display = "  +---+\n  |   |\n  O   |\n /|\\  |\n / \\  |\n      |\n=========\n";
                break;
            default:
                // 🏁 DEFAULT: Empty gallows (game start or invalid input)
                $display = "  +---+\n      |\n      |\n      |\n      |\n      |\n=========\n";
        }

        return $display;

        // 💡 LEARNING NOTE: Notice how this method doesn't store anything.
        // It just takes input, processes it, and returns output.
        // This is a PURE FUNCTION - no side effects!
    }
}

/**
 * 🎓 WHAT YOU LEARNED FROM DRAWNHANGMAN CLASS:
 * 
 * 1. **SINGLE RESPONSIBILITY PRINCIPLE**: This class has exactly one job
 *    - It draws hangman art based on attempts remaining
 *    - It doesn't track game state, handle input, or manage words
 * 
 * 2. **STATELESS DESIGN**: No properties needed
 *    - The class provides behavior without maintaining internal data
 *    - This makes it simple, predictable, and easy to test
 * 
 * 3. **UTILITY PATTERN**: Pure function wrapped in a class
 *    - Same input always produces same output
 *    - No dependencies on other objects or external state
 * 
 * 4. **CLEAR SEPARATION OF CONCERNS**: Visual logic is isolated
 *    - Game logic doesn't mix with drawing logic
 *    - Easy to change the art without affecting game rules
 * 
 * 5. **SWITCH STATEMENT USAGE**: Clean way to handle discrete values
 *    - Each attempt count maps to a specific drawing stage
 *    - Default case handles unexpected values gracefully
 * 
 * 💡 BENEFITS OF THIS DESIGN:
 * 
 * - **EASY TO TEST**: Just call get() with different numbers and check output
 * - **EASY TO MODIFY**: Want different art? Just change this class
 * - **REUSABLE**: Other games could use this for their hangman drawing
 * - **ISOLATED**: Changes here won't break other parts of the game
 * 
 * 💡 COMPARISON TO PROCEDURAL CODE:
 * In hangman_v2.php, the drawing logic might be mixed with game logic
 * in a big function. Now it's cleanly separated and can be easily
 * modified, tested, or even replaced with a different drawing style.
 */
