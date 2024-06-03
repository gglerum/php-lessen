<?php
require_once 'Login.php';
/**
 * Represents a Admin in the library system.
 */
class Admin extends DBEntity
{
    use Login;
    /**
     * Creates a new instance of the Admin class.
     *
     * @param int $id The Admin ID.
     * @param string $name The Admin name.
     * @param string $email The Admin email.
     * @param string|null $password The Admin password.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly null|string $password,
    ) {/* property promotion through constructor */
    }

    /**
     * Creates a new instance of the Admin builder.
     *
     * @return object The Admin builder.
     */
    public static function builder()
    {
        return new class
        {
            private int $id = 0;
            private string $name;
            private string $email;
            private null|string $passwordHash;

            /**
             * Sets the Admin ID.
             *
             * @param int $id The Admin ID.
             * @return object The Admin builder.
             */
            public function id(int $id): self
            {
                $this->id = $id;
                return $this;
            }

            /**
             * Sets the Admin name.
             *
             * @param string $name The Admin name.
             * @return object The Admin builder.
             */
            public function name(string $name): self
            {
                $this->name = $name;
                return $this;
            }

            /**
             * Sets the Admin email.
             *
             * @param string $email The Admin email.
             * @return object The Admin builder.
             */
            public function email(string $email): self
            {
                $this->email = $email;
                return $this;
            }

            /**
             * Sets the admin password.
             *
             * @param string|null $password The Admin password.
             * @return object The Admin builder.
             */
            public function password(?string $password): self
            {
                $this->passwordHash = $password;
                return $this;
            }

            public function build(): Admin
            {
                return new Admin(
                    $this->id,
                    $this->name,
                    $this->email,
                    $this->passwordHash
                );
            }
        };
    }
}
