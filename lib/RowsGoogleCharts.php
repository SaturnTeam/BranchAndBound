<?php

namespace TheSaturn\BranchAndBound;

/**
 * Формирователь массива с данными для Google Charts
 */
class    RowsGoogleCharts
{
    /**
     * Строки для вывода
     * @var array
     */
    public $data = [];

    function    __construct(Node $root)
    {
        $this->fillData([$root]);
    }

    protected function    fillData($leaves)
    {
        if (($cnt = count($leaves)) == 0)
        {
            return;
        }
        if ($cnt == 1)//это корень
        {
            $this->data[] = self::makeRow(strval($leaves[0]->id),
                $leaves[0]->callId, $leaves[0]->branchnBound->minBorder);
            $this->fillData($leaves[0]->childrens);
            return;
        }
        foreach ($leaves as $leaf)
        {

            $this->data[] = self::makeRow(
                $leaf->id,
                $leaf->callId,
                $leaf->branchnBound->minBorder,
                $leaf->prev->id,
                $leaf->branchnBound->rowRam,
                $leaf->branchnBound->columnRam,
                $leaf->branchnBound->includeVet);
            $this->fillData($leaf->childrens);
        }
    }

    /**
     * @param string $id
     * @param string $callId
     * @param string $minBorder
     * @param string $parentId
     * @param string $row
     * @param string $column
     * @param boolean $included
     * @return array
     */
    protected static function makeRow($id, $callId, $minBorder, $parentId = '', $row = '', $column = '', $included = true)
    {
        return [
            'id' => $id,
            'callId' => $callId,
            'minBorder' => $minBorder,
            'parentID' => $parentId,
            'row' => $row,
            'column' => $column,
            'included' => $included
        ];
    }

    /**
     * Вывод данных как массив для JS
     * @return string
     */
    function    __toString()
    {
        $str = '';
        if (!isset($_POST['google']) && isset($_POST['amount']) && !empty($_POST['amount']))
        {
            return $str;
        }
        foreach ($this->data as $leaf)
        {
            $str .= "[
			{v:'{$leaf['id']}',
			f:'<val>{$leaf['callId']}</val><div><sub>" . ($leaf['callId'] == 0 ? '' : $leaf['callId']) .
                "</sub><span " . ($leaf['included'] ? '' : 'class="overline"') .
                ">({$leaf['row']}:{$leaf['column']})</span><sub>{$leaf['minBorder']}</sub></div>'},
			 '{$leaf['parentID']}',
			 ''

			],\n";
        }
        if ($str)
        {
            $str = substr($str, 0, strlen($str) - 2);
        }
        return $str;
    }
}