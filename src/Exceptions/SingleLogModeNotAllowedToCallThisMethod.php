<?php

namespace SoulDoit\ActivityLogger\Exceptions;

use InvalidArgumentException;

class SingleLogModeNotAllowedToCallThisMethod extends InvalidArgumentException
{
    public static function create(string $method)
    {
        return new static("The given method ($method) cannot be called in single log mode.");
    }
}
