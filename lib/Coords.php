<?php

namespace TheSaturn\BranchAndBound;

/**
 *    Хранение ключа строки и ключа колонки матрицы
 */
class    Coords
{
    /**
     * @var mixed
     */
    public $row;
    /**
     * @var mixed
     */
    public $column;

    /**
     *
     * @param mixed $row
     * @param mixed $column
     */
    function    __construct($row, $column)
    {
        $this->row = $row;
        $this->column = $column;
    }
    function __toString()
    {
        return "($this->row:$this->column)";
    }
}