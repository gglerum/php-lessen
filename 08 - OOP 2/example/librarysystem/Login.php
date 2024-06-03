<?php
trait Login
{

    /**
     * Retrieves a customer by login credentials.
     *
     * @param string $email The customer email.
     * @param string $password The customer password.
     * @return Customer|null The customer object if login is successful, null otherwise.
     */
    public static function getByLogin(string $email, string $password): mixed
    {
        $customer = self::query()->where('email = ?', [$email])->select();
        if (!$customer) {
            return null;
        }
        return password_verify($password, $customer[0]->password) ? $customer[0] : null;
    }
}
