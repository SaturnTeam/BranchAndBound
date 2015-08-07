<?php

namespace TheSaturn\BranchAndBound;

/**
 * Узел дерева
 */
class    Node
{
    /**
     *    Сохраняет соответсвующий узлу экземпляр класса
     * @var BranchAndBound
     */
    public $branchnBound;
    /**
     * Идентификатор текущего класса (используется для пострения дерева)
     * @var int
     */
    public $id;
    /**
     * Для логгирования
     * @var Messages
     */
    public static $messages;
    /**
     * Номер в последоватлеьности использования класса
     * нужна для вывода в узле дерева
     * @var int
     */
    public $callId = 0;
    /**
     * Список потомков
     * @var array
     */
    public $childrens = [];
    /**
     * Ссылка на родителя
     * @var Node
     */
    public $prev = 0;
    /**
     * Содержик список ссылок на классы Node с минимальной стоимостью
     * и не имеющие потомков
     * @var array
     */
    public static $mins = [];
    /**
     * Ответ в произведенных вычислениях
     * @var string
     */
    public static $answer = '';

    /**
     * При инициализации нужно только предать таблицу затрат
     * @param array or BranchAndBound $table
     * @param Node $prev
     */
    function    __construct($table, Node $prev = null)
    {
        $this->id = Sequence::getID();
        if (is_array($table))
        {
            self::$messages->open(1);
            $this->branchnBound = new    BranchAndBound($table);
            $this->addMyselfToMins();
            self::$messages->close();
            $this->handleNode();
        }
        else
        {
            $this->branchnBound = $table;
            if($this->branchnBound->solvable)
            {
                $this->addMyselfToMins();
            }
            $this->prev = $prev;
        }
    }

    function addMyselfToMins()
    {
        if ($this->branchnBound->minBorder != INF)
        {
            isset(self::$mins[$this->branchnBound->minBorder])
            || self::$mins[$this->branchnBound->minBorder] = [];
            //вставка в начало для эвристики: дерево будет сначалао углубляться,
            //а потом уже расширяться
            array_unshift(self::$mins[$this->branchnBound->minBorder], $this);
        }

    }

    /**
     * Обработка узла: разделение на подмножества, переход к следующему узлу,
     * формирование ответа.
     */
    function    handleNode()
    {
        $this->callId = CallId::getID();
        self::$messages->open($this->callId);
        self::$messages->add([], "Нули на предыдущих этапах:" . $this->history());
        if ($this->branchnBound->solved)
        {
            $lastBranch = new BranchAndBound([], $this->branchnBound->minBorder);
            $lastBranch->rowRam = key($this->branchnBound->table);
            $lastBranch->columnRam = key(current($this->branchnBound->table));
            $lastBranch->includeVet = true;

            $lastBranch2 = new BranchAndBound([], INF);
            $lastBranch2->rowRam = key($this->branchnBound->table);
            $lastBranch2->columnRam = key(current($this->branchnBound->table));
            $this->childrens[] = new    Node($lastBranch2, $this);
            $this->childrens[] = new    Node($lastBranch, $this);
            $this->makeAnswer();
            return;
        }
        $devideResult = $this->branchnBound->devide();
        self::$messages->close();
        if (is_array($devideResult))
        {
            $this->addNewNodes($devideResult);
        }
        else
        {
            $this->removeMyselfFromMins();
            $this->addMyselfToMins();
            $this->chooseForWorkFromMins();
        }
    }

    /**
     * Массив с BranchAndBound's
     * @param array $devideResult
     */
    function addNewNodes($devideResult)
    {
        $this->removeMyselfFromMins();
        foreach ($devideResult as $branchnBound)
        {
            $this->childrens[] = new    Node($branchnBound, $this);
        }
        $this->chooseForWorkFromMins();
    }

    /**
     * Удаляем узел с потомками из множеств с возможными решениями
     */
    function    removeMyselfFromMins()
    {
        foreach (self::$mins as $key => &$borders)
        {
            foreach (self::$mins[$key] as $key2 => $value)
            {
                if ($value == $this)
                {
                    unset(self::$mins[$key][$key2]);
                }
            }
            if (!count(self::$mins[$key]))
            {
                unset(self::$mins[$key]);
            }
            else
            {
                reset(self::$mins[$key]);
            }
        }
    }

    /**
     * Выбор на обработку следующего узла
     */
    function    chooseForWorkFromMins()
    {
        ksort(self::$mins);
        reset(self::$mins);
        current(current(self::$mins))->handleNode();
    }

    /**
     * Формирование ответа
     */
    function    makeAnswer()
    {
        $path = [];
        $temp = $this;
        while ($temp)
        {//добавление ветвей в массив
            $path += $temp->branchnBound->fullPath;
            $temp = $temp->prev;
        }
        $from = each($path)['key'];
        $frCopy = $from;
        $res = [$from];
        do
        {//прогулка по массиву соединяя ребра
            $frCopy = $path[$frCopy];
            $res[] = $frCopy;
        } while ($frCopy != $from);
        self::$answer = implode('=>', $res);
        self::$answer = "Ответ: путь:" . self::$answer . " длина: {$this->branchnBound->minBorder}";
    }

    /**
     * История взятых и невзятых нулей на предыдущих эатпах
     * @return string
     */
    function    history()
    {
        $his = '';
        $temp = $this;
        while ($temp !== 0)
        {
            $his .= "{$temp->branchnBound->ramfication} ";
            $temp = $temp->prev;
        }
        return $his;
    }

}
