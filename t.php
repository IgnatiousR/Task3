<?php

//$allIntegers = array_reduce($numbers, function($carry, $num) {
//return $carry && ctype_digit($num);}, true);
//if(!$allIntegers){echo "Error: Dice cannot contain floating numbers.".PHP_EOL."Example: php dice.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3".PHP_EOL;exit();}

        // for($i = 0; $i < count($dice); $i++){
        //     $numbers = explode(',', $dice[$i]);
        //     if(count($numbers)!=6){echo 'Error: Each dice needs to have exactly 6 faces.'.PHP_EOL."Example: php dice.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3".PHP_EOL;exit();}
        //     foreach ($numbers as $number) {
        //         if($this->isStringFloat($number)){echo "Error: Dice cannot contain floating numbered faces.".PHP_EOL;exit();}
        //     }
        // }

    // $uniformInteger = $hmacGenerator->generateUniformlyDistributedInteger(0,5);
    // $key = $hmacGenerator->generateKey();
    // $hmac = $hmacGenerator->generateHMAC($uniformInteger, $key);

$dice = ["2,2,4,4,9,9", "6,8,1,1,8,6", "7,5,3,7,5,3,6,5", "7,5,3,7,5,3"];
array_map(function($n) {
    $numbers = explode(',', $n);
    if(count($numbers)!=6){echo 'Error: Each dice needs to have exactly 6 faces.'.PHP_EOL."Example: php dice.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3".PHP_EOL;exit();}
    $numbers = array_map(function($n) {
        if(filter_var($n, FILTER_VALIDATE_FLOAT) !== false && filter_var($n, FILTER_VALIDATE_INT) === false) {echo "Error: Dice cannot contain floating numbered faces.".PHP_EOL;exit();}
    }, $numbers);
}, $dice);

print_r($dice);

echo hash("sha3-256", "abc"), PHP_EOL;