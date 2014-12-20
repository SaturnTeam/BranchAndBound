<?php

namespace TheSaturn\BranchAndBound;

/**
 * Служит для генерирования порядка создания нового класса Node
 */
class    Sequence
{
    /**
     * @var int
     */
    public static $count = 1;

    /**
     * @return int
     */
    public static function    getID()
    {
        return self::$count++;
    }
}
