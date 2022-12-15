<?php

namespace SoulDoit\ActivityLogger\Exceptions;

use InvalidArgumentException;

class TrackedLoggingAlreadyStopped extends InvalidArgumentException
{
    public static function create()
    {
        return new static("The tracked logging already stopped.");
    }
}
