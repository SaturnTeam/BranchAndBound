<?php

namespace TheSaturn\BranchAndBound;

/**
 * Перегрузка функции max для исключения бесконечностей
 * @param array $arr
 * @return mixed
 */
function    max($arr)
{
    foreach ($arr as $key => $value)
    {
        if (is_infinite($value))
        {
            unset($arr[$key]);
        }
    }
    return $arr ? \max($arr) : 0;
}

/**
 * Перегрузка функции min для исключения бесконечностей
 * @param array $arr
 * @return mixed
 */
function    min($arr)
{
    foreach ($arr as $key => $value)
    {
        if (is_infinite($value))
        {
            unset($arr[$key]);
        }
        else
        {
            break;
        }
    }
    return $arr ? \min($arr) : 0;
}

/**
 * Класс совершает все необходимые операции над узлом дерева,
 * в частности разбиение на ветви
 */
class    BranchAndBound
{
    /**
     * Таблица затрат
     * @var array
     */
    public $table = [];
    /**
     * Стоимость
     * @var int
     */
    public $minBorder = 0;
    /**
     * Для логгирования
     * @var Messages
     */
    public static $messages;
    /**
     * Решаема ли матрица или нет
     * @var boolean
     */
    public $solvable = true;
    /**
     * Получившиеся звенья на этапах ( необходима для предотвращения циклов)
     * @var array
     */
    public $path = [];
    /**
     * Хранит все предыдущие ребра "как есть", без удаления
     * @var array
     */
    public $fullPath = [];
    /**
     * Хранит возможные ответления на данном этапе
     * @var Ramification
     */
    public $ramfication;
    /**
     * Была ли включена ветка $rowRam $columnRam
     * Нужна для графического построения дерева
     * @var bool
     */
    public $includeVet = false;
    /**
     *    Строка от которой произшла данная матрица
     * @var int
     */
    public $rowRam;
    /**
     *    Колонка от которой произшла данная матрица
     * @var int
     */
    public $columnRam;
    public $solved = false;

    /**
     *
     * @param array $table
     * @param int $minBorder
     * @param array $path
     */
    function    __construct($table, $minBorder = null, $path = [])
    {
        $this->ramfication = new    Ramification;
        $this->table = $table;
        if ($minBorder === null)
        {
            $this->doOperations();
        }
        else
        {
            $this->minBorder = $minBorder;
            $this->path = $path;
        }
    }

    /**
     * Нахождение минимальных по строкам
     * @return array
     */
    function    minFromRows()
    {
        self::addMess([], 'Нахождение минимальных по строкам');
        $mins = [];
        $table = $this->table;
        foreach ($table as $key => $value)
        {
            $mins[$key] = min($value);
        }
        self::addMess([], "Мнимальные по строкам:" . implode(' ', $mins));
        return $mins;
    }

    /**
     * Нахождение минимальных по столбцам
     * @return array
     */
    function    minFromColumns()
    {
        self::addMess([], 'Нахождение минимальных по столбцам');
        $mins = [];
        reset($this->table);
        $columns = array_keys(current($this->table));
        foreach ($columns as $column)
        {
            $temp = [];
            foreach ($this->table as $key => $row)
            {
                $temp[] = $row[$column];
            }
            $mins[$column] = min($temp);
        }
        self::addMess([], "Мнимальные по столбцам:" . implode(' ', $mins));
        return $mins;
    }

    /**
     * Вычитание минимумов по строке
     */
    function    subEveryRow()
    {
        self::addMess($this->table, 'Вычитание минимумов по строке');
        $mins = $this->minFromRows();
        $this->minBorder += array_sum($mins);
        self::addMess([], "Почти новая мин граница $this->minBorder");
        foreach ($mins as $row => $min)
        {
            foreach ($this->table[$row] as &$column)
            {
                $column -= $min;
            }
        }
        self::addMess([], 'Результат вычитания минимумов по строке');
    }

    /**
     * Вычитание минимумов по столбцам
     */
    function    subEveryColumn()
    {
        self::addMess([], 'Вычитание минимумов по столбцам');
        $mins = $this->minFromColumns();
        $this->minBorder += array_sum($mins);
        self::addMess([], "Новая мин граница $this->minBorder");
        reset($this->table);
        $columns = array_keys(current($this->table));
        foreach ($columns as $column)
        {
            $temp = [];
            foreach ($this->table as &$row)
            {
                $row[$column] -= $mins[$column];
            }
            $mins[] = min($temp);
        }
        self::addMess($this->table, "Результат вычитания минимумов по столбцам");
    }

    /**
     * Подсчитывает штрафы у нулей
     * Возвращает максимальное значение
     * и массив с координатами нулей и их штрафами
     * @return array
     */
    function    zeroDegreeMax()
    {
        self::addMess([], "Начало подсчета штрафов у нулей");
        $sumsArr = [];
        foreach ($this->table as $i => $row)
        {
            foreach ($row as $j => $column)
            {
                if ($column == 0)
                {
                    $sumsArr[$i][$j] = $this->sumMinRowColumn($i, $j);
                }
            }
        }
        $str = "Подсчитанные степени у нулей:";
        foreach ($sumsArr as $i => $row)
        {
            foreach ($row as $j => $value)
            {
                $str .= "<br>($i:$j)=$value";
            }
        }
        self::addMess([], $str);
        self::addMess([], "Конец подсчета штрафов у нулей");
        $max = [];
        foreach ($sumsArr as $i => $row)
        {
            $max[$i] = max($row);
        }
        self::addMess([], "Максимумы по строкам:" . implode(' ', $max));
        return [max($max), $sumsArr];
    }

    /**
     * Находит позиции нулей соответсвующий максимальному штрафу
     * Генерирует исключение в случае тупиковой ситуации: т.е. нет решения по этой ветви
     * @throws Exception
     */
    function    zeroDegreePosition()
    {
        if (!$this->addToRamfication($this->zeroDegreeMax()))
        {
            throw    new    Exception("", 1);
        }
        self::addMess([], "Максимальная степень 0 находятся на позициях $this->ramfication");
    }
    /**
     * Добавляем координаты для разделения
     * @return boolean
     */
    private function addToRamfication($zeroDegreeMax)
    {
        list($max, $sumsArr) = $zeroDegreeMax;
        foreach ($sumsArr as $i => $row)
        {
            foreach ($row as $j => $column)
            {
                if ($column == $max)
                {
                    $this->ramfication->add($i, $j);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Выполняет операции над текущей матрицей затрат
     */
    function    doOperations()
    {
        $this->subEveryRow();
        $this->subEveryColumn();
        $this->zeroDegreePosition();
    }

    /**
     * подсчитывает штраф нуля с координатами $row:$column
     * @param int $row
     * @param int $column
     * @return int
     */
    function    sumMinRowColumn($row, $column)
    {
        $table = $this->table;
        $rowArr = $table[$row];
        $columnArr = [];
        reset($this->table);
        $columns = array_keys(current($this->table));

        foreach ($this->table as $key => $rowT)
        {
            if ($key != $row)
            {
                $columnArr[] = $rowT[$column];
            }
        }
        $c1 = $columnArr;
        unset($rowArr[$column]);
        return min($rowArr) + min($columnArr);
    }

    /**
     * Разделение на подмножества
     * возврат массива с объектами BranchAndBound либо
     * возврат true в случае если мин стоимость не является бесконечностью
     * возврат false в случае тупика
     * @return mixed
     */
    function    devide()
    {
        if (count($this->table) == 1)
        {
            self::addMess($this->table, 'В таблице всего один элемент');
            $coord = $this->ramfication->coords[0];
            reset($this->table);
            $this->path[key($this->table)] = key(current($this->table));
            self::addMess([], "добавили в путь $coord->row:$coord->column");
            $this->fullPath[$coord->row] = $coord->column;
            $this->minBorder += current(current($this->table));
            $this->solved = true;
            return $this->minBorder != INF;
        }
        self::addMess($this->table, 'Начинаем разделение');
        foreach ($this->ramfication->coords as $key => $coord)
        {

        self::addMess([], $coord);
        }
        $return = [];
        foreach ($this->ramfication->coords as $key => $coord)
        {

            $branchnBound1 = $this->withoutCoordsHandle($coord);
            $branchnBound2 = $this->withCoordsHandle($coord);
            $str = "Граница у несодержащего ребро ($coord->row,$coord->column):";
            $str .= $branchnBound1->solvable ? $branchnBound1->minBorder : "INF";
            $str .= " у содержащего";
            $str .= $branchnBound2->solvable ? $branchnBound2->minBorder : "INF";
            self::addMess([], $str);
            $return[] = $branchnBound1;
            $return[] = $branchnBound2;
        }
        return $return;
    }

    /**
     * Возвращает объект в случае успешной обработки таблицы без ребра
     * @param \TheSaturn\BranchAndBound\Coords $coord
     * @return \TheSaturn\BranchAndBound\BranchAndBound|boolean
     */
    protected function withoutCoordsHandle(Coords $coord)
    {
        $table1 = $this->table;
        $table1[$coord->row][$coord->column] = INF;
        $table1 = $this->preventCycle($table1);
        $branchnBound1 = new    BranchAndBound($table1, $this->minBorder, $this->path);
        $branchnBound1->rowRam = $coord->row;
        $branchnBound1->columnRam = $coord->column;
        self::addMess($table1, "Старт обработки множества не включающего в себя ребро ($coord->row,$coord->column)");
        try
        {
            $branchnBound1->doOperations();
        } catch (Exception    $e)
        {
            $branchnBound1->solvable = false;
        }
        return $branchnBound1;
    }

    /**
     * Возвращает объект в случае успешной обработки таблицы с ребром
     * @param \TheSaturn\BranchAndBound\Coords $coord
     * @return \TheSaturn\BranchAndBound\BranchAndBound|boolean
     */
    protected function withCoordsHandle(Coords $coord)
    {
        $table2 = $this->table;
        $table2 = $this->unsetRowColumn($table2, $coord->row, $coord->column);
        isset($table2[$coord->column][$coord->row]) &&
        $table2[$coord->column][$coord->row] = INF;
        $table2 = $this->preventCycle($table2);
        $branchnBound2 = new    BranchAndBound($table2, $this->minBorder, $this->path);
        $branchnBound2->path[$coord->row] = $coord->column;
        $branchnBound2->fullPath[$coord->row] = $coord->column;
        $branchnBound2->includeVet = true;
        $branchnBound2->rowRam = $coord->row;
        $branchnBound2->columnRam = $coord->column;;
        self::addMess($table2, "Страт обработки множества включающего в себя ребро ($coord->row,$coord->column)");
        try
        {
            $branchnBound2->doOperations();
        } catch (Exception    $e)
        {
            $branchnBound2->solvable = false;
        }
        return $branchnBound2;
    }

    /**
     * Предотвращаение негамильтоновых контуров (циклов)
     * изменяет $this->path
     * @param array $table
     * @return array
     */
    function    preventCycle($table)
    {
        self::addMess([], "Поиск циклов");
        $paths =	&$this->path;
        $pathCopy = $this->path;
        foreach ($paths as $row => $column)
        {
            isset($table[$column][$row]) &&
            $table[$column][$row] = INF;
            foreach ($pathCopy as $rowCopy => $columnCopy)
            {
                if ($row == $columnCopy)
                {
                    $paths[$rowCopy] = $column;
                    unset($paths[$row]);
                    isset($table[$rowCopy][$column]) &&
                    $table[$rowCopy][$column] = INF;
                    isset($table[$column][$rowCopy]) &&
                    $table[$column][$rowCopy] = INF;
                    self::addMess([], "Цикл найден. уничтожен [$rowCopy][$column]");
                    return $this->preventCycle($table);
                }
            }
        }
        self::addMess([], "Цикл не найден");
        return $table;
    }

    /**
     * Удаление из таблицы соответсвующей строки и колонки
     * @param array $table
     * @param int $row
     * @param int $column
     * @return array
     */
    function    unsetRowColumn($table, $row, $column)
    {
        self::addMess($table, "Удаление из матрицы $row:$column");
        unset($table[$row]);
        foreach ($table as $key => &$value)
        {
            unset($value[$column]);
        }
        self::addMess($table, "Результат удаления из матрицы $row:$column");
        return $table;
    }

    /**
     * Добавляет новое сообщение
     * @param array $tabe
     * @param string $text
     */
    static function    addMess($tabe, $text)
    {
        return self::$messages->add($tabe, $text);
    }
}
