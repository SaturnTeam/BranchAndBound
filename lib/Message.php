<?php
namespace TheSaturn\BranchAndBound;

/**
 * Служит для хранения таблиц и текстовых пояснений
 */
class    Message implements ToHTML
{
    /**
     * Таблица затрат
     * @var array
     */
    public $table = [];
    /**
     * Строка
     * @var string
     */
    public $text = '';

    /**
     * @param array $table
     * @param string $text
     */
    function    __construct($table, $text)
    {
        $this->table = $table;
        $this->text = $text;
    }

    function    printt()
    {
        $this->printText();
        $this->printTable();
    }

    /**
     * Вывод таблицы
     */
    function    printTable()
    {
        if (count($this->table) == 0)
        {
            return;
        }
        $str = '<table class="table table-bordered table-sol"><tbody>';
        $str .= '<tr><td></td>';
        reset($this->table);
        foreach (current($this->table) as $columnName => $value)
        {
            $str .= "<td>$columnName</td>";
        }
        $str .= '</tr>';
        foreach ($this->table as $rowName => $row)
        {
            $str .= "<tr><td>$rowName</td>";
            foreach ($row as $columnName => $value)
            {
                $str .= "<td>$value</td>";
            }
            $str .= '</tr>';
        }
        $str .= '</tbody></table>';
        echo $str;
    }

    /**
     * Вывод текстовых данных
     */
    function    printText()
    {
        echo "<p>$this->text</p>";
    }
}