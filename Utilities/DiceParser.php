<?php
class DiceParser
{
    public function parse(array $dice): array
    {
        array_shift($dice);
        if(!(count($dice)>=3)){echo "Error: Incorrect number of arguments. You must provide at least 3 dice configuration.",PHP_EOL,"Example: php dice.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3",PHP_EOL;exit();}
        foreach($dice as $die){
            $faces = explode(',', $die);
            if(count($faces)!=6){echo 'Error: Each dice needs to have exactly 6 faces.',PHP_EOL,"Example: php dice.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3",PHP_EOL;exit();}
            foreach ($faces as $face) {
                if($this->isStringFloat($face)){echo "Error: Dice cannot contain floating numbered faces.",PHP_EOL;exit();}
            }
        }
        return $dice;
    }

    private function isStringFloat(string $num): bool
    {
        return filter_var($num, FILTER_VALIDATE_FLOAT) !== false && filter_var($num, FILTER_VALIDATE_INT) === false;
    }
}