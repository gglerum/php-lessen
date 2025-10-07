<?php

/**
 * EDUCATIONAL EXAMPLE: Web Application Router
 * 
 * The Router is the "traffic director" of your web application. It takes incoming
 * URLs and determines which controller method should handle each request.
 * 
 * KEY WEB CONCEPTS:
 * 
 * 1. **URL Routing**: Maps URLs like "/book/1" to controller methods
 * 2. **HTTP Methods**: Handles different request types (GET for viewing, POST for submitting)
 * 3. **RESTful Design**: Clean, predictable URL patterns
 * 4. **Parameter Extraction**: Gets IDs and data from URLs
 * 
 * EVOLUTION FROM MODULE 04:
 * - Module 04: Functions called directly based on user menu choices
 * - Module 05: Controllers called automatically based on URL patterns
 * 
 * URL PATTERN EXAMPLES:
 * - GET /           → BookController::index() (show all books)
 * - GET /book       → BookController::createBook() (show add form)
 * - POST /book      → BookController::store() (process form submission)
 * - GET /book/1     → BookController::show(1) (show book details)
 * 
 * This is a simplified educational router. Professional frameworks like Laravel
 * have much more sophisticated routing with middleware, named routes, and more.
 */
class Router
{
    /**
     * Route definitions: [HTTP method, URL pattern, Controller method]
     * 
     * Each route is an array with three elements:
     * 1. HTTP method (get/post) - determines if we're viewing or submitting data
     * 2. URL pattern - the URL structure (":id" means a variable parameter)
     * 3. Controller action - array with [ClassName, methodName] to call
     * 
     * ROUTING PATTERNS EXPLAINED:
     * - 'book/:id' matches /book/1, /book/2, etc. (":id" becomes a parameter)
     * - 'book' matches exactly /book
     * - '' matches the root URL /
     */
    private array $routes = [
        ['get', 'book/:id', [BookController::class, 'show']],     // View specific book
        ['get', 'book', [BookController::class, 'createBook']],   // Show add book form
        ['post', 'book', [BookController::class, 'store']],       // Process form submission
        ['get', '', [BookController::class, 'index']],            // Homepage: list all books
    ];

    /**
     * URL parts extracted from the request
     * Example: "/book/1" becomes ['book', '1']
     */
    private array $pathParts;

    /**
     * Constructor: Extract and parse the requested URL
     * 
     * UNDERSTANDING WEB URLS:
     * When a user visits "/book/1", the web server sets $_SERVER['PATH_INFO'] = "/book/1"
     * We need to break this into parts we can work with: ['book', '1']
     */
    public function __construct()
    {
        // Get the URL path from the web server
        // $_SERVER['PATH_INFO'] contains the part of the URL after the script name
        $pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';

        // Convert "/book/1" into ['book', '1']
        // substr($pathInfo, 1) removes the leading "/"
        // explode('/') splits on "/" characters
        $this->pathParts = explode('/', substr($pathInfo, 1));
    }

    /**
     * CORE ROUTING LOGIC: Process the current request and call the appropriate controller
     * 
     * This method implements the "Front Controller" pattern:
     * 1. Examine the incoming request (URL + HTTP method)
     * 2. Find a matching route from our route definitions
     * 3. Call the appropriate controller method
     * 4. Handle 404 errors if no route matches
     * 
     * WEB REQUEST CYCLE:
     * User clicks link/submits form → Web server calls index.php → Router examines URL → 
     * Controller handles request → View renders HTML → Browser displays result
     */
    public function processRoute(): void
    {
        // Step 1: Determine the HTTP method (GET for links, POST for form submissions)
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        // Step 2: Loop through all defined routes to find a match
        foreach ($this->routes as $route) {
            // Extract route components
            [$routeMethod, $routePath, $routeAction] = $route;

            // Step 3: Check if this route matches the current request
            if ($method === $routeMethod && $this->matchRoute($routePath)) {

                // Step 4: Handle routes with parameters (like /book/1)
                if (isset($this->pathParts[1])) {
                    $id = (int)$this->pathParts[1]; // Convert string to integer
                    $routeAction($id); // Call controller method with ID parameter
                    return; // Exit after handling the request
                }

                // Step 5: Handle routes without parameters (like /book or /)
                $routeAction(); // Call controller method without parameters
                return; // Exit after handling the request
            }
        }

        // Step 6: No route matched - return 404 Not Found
        // In professional applications, this would show a nice error page
        header('HTTP/1.1 404 Not Found');
        print '404 Not Found';
    }

    /**
     * ROUTE MATCHING LOGIC: Check if the current URL matches a route pattern
     * 
     * This method handles the complexity of matching URLs with parameters.
     * 
     * EXAMPLES:
     * - Route "book" matches URL "book" exactly
     * - Route "book/:id" matches "book/1", "book/2", etc.
     * - Route "book/:id" does NOT match "book/1/edit" (different number of parts)
     * 
     * @param string $routePath The route pattern to match against (e.g., "book/:id")
     * @return bool True if the current URL matches this route pattern
     */
    private function matchRoute(string $routePath): bool
    {
        // Step 1: Break the route pattern into parts
        // "book/:id" becomes ['book', ':id']
        $routePathParts = explode('/', $routePath);

        // Step 2: URLs must have the same number of parts to match
        // "/book/1" has 2 parts, so route must also have 2 parts
        if (count($routePathParts) !== count($this->pathParts)) {
            return false;
        }

        // Step 3: Check each part of the URL against the route pattern
        foreach ($routePathParts as $key => $routePathPart) {
            // Step 4: Skip parameter placeholders (anything starting with ":")
            // ":id", ":slug", ":category" are all parameters that match any value
            if (@$routePathPart[0] === ':') {
                continue; // This part matches any value, so keep checking other parts
            }

            // Step 5: Exact string match required for non-parameter parts
            // "book" in the route must match "book" in the URL exactly
            if ($routePathPart !== $this->pathParts[$key]) {
                return false; // This route doesn't match
            }
        }

        // If we made it here, all parts matched!
        return true;
    }
}
