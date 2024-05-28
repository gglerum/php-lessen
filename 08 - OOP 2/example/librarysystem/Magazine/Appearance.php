<?php

/**
 * Represents the appearance frequency of a magazine.
 */
enum Appearance: int
{
    case WEEKLY = 1;
    case MONTHLY = 2;
    case YEARLY = 3;

    /**
     * Converts an integer value to the corresponding Appearance enum value.
     *
     * @param int $id The integer value representing the Appearance.
     * @return self The Appearance enum value.
     */
    public static function fromInt(int $id): self
    {
        return match ($id) {
            1 => self::WEEKLY,
            2 => self::MONTHLY,
            3 => self::YEARLY,
        };
    }

    /**
     * Converts the Appearance enum value to its string representation.
     *
     * @return string The string representation of the Appearance.
     */
    public function toString(): string
    {
        return match ($this) {
            self::WEEKLY => 'Weekly',
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Yearly',
        };
    }
}
