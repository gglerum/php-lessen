<?php

/**
 * MOVIE MANAGEMENT SYSTEM - APPLICATION ENTRY POINT
 * 
 * 🎯 LEARNING OBJECTIVES:
 * This file demonstrates the "entry point" pattern used in professional PHP applications.
 * It shows how to bootstrap an application and start the main flow.
 * 
 * 🔧 WHAT THIS FILE DOES:
 * 1. Loads the Composer autoloader (automatic class loading)
 * 2. Creates the main application object
 * 3. Starts the application by calling the main menu
 * 
 * 🏗️ ARCHITECTURE ROLE:
 * This is the "front controller" - the single entry point for the entire application.
 * All requests come through this file, which then delegates to the appropriate classes.
 */

// 🔧 COMPOSER AUTOLOADER
// This magical line automatically loads any class we need without requiring manual includes.
// Composer is PHP's dependency manager, and it generates this autoloader for us.
// Compare this to hangman_v3 where we had many require_once statements!
require_once __DIR__ . '/vendor/autoload.php';

// 🚀 APPLICATION BOOTSTRAP & START
// We create the main application object and immediately start the main menu.
// This follows the "composition over inheritance" principle - the application
// is composed of objects that work together, rather than inheriting behavior.
(new Main())->showMainMenu();
