<?php
require_once 'DiceParser.php';
require_once 'TableGenerator.php';
require_once 'HmacGenerator.php';
require_once 'FairNumberGenerator.php';
require_once 'Probability.php';

function displayOptions(int $start,int $end,TableGenerator $tableGenerator, string $message=null, array $dice = null){
    $run = true;
    while($run){
        if($message){echo $message.PHP_EOL;}

        if($dice){for($i = $start; $i <= $end; $i++){echo "$i - $dice[$i]".PHP_EOL;}}
        else{for($i = $start; $i <= $end; $i++){echo "$i - $i".PHP_EOL;}}
        
        echo "X - exit".PHP_EOL."? - help".PHP_EOL;
        $playerInput = readline('Your selection: ');
        if($playerInput === "?"){echo "Help: This is a non-transitive dice game.".PHP_EOL."The table below shows the probabilities for each pair of dice.".PHP_EOL.$tableGenerator->display();}
        elseif($playerInput === "X" | $playerInput === "x"){echo "Exiting the progam.";exit();}
        elseif(is_numeric($playerInput) && $playerInput >= $start && $playerInput <= $end){$run = false;return (int)$playerInput;}
        else{echo "Incorrect input, try again.".PHP_EOL;}
    }
}


$playerInput=$playerDice=$comDice=null;
$diceParser = new DiceParser();
$tableGenerator = new TableGenerator();
$hmacGenerator = new HmacGenerator();
$fairNumberGenerator = new FairNumberGenerator();
$probability = new Probability();

$argv = $diceParser->parse($argv);
$tableGenerator->generate($argv, $probability);

echo "Let's determine who makes the first move.".PHP_EOL;
echo "I selected a random value in the from 0 or 1".PHP_EOL;

[$randomVal, $key, $hmacVal] = $hmacGenerator->generate(0,1);
echo "(HMAC=$hmacVal)".PHP_EOL;
$playerInput = displayOptions(0, 1,$tableGenerator, "Try to guess my selection.");

echo "My selection: $randomVal (KEY=$key).".PHP_EOL;

if($playerInput === $randomVal)
{
    $playerInput = displayOptions(0, count($argv)-1,$tableGenerator,"Choose your dice:",$argv);
    $playerDice = $argv[$playerInput];
    unset($argv[$playerInput]);
    $argv = array_values($argv);
    echo "You chose the [$playerDice] dice".PHP_EOL;
    [$randomVal, $key, $hmacVal] = $hmacGenerator->generate(0,count($argv)-1);
    $comDice = $argv[$randomVal];
    echo "I choose the [$comDice] dice.".PHP_EOL;
}
else
{
    [$randomVal, $key, $hmacVal] = $hmacGenerator->generate(0,count($argv)-1);
    $comDice = $argv[$randomVal];
    unset($argv[$randomVal]);
    $argv = array_values($argv);
    echo "I make the first move and choose the [$comDice] dice.".PHP_EOL."(HMAC=$hmacVal)".PHP_EOL;
    echo "My selection: $randomVal (KEY=$key).".PHP_EOL;
    $playerInput = displayOptions(0, count($argv)-1,$tableGenerator,"Choose your dice:",$argv);
    $playerDice = $argv[$playerInput];
    echo "You chose the [$playerDice] dice".PHP_EOL;
}

[$randomVal, $key, $hmacVal] = $hmacGenerator->generate(0,5);
echo "It's time for my roll.\nI selected a random value in the range 0..5 (HMAC=$hmacVal).".PHP_EOL;
$playerInput = displayOptions(0, 5,$tableGenerator,"Add your number to modulus 6.");

echo "My number is $randomVal (KEY=$key).".PHP_EOL;

$modVal = $fairNumberGenerator->generate($playerInput, $randomVal);
echo "The fair number generation result is $randomVal + $playerInput = $modVal (mod 6).".PHP_EOL;
$diceNum = explode(',', $comDice);
echo "My roll result is $diceNum[$modVal].".PHP_EOL;$comRoll = $diceNum[$modVal];

[$randomVal, $key, $hmacVal] = $hmacGenerator->generate(0,5);
echo "It's time for your roll.\nI selected a random value in the range 0..5 (HMAC=$hmacVal).\nAdd your number to modulus 6.".PHP_EOL;
$playerInput = displayOptions(0, 5,$tableGenerator,"Add your number to modulus 6.");
echo "My number is $randomVal (KEY=$key).".PHP_EOL;
$modVal = $fairNumberGenerator->generate($playerInput, $randomVal);
echo "The fair number generation result is $randomVal + $playerInput = $modVal (mod 6).".PHP_EOL;
$diceNum = explode(',', $playerDice);
echo "Your roll result is $diceNum[$modVal].".PHP_EOL;$playerRoll=$diceNum[$modVal];

if($playerRoll>$comRoll){echo "You win ($playerRoll > $comRoll)!".PHP_EOL;}
elseif($playerRoll<$comRoll){echo "I win ($comRoll > $playerRoll)!".PHP_EOL;}
else{echo "Draw ($comRoll == $playerRoll)".PHP_EOL;}
