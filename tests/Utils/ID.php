<?php

namespace Test\Utils;

class ID
{
    // Generates Unique id for testing, duplicates happen very rarely
    // and is acceptable for testing purposes
    public static function generate(): int
    {
        usleep(1000);
        $time = (string)microtime(true);

        return (int)substr($time, -4);
    }
}
