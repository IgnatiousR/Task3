<?php
require_once 'DiceParser.php';
require_once 'TableGenerator.php';
require_once 'HmacGenerator.php';
require_once 'FairNumberGenerator.php';


// function probabilityCal($dieA, $dieB){
//     $winsA=$winsB=$draws=$total=0;
//     foreach ($dieA as $a) {
//         foreach ($dieB as $b){
//             if($a>$b){$winsA++;}
//             elseif($a<$b){$winsB++;}
//             else{$draws++;}
//             $total++;
//         }
//     }
//     $winsA = round(($winsA / $total) * 100, 2);
//     $winsB = round(($winsB / $total) * 100, 2);
//     $draws = round(($draws / $total) * 100, 2);
//     return [$winsA, $winsB, $draws];
// }

// array_shift($argv);


$playerInput=$playerDice=$comDice=null;
$run = true;
$diceParser = new DiceParser();
$tableGenerator = new TableGenerator();
$hmacGenerator = new HmacGenerator();
$fairNumberGenerator = new FairNumberGenerator();
$argv = $diceParser->parse($argv);

// $tbl = new Console_Table();
// $tbl->setHeaders(array('Die 1', 'Die 2', 'Dice 1 Win%', 'Dice 2 Win%', 'Draw %'));
// for( $i = 0; $i < count($argv); $i++ ) {
//     for($j=$i+1; $j < count($argv); $j++ ){
//         $probResult = probabilityCal(explode(',', $argv[$i]),explode(',', $argv[$j]));
//         $tbl->addRow(array($argv[$i], $argv[$j], $probResult[0], $probResult[1], $probResult[2]));
//     }
// }

// if(!(count($argv)>=3)){echo "Error: Incorrect number of arguments. You must provide at least 3 dice configuration.".PHP_EOL."Example: php dice.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3".PHP_EOL;exit();}

// for($i = 0; $i < count($argv); $i++){
//     $numbers = explode(',', $argv[$i]);
//     if(count($numbers)!=6){echo 'Error: Each dice needs to have exactly 6 faces.'.PHP_EOL."Example: php dice.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3".PHP_EOL;exit();}

//     $allIntegers = array_reduce($numbers, function($carry, $num) {
//     return $carry && ctype_digit($num);}, true);

//     if(!$allIntegers){echo "Error: Dice cannot contain floating numbers.".PHP_EOL."Example: php dice.php 2,2,4,4,9,9 6,8,1,1,8,6 7,5,3,7,5,3".PHP_EOL;exit();}
// }

echo "Let's determine who makes the first move.".PHP_EOL;
echo "I selected a random value in the range 0..1".PHP_EOL;
$randomVal = random_int(0, 1);
echo $randomVal.PHP_EOL;
$key = bin2hex(random_bytes(32));
$hmacVal = hash_hmac('sha3-256', $randomVal, $key);
echo "(HMAC=$hmacVal)".PHP_EOL;

while($run){
    echo "Try to guess my selection".PHP_EOL."0 - 0".PHP_EOL."1 - 1".PHP_EOL."X - exit".PHP_EOL."? - help".PHP_EOL;
    $playerInput = readline('Your selection: ');
    if($playerInput === "?"){echo "Help: This is a non-transitive dice game.".PHP_EOL."The table below shows the probabilities for each pair of dice.".PHP_EOL.$tbl->getTable();}
    elseif($playerInput === "X" | $playerInput === "x"){echo "Exiting the progam.";exit();}
    elseif(is_numeric($playerInput) && $playerInput >= 0 && $playerInput <= 1){$run = false;$playerInput=(int)$playerInput;}
    else{echo "Incorrect input, try again.".PHP_EOL;}
}
echo "My selection: $randomVal (KEY=$key).".PHP_EOL;
$run = true;

if($playerInput === $randomVal){
    while($run){
        echo "Choose your dice:".PHP_EOL;
        for($i = 0; $i < count($argv); $i++){echo "$i - $argv[$i]".PHP_EOL;}
        echo "X - exit".PHP_EOL."? - help".PHP_EOL;
        $playerInput = readline('Your selection: ');
        if($playerInput === "?"){echo "Help: This is a non-transitive dice game.".PHP_EOL."The table below shows the probabilities for each pair of dice.".PHP_EOL.$tbl->getTable();}
        elseif($playerInput === "X" | $playerInput === "x"){echo "Exiting the progam.";exit();}
        elseif(is_numeric($playerInput) && $playerInput >= 0 && $playerInput < count($argv))
        {
            $playerDice = $argv[$playerInput];
            unset($argv[$playerInput]);
            $argv = array_values($argv);
            $run = false;
            echo "You chose the [$playerDice] dice".PHP_EOL;
            $randomVal = random_int(0, count($argv)-1);
            $comDice = $argv[$randomVal];
            echo "I choose the [$comDice] dice.".PHP_EOL;

        }
        else{echo "Incorrect input, try again.".PHP_EOL;}
    }
}
else{
    $randomVal = random_int(0, count($argv)-1);
    $comDice = $argv[$randomVal];
    unset($argv[$randomVal]);
    $argv = array_values($argv);
    echo "I make the first move and choose the [$comDice] dice.".PHP_EOL;
    $key = bin2hex(random_bytes(32));
    $hmacVal = hash_hmac('sha3-256', $randomVal, $key);
    echo "(HMAC=$hmacVal)".PHP_EOL;
    echo "My selection: $randomVal (KEY=$key).".PHP_EOL;
    $run = true;

    while($run){
        echo "Choose your dice:".PHP_EOL;
        for($i = 0; $i < count($argv); $i++){echo "$i - $argv[$i]".PHP_EOL;}
        echo "X - exit".PHP_EOL."? - help".PHP_EOL;
        $playerInput = readline('Your selection: ');
        if($playerInput === "?"){echo "Help: This is a non-transitive dice game.".PHP_EOL."The table below shows the probabilities for each pair of dice.".PHP_EOL.$tbl->getTable();}
        elseif($playerInput === "X" | $playerInput === "x"){echo "Exiting the progam.";exit();}
        elseif(is_numeric($playerInput) && $playerInput >= 0 && $playerInput < count($argv))
        {
            $playerDice = $argv[$playerInput];
            $run = false;
            echo "You chose the [$playerDice] dice".PHP_EOL;
        }
        else{echo "Incorrect input, try again.".PHP_EOL;}
    }
}

$randomVal = random_int(0, 5);
$key = bin2hex(random_bytes(32));
$hmacVal = hash_hmac('sha3-256', $randomVal, $key);
echo "It's time for my roll.\nI selected a random value in the range 0..5 (HMAC=$hmacVal).\nAdd your number to modulus 6.".PHP_EOL;

$run = true;
while($run){
    for($i = 0; $i < 6; $i++){echo "$i - $i".PHP_EOL;}
    echo "X - exit".PHP_EOL."? - help".PHP_EOL;
    $playerInput = readline('Your selection: ');
    if($playerInput === "?"){echo "Help: This is a non-transitive dice game.".PHP_EOL."The table below shows the probabilities for each pair of dice.".PHP_EOL.$tbl->getTable();}
    elseif($playerInput === "X" | $playerInput === "x"){echo "Exiting the progam.";exit();}
    elseif(is_numeric($playerInput) && $playerInput >= 0 && $playerInput < 6)
    {
        $run = false;
        echo "My number is $randomVal (KEY=$key).".PHP_EOL;
        $modVal = ($randomVal + $playerInput) % 6;
        echo "The fair number generation result is $randomVal + $playerInput = $modVal (mod 6).".PHP_EOL;
        $diceNum = explode(',', $comDice);
        echo "My roll result is $diceNum[$modVal].".PHP_EOL;$comRoll = $diceNum[$modVal];
    }
    else{echo "Incorrect input, try again.".PHP_EOL;}
}

$randomVal = random_int(0, 5);
$key = bin2hex(random_bytes(32));
$hmacVal = hash_hmac('sha3-256', $randomVal, $key);
echo "It's time for your roll.\nI selected a random value in the range 0..5 (HMAC=$hmacVal).\nAdd your number to modulus 6.".PHP_EOL;
$run = true;
while($run){
    for($i = 0; $i < 6; $i++){echo "$i - $i".PHP_EOL;}
    echo "X - exit".PHP_EOL."? - help".PHP_EOL;

    $playerInput = readline('Your selection: ');
    if($playerInput === "?"){echo "Help: This is a non-transitive dice game.".PHP_EOL."The table below shows the probabilities for each pair of dice.".PHP_EOL.$tbl->getTable();}
    elseif($playerInput === "X" | $playerInput === "x"){echo "Exiting the progam.";exit();}
    elseif(is_numeric($playerInput) && $playerInput >= 0 && $playerInput < 6)
    {
        $run = false;
        echo "My number is $randomVal (KEY=$key).".PHP_EOL;
        $modVal = ($randomVal + $playerInput) % 6;
        echo "The fair number generation result is $randomVal + $playerInput = $modVal (mod 6).".PHP_EOL;
        $diceNum = explode(',', $playerDice);
        echo "Your roll result is $diceNum[$modVal].".PHP_EOL;$playerRoll=$diceNum[$modVal];
    }
    else{echo "Incorrect input, try again.".PHP_EOL;}
}

if($playerRoll>$comRoll){echo "You win ($playerRoll > $comRoll)!".PHP_EOL;}
elseif($playerRoll<$comRoll){echo "I win ($comRoll > $playerRoll)!".PHP_EOL;}
else{echo "Draw ($comRoll == $playerRoll)".PHP_EOL;}
?>
