<?php

namespace TheSaturn\BranchAndBound;

/**
 * Сборник всего лога решения
 */
class    Messages
{
    /**
     * Хранит список сообщений для вывода на страницу
     * @var array
     */
    public $messages = [];

    /**
     * Новая часть решения
     * @param int $id
     */
    function    open($id)
    {
        $this->messages[] = new    Pages($id);
    }

    /**
     * Закрыть часть решения
     */
    function    close()
    {
        $this->messages[] = new    Pages;
    }

    /**
     * Добавить в лог таблицу и текст
     * @param array $table
     * @param string $text
     */
    function    add($table, $text)
    {

        $this->messages[] = new    Message($table, $text);
    }

    /**
     * Вывод всего лога
     */
    function    printt()
    {
        foreach ($this->messages as $mess)
        {
            $mess->printt();
        }
    }
}