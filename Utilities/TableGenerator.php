<?php
require_once 'Console/Table.php';
require_once 'Probability.php';
class TableGenerator
{
    public $tabl;
    public function __construct()
    {
        $this->tabl = new Console_Table();
        $this->tabl->setHeaders(array('Die 1', 'Die 2', 'Dice 1 Win%', 'Dice 2 Win%', 'Draw %'));
    }
    public function generate($dice, Probability $probability)
    {
        for($i=0; $i<count($dice); $i++){
            for($j=$i+1; $j<count($dice); $j++){
                $probResult = $probability->CalculateProbility(explode(',', $dice[$i]), explode(',', $dice[$j]));
                $this->tabl->addRow(array($dice[$i], $dice[$j], $probResult[0], $probResult[1], $probResult[2]));
            }
        }
    }

    public function display()
    {
        echo $this->tabl->getTable();
    }
}