<?php
class AdminController
{
    /**
     * Displays the login form for admins.
     */
    public static function loginForm()
    {
        require_once 'html/login.html';
    }


    /**
     * Handles the login process for admins.
     */
    public static function login()
    {
        if (!isset($_POST['email']) || !isset($_POST['password'])) {
            header('Location: index.php');
        }
        $admin = Admin::getByLogin($_POST['email'], $_POST['password']);
        if ($admin) {
            $_SESSION['admin'] = $admin;
            header('Location: index.php');
        } else {
            echo 'Invalid login';
        }
    }
}
