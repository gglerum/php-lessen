<?php
require_once 'Appearance.php';
/**
 * Represents a magazine in the library system.
 */
class Magazine extends DBEntity implements Borrowable
{
    use Borrow;

    /**
     * Creates a new instance of the Magazine class.
     *
     * @param int $id The ID of the magazine.
     * @param string $title The title of the magazine.
     * @param string $editor The editor of the magazine.
     * @param string $isnn The ISNN (International Standard Serial Number) of the magazine.
     * @param string $genre The genre of the magazine.
     * @param Appearance $appearance The appearance of the magazine.
     * @param string $ageRating The age rating of the magazine.
     * @param int $pages The number of pages in the magazine.
     * @param string $publisher The publisher of the magazine.
     * @param DateTime $publishedAt The date and time when the magazine was published.
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $editor,
        public readonly string $isnn,
        public readonly string $genre,
        public readonly Appearance $appearance,
        public readonly string $ageRating,
        public readonly int $pages,
        public readonly string $publisher,
        public readonly DateTime $publishedAt,
    ) { /* property promotion through constructor */
    }

    /**
     * Get string representation of when the magazine was published
     *
     * @return string period of publication
     */
    public function periodPublished(): string
    {
        return match ($this->appearance) {
            Appearance::WEEKLY => $this->publishedAt->format('Y-W') . ' (week)',
            Appearance::MONTHLY => $this->publishedAt->format('Y-m') . ' (month)',
            Appearance::YEARLY => $this->publishedAt->format('Y') . ' (year)'
        };
    }

    /**
     * Returns a builder instance for creating a Magazine object.
     *
     * @return object A builder instance.
     */
    public static function builder()
    {
        return new class
        {
            private int $id = 0;
            private string $title;
            private string $editor;
            private string $isnn;
            private string $genre;
            private Appearance $appearance;
            private string $ageRating = 'PG';
            private int $pages;
            private string $publisher;
            private DateTime $publishedAt;

            /**
             * Sets the ID of the magazine.
             *
             * @param int $id The ID of the magazine.
             * @return object The builder instance.
             */
            public function id(int $id): self
            {
                $this->id = $id;
                return $this;
            }

            /**
             * Sets the title of the magazine.
             *
             * @param string $title The title of the magazine.
             * @return object The builder instance.
             */
            public function title(string $title): self
            {
                $this->title = $title;
                return $this;
            }

            /**
             * Sets the editor of the magazine.
             *
             * @param string $editor The editor of the magazine.
             * @return object The builder instance.
             */
            public function editor(string $editor): self
            {
                $this->editor = $editor;
                return $this;
            }

            /**
             * Sets the ISNN of the magazine.
             *
             * @param string $isnn The ISNN of the magazine.
             * @return object The builder instance.
             */
            public function isnn(string $isnn): self
            {
                $this->isnn = $isnn;
                return $this;
            }

            /**
             * Sets the genre of the magazine.
             *
             * @param string $genre The genre of the magazine.
             * @return object The builder instance.
             */
            public function genre(string $genre): self
            {
                $this->genre = $genre;
                return $this;
            }

            /**
             * Sets the appearance of the magazine.
             *
             * @param int $appearanceId The ID of the appearance.
             * @return object The builder instance.
             */
            public function appearance(int $appearanceId): self
            {
                $this->appearance = Appearance::fromInt($appearanceId);
                return $this;
            }

            /**
             * Sets the age rating of the magazine.
             *
             * @param string $ageRating The age rating of the magazine.
             * @return object The builder instance.
             */
            public function age_rating(string $ageRating): self
            {
                $this->ageRating = $ageRating;
                return $this;
            }

            /**
             * Sets the number of pages in the magazine.
             *
             * @param int $pages The number of pages in the magazine.
             * @return object The builder instance.
             */
            public function pages(int $pages): self
            {
                $this->pages = $pages;
                return $this;
            }

            /**
             * Sets the publisher of the magazine.
             *
             * @param string $publisher The publisher of the magazine.
             * @return object The builder instance.
             */
            public function publisher(string $publisher): self
            {
                $this->publisher = $publisher;
                return $this;
            }

            /**
             * Sets the date and time when the magazine was published.
             *
             * @param string $dateTime The date and time when the magazine was published.
             * @return object The builder instance.
             */
            public function published_at(string $dateTime): self
            {
                $this->publishedAt = DateTime::createFromFormat('Y-m-d h:i:m', $dateTime);
                return $this;
            }

            /**
             * Builds and returns a Magazine object.
             *
             * @return Magazine The built Magazine object.
             */
            public function build(): Magazine
            {
                return new Magazine(
                    id: $this->id,
                    title: $this->title,
                    editor: $this->editor,
                    isnn: $this->isnn,
                    genre: $this->genre,
                    appearance: $this->appearance,
                    ageRating: $this->ageRating,
                    pages: $this->pages,
                    publisher: $this->publisher,
                    publishedAt: $this->publishedAt
                );
            }
        };
    }
}
