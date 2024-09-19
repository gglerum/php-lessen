<?php
define('URL', '/00%20-%20Extra/01%20-%20Regex%20&%20Formatting/validation');

require_once 'Book.php';
require_once 'Main.php';
$main = new Main();

session_start();

/**
 * Validate the book data
 * @param array $data The data to validate supplied by a form
 * @return array the errors if any
 */
function validateBook(array $data): array
{
    $errors = [];
    //check if required fields are empty
    $required = ['title', 'author', 'isbn', 'genre', 'ageRating', 'pages', 'publisher', 'publishedAt'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            //using sprintf to show the field name in the error message without the need for concatenation
            $errors[$field] = sprintf('%s is required', $field);
        }
    }

    //check if ageRating is a number
    if (!is_numeric($data['ageRating'])) {
        $errors['ageRating'] = 'Age rating must be a number';
    }

    //check if pages is a number
    if (!is_numeric($data['pages'])) {
        $errors['pages'] = 'Pages must be a number';
    }

    //check if isbn is valid
    if (!preg_match('/^\d{3}-\d{10}$/', $data['isbn'])) {
        $errors['isbn'] = 'ISBN must be in the format 000-0000000000';
    }

    return $errors;
}

if ($_POST) {
    if ($hasErrors = validateBook($_POST)) {
        $_SESSION['errors'] = $hasErrors;
        $_SESSION['data'] = $_POST;
        header('location: ' . URL . '/?action=replace');
        exit;
    }
    $_SESSION['book'] = $main->createBook($_POST);
    header('location: ' . URL);
}
if (isset($_GET['action']) == 'replace' || !isset($_SESSION['book'])) {
    //if there is data in the session, use it to prefill the form  
    if (in_array($_SESSION, ['data', 'book'])) {
        //Book object needs to cast to an array to be able to display it in the form
        $data = $_SESSION['data'] ?? $_SESSION['book'] ? (array)$_SESSION['book'] : [];
        //if date is an object, convert it to string
        if (is_object($data['publishedAt'])) {
            $data['publishedAt'] = $data['publishedAt']->format('Y-m-d');
        }
    }
    //show errors if there are any
    $errors = $_SESSION['errors'] ?? [];
    //remove temporary data from the session
    unset($_SESSION['data']);
    unset($_SESSION['errors']);

    include_once 'html/form.html';
} else if (isset($_SESSION['book'])) {
    $main->showBook($_SESSION['book']);
}
