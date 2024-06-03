<?php
require_once 'Login.php';
/**
 * Represents a customer in the library system.
 */
class Customer extends DBEntity
{
    use Login;
    /**
     * Creates a new instance of the Customer class.
     *
     * @param int $id The customer ID.
     * @param string $name The customer name.
     * @param string $email The customer email.
     * @param string|null $phone The customer phone number.
     * @param string|null $address The customer address.
     * @param string|null $city The customer city.
     * @param string|null $postalCode The customer postal code.
     * @param string|null $password The customer password.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly null|string $phone,
        public readonly null|string $address,
        public readonly null|string $city,
        public readonly null|string $postalCode,
        public readonly null|string $password,
    ) {/* property promotion through constructor */
    }

    /**
     * Creates a new instance of the Customer builder.
     *
     * @return object The Customer builder.
     */
    public static function builder()
    {
        return new class
        {
            private int $id = 0;
            private string $name;
            private string $email;
            private null|string $phone;
            private null|string $address;
            private null|string $city;
            private null|string $postalCode;
            private null|string $passwordHash;

            /**
             * Sets the customer ID.
             *
             * @param int $id The customer ID.
             * @return object The Customer builder.
             */
            public function id(int $id): self
            {
                $this->id = $id;
                return $this;
            }

            /**
             * Sets the customer name.
             *
             * @param string $name The customer name.
             * @return object The Customer builder.
             */
            public function name(string $name): self
            {
                $this->name = $name;
                return $this;
            }

            /**
             * Sets the customer email.
             *
             * @param string $email The customer email.
             * @return object The Customer builder.
             */
            public function email(string $email): self
            {
                $this->email = $email;
                return $this;
            }

            /**
             * Sets the customer phone number.
             *
             * @param string|null $phone The customer phone number.
             * @return object The Customer builder.
             */
            public function phone(?string $phone): self
            {
                $this->phone = $phone;
                return $this;
            }

            /**
             * Sets the customer address.
             *
             * @param string|null $address The customer address.
             * @return object The Customer builder.
             */
            public function address(?string $address): self
            {
                $this->address = $address;
                return $this;
            }

            /**
             * Sets the customer city.
             *
             * @param string|null $city The customer city.
             * @return object The Customer builder.
             */
            public function city(?string $city): self
            {
                $this->city = $city;
                return $this;
            }

            /**
             * Sets the customer postal code.
             *
             * @param string|null $postalCode The customer postal code.
             * @return object The Customer builder.
             */
            public function postal_code(?string $postalCode): self
            {
                $this->postalCode = $postalCode;
                return $this;
            }

            /**
             * Sets the customer password.
             *
             * @param string|null $password The customer password.
             * @return object The Customer builder.
             */
            public function password(?string $password): self
            {
                $this->passwordHash = $password;
                return $this;
            }

            /**
             * Builds a new instance of the Customer class.
             *
             * @return Customer The built Customer object.
             */
            public function build(): Customer
            {
                return new Customer(
                    $this->id,
                    $this->name,
                    $this->email,
                    $this->phone,
                    $this->address,
                    $this->city,
                    $this->postalCode,
                    $this->passwordHash
                );
            }
        };
    }
}
