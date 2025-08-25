<?php

namespace App\ValueObjects;

/**
 * FileSize Value Object: Represents the size of a file in bytes.
 */
class FileSize
{
    private int $bytes;

    public function __construct(int $bytes)
    {
        if ($bytes < 0) {
            throw new InvalidArgumentException('File size cannot be negative');
        }
        $this->bytes = $bytes;
    }

    /**
     * Create a FileSize instance from files.
     *
     * @param array $files
     * @return self
     */
    public static function fromFiles(array $files): self
    {
        $totalSize = array_reduce($files, fn(int $carry, $file) => $carry + $file->getSize(), 0);
        return new self($totalSize);
    }

    /**
     * Create a FileSize instance from bytes.
     * @param int $bytes
     * @return FileSize
     */
    public static function fromBytes(int $bytes): self
    {
        return new self($bytes);
    }

    /**
     * Check if the current file size exceeds the given limit.
     * @param \App\ValueObjects\FileSize $limit
     * @return bool
     */
    public function exceedsLimit(FileSize $limit): bool
    {
        return $this->bytes > $limit->bytes;
    }

    /**
     * Add another FileSize instance to the current one.
     * @param \App\ValueObjects\FileSize $other
     * @return FileSize
     */
    public function add(FileSize $other): self
    {
        return new self($this->bytes + $other->bytes);
    }

    /**
     * Convert the FileSize instance to bytes.
     * @return int
     */
    public function toBytes(): int
    {
        return $this->bytes;
    }
}
