<?php

/**
 * EDUCATIONAL EXAMPLE: Web Application Entry Point
 * 
 * This is the "front controller" - the single entry point for all web requests.
 * Unlike console applications that run once and exit, web applications handle
 * multiple requests from users' browsers.
 * 
 * Key Web Development Concepts Demonstrated:
 * 1. **Single Entry Point**: All URLs route through this one file
 * 2. **Session Management**: Persistent data storage across page requests
 * 3. **MVC Bootstrap**: Initialize the application architecture
 * 4. **Request Routing**: Determine which code handles each URL
 * 
 * From Module 04 to Module 05 Evolution:
 * - Module 04: main.php creates objects and runs console loop
 * - Module 05: index.php handles web requests and routes to controllers
 */

// STEP 1: Load all required classes
// In web applications, we load everything needed at startup
require_once 'Book.php';           // Model: Data representation
require_once 'BookController.php'; // Controller: Business logic
require_once 'Router.php';         // Router: URL handling

// STEP 2: Start session for user state management
// Sessions allow data to persist between page requests
// Without sessions, each page request would be completely isolated
session_start();

// STEP 3: Initialize and run the router
// The router determines which controller method should handle this request
// based on the URL the user requested (like /book/1 or /book)
$router = new Router();
$router->processRoute(); // This examines the URL and calls the appropriate controller method
