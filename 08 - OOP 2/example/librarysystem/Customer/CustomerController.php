<?php

/**
 * The CustomerController class handles customer-related actions and requests.
 */
class CustomerController
{
    /**
     * Displays the login form for customers.
     */
    public static function loginForm()
    {
        require_once 'html/login.html';
    }

    /**
     * Handles the login process for customers.
     */
    public static function login()
    {
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            header('Location: index.php');
        }
        $customer = Customer::getByLogin($_POST['email'], $_POST['password']);
        if ($customer) {
            $_SESSION['customer'] = $customer;
            header('Location: index.php');
        } else {
            echo 'Invalid login';
        }
    }
}
