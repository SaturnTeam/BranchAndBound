<?php

namespace TheSaturn\BranchAndBound;

/**
 * Формирование таблицы для обсчета
 */
class    TableBranchAndBound
{
    /**
     * Таблица затрат
     * @var array
     */
    public $table = [];

    function    __construct()
    {
        if (count($_POST) == 0 || isset($_POST['amount']) && !empty($_POST['amount']))
        {
            $this->random();
        }
        elseif (isset($_POST['table']))
        {
            $this->fromPost();
        }
        else
        {
            $this->fromExamples();
        }
    }

    /**
     * Генерация рандомной таблицы с учетом количества строк в $_POST['amount']
     */
    function    random()
    {
        $max = 7;
        if (isset($_POST['amount']))
        {
            $amount = (int)$_POST['amount'];
            $rows = ($amount > 2 && $amount < 20) ? $amount : 3;
        }
        else
        {
            $rows = rand(3, $max);
        }
        for ($i = 1; $i <= $rows; $i++)
        {
            for ($j = 1; $j <= $rows; $j++)
            {
                $this->table[$i][$j] = $i == $j ? INF : rand(0, 99);
            }
        }
    }

    /**
     * Заполнение таблицы на основе полученных данных
     */
    function    fromPost()
    {
        foreach ($_POST['table'] as $rowName => $row)
        {
            foreach ($row as $columnName => $value)
            {
                $this->table[$rowName][$columnName] = abs(intval($value));
            }
        }
        foreach ($this->table as $rowName => $row)
        {
            $this->table[$rowName][$rowName] = INF;
            ksort($this->table[$rowName]);
        }
        ksort($this->table);
    }

    /**
     * Вывод таблицы в HTML
     * @return string
     */
    function    __toString()
    {
        $str = '<table class="table table-bordered" id="tableInput"><tbody>';
        $str .= '<tr><td></td>';
        foreach ($this->table as $rowName => $row)
        {
            $str .= "<td>$rowName</td>";
        }
        $str .= '</tr>';
        foreach ($this->table as $rowName => $row)
        {
            $str .= "<tr><td>$rowName</td>";
            foreach ($row as $columnName => $value)
            {
                $str .= "<td>";
                $str .=
                    '<input class="form-control" type="text" value="' . $value . '" name="table[' . $rowName . '][' .
                    $columnName . ']" requied' . ($columnName == $rowName ? ' disabled' : '') . '>';
                $str .= "</td>";
            }
            $str .= '</tr>';
        }
        $str .= '</tbody></table>';
        return $str;
    }

    /**
     * Загрузка таблиц из примеров
     */
    function    fromExamples()
    {
        if (isset($_POST['pr2']))
        {
            $keys = [1, 2, 3, 4, 5, 6];
            $table = [];
            $table[1] = array_combine($keys, [INF, 26, 42, 15, 29, 25]);
            $table[2] = array_combine($keys, [7, INF, 16, 1, 30, 25]);
            $table[3] = array_combine($keys, [20, 13, INF, 35, 5, 0]);
            $table[4] = array_combine($keys, [21, 16, 25, INF, 18, 18]);
            $table[5] = array_combine($keys, [12, 46, 27, 48, INF, 5]);
            $table[6] = array_combine($keys, [23, 5, 5, 9, 5, INF]);
            $this->table = $table;
        }
        elseif (isset($_POST['pr1']))
        {

            $keys = [1, 2, 3, 4, 5];
            $tables = [];
            $table = [];
            $table[1] = array_combine($keys, [INF, 14, 9, 16, 7]);
            $table[2] = array_combine($keys, [20, INF, 9, 19, 14]);
            $table[3] = array_combine($keys, [18, 15, INF, 12, 12]);
            $table[4] = array_combine($keys, [23, 10, 13, INF, 17]);
            $table[5] = array_combine($keys, [7, 6, 6, 6, INF]);
            $table[1] = array_combine($keys, [INF, 20, 18, 12, 8]);
            $table[2] = array_combine($keys, [5, INF, 14, 7, 11]);
            $table[3] = array_combine($keys, [12, 18, INF, 6, 11]);
            $table[4] = array_combine($keys, [11, 17, 11, INF, 12]);
            $table[5] = array_combine($keys, [5, 5, 5, 5, INF]);
            $this->table = $table;
        }
        elseif (isset($_POST['pr3']))
        {

            $keys = [1, 2, 3, 4, 5, 6];
            $tables = [];
            $table = [];
            $table[1] = array_combine($keys, [INF, 7, 16, 21, 2, 17]);
            $table[2] = array_combine($keys, [13, INF, 21, 15, 43, 23]);
            $table[3] = array_combine($keys, [25, 3, INF, 31, 17, 9]);
            $table[4] = array_combine($keys, [13, 10, 27, INF, 33, 12]);
            $table[5] = array_combine($keys, [9, 2, 19, 14, INF, 51]);
            $table[6] = array_combine($keys, [42, 17, 5, 9, 23, INF]);
            $this->table = $table;
        }
    }

}
