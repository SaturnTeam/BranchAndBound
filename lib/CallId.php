<?php

namespace TheSaturn\BranchAndBound;

/**
 * Служит для генерирования порядка обработки таблицы из класса Node
 */
class    CallId
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