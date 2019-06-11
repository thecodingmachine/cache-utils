<?php

declare(strict_types=1);

namespace TheCodingMachine\CacheUtils;

use RuntimeException;

class FileAccessException extends RuntimeException
{
    public static function cannotAccessFileModificationTime(string $fileName): self
    {
        return new self('Cannot access file modification date of "' . $fileName . '". Does this file exists?');
    }
}
