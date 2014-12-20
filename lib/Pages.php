<?php

namespace TheSaturn\BranchAndBound;

/**
 * Служит для разбиения решения по частям:
 * одна часть - подробное решение на выбранном этапе
 */
class    Pages implements ToHTML
{
    /**
     * int открыть часть решения с id
     * false необходимо закрыть часть решения
     * @var mixed
     */
    public $id;

    /**
     * @param int $id
     */
    function    __construct($id = false)
    {
        $this->id = $id;
    }

    /**
     * Если id задан - это будет открывающим тегом, иначе - закрывающим
     */
    function    printt()
    {
        if ($this->id !== false)
        {
            echo '<div class="page" data-id="' . $this->id . '">';
        }
        else
        {
            echo '</div>';
        }
    }
}