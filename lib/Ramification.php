<?php

namespace TheSaturn\BranchAndBound;

/**
 * Для хранения множества строк и колонок
 */
class    Ramification
{
    /**
     * @var array of Coords
     */
    public $coords = [];

    /**
     * Добавление новых координат в массив
     * @param mixed $row
     * @param mixed $column
     */
    function    add($row, $column)
    {
        $this->coords[] = new    Coords($row, $column);
    }

    /**
     * Вывод всех элементов массива
     * @return string
     */
    public function    __toString()
    {
        $str = '';
        foreach ($this->coords as $coord)
        {
            $str .= "({$coord->row}:{$coord->column})";
        }
        return $str;
    }
}