<?php
require_once 'Utilities/DiceParser.php';
require_once 'Utilities/TableGenerator.php';
require_once 'Utilities/HmacGenerator.php';
require_once 'Utilities/FairNumberGenerator.php';
require_once 'Utilities/Probability.php';

function displayOptions(int $start, int $end, TableGenerator $tableGenerator, string $message = null, array $dice = null) {
    while (true) {
        if($message) echo $message,PHP_EOL;
        if($dice){foreach (range($start, $end) as $i) echo "$i - $dice[$i]",PHP_EOL;}
        else{foreach (range($start, $end) as $i) echo "$i - $i",PHP_EOL;}
        echo "X - exit",PHP_EOL,"? - help",PHP_EOL;
        $input = readline('Your selection: ');

        if ($input==="?") {echo "Help: This is a non-transitive dice game.",PHP_EOL,"The table below shows the probabilities for each pair of dice.",PHP_EOL,$tableGenerator->display();}
        elseif(strtoupper($input)==="X") {echo "Exiting the program.";exit();}
        elseif(is_numeric($input) && $input>=$start && $input<=$end) {return (int)$input;}
        else{echo "Incorrect input, try again.",PHP_EOL;}
    }
}

function secureRandomGenerate(HmacGenerator $hmacGenerator, int $min, int $max): array{
    $uniformInteger = $hmacGenerator->generateUniformlyDistributedInteger($min,$max);
    $key = $hmacGenerator->generateKey();
    $hmac = $hmacGenerator->generateHMAC($uniformInteger, $key);
    return [$uniformInteger, $key, $hmac];
}

function rollDice(string $dice, HmacGenerator $hmacGenerator, FairNumberGenerator $fairNumberGenerator, TableGenerator $tableGenerator){
    [$uniformInteger, $key, $hmac] = secureRandomGenerate($hmacGenerator,0,5);
    echo "I selected a random value in the range 0..5",PHP_EOL,"(HMAC=$hmac)",PHP_EOL;
    $playerInput = displayOptions(0, 5, $tableGenerator, "Add your number to modulus 6.");
    echo "My number is $uniformInteger = (KEY=$key).",PHP_EOL;
    $modVal = $fairNumberGenerator->generate($playerInput, $uniformInteger);
    echo "The fair number generation result is $uniformInteger + $playerInput = $modVal (mod 6).",PHP_EOL;
    $diceValues = explode(',', $dice);
    return $diceValues[$modVal];
}

function selectDice(&$argv, HmacGenerator $hmacGenerator): array {
    [$uniformInteger, $key, $hmac] = secureRandomGenerate($hmacGenerator,0,count($argv)-1);
    $dice = $argv[$uniformInteger];
    unset($argv[$uniformInteger]);
    $argv = array_values($argv);
    return [$dice, $uniformInteger, $key, $hmac];
}

function displayGameResult(int $playerRoll,int $comRoll){
    if ($playerRoll > $comRoll) {echo "You win ($playerRoll > $comRoll)!",PHP_EOL;}
    elseif ($playerRoll < $comRoll) {echo "I win ($comRoll > $playerRoll)!",PHP_EOL;}
    else {echo "Draw ($comRoll == $playerRoll)",PHP_EOL;}
}


$diceParser = new DiceParser();
$tableGenerator = new TableGenerator();
$hmacGenerator = new HmacGenerator();
$fairNumberGenerator = new FairNumberGenerator();
$probability = new Probability();

$argv = $diceParser->parse($argv);
$tableGenerator->generate($argv, $probability);

echo "Let's determine who makes the first move.",PHP_EOL;
echo "I selected a random value from 0 or 1",PHP_EOL;
[$uniformInteger, $key, $hmac] = secureRandomGenerate($hmacGenerator,0,1);
echo "(HMAC=$hmac)",PHP_EOL;
$guess = displayOptions(0, 1, $tableGenerator, "Try to guess my selection.");
echo "My selection: $uniformInteger (KEY=$key).",PHP_EOL;

if ($guess === $uniformInteger) {
    $playerIndex = displayOptions(0, count($argv) - 1, $tableGenerator, "Choose your dice:", $argv);
    $playerDice = $argv[$playerIndex];
    unset($argv[$playerIndex]);
    $argv = array_values($argv);
    echo "You chose the [$playerDice] dice.",PHP_EOL;
    [$comDice, $comIndex, $comKey, $comHmac] = selectDice($argv, $hmacGenerator);
    echo "I choose the [$comDice] dice.",PHP_EOL,"(HMAC=$comHmac)",PHP_EOL,"My selection: $comIndex (KEY=$comKey).",PHP_EOL;;
} else {
    [$comDice, $comIndex, $comKey, $comHmac] = selectDice($argv, $hmacGenerator);
    echo "I make the first move and choose the [$comDice] dice.",PHP_EOL,"(HMAC=$comHmac)",PHP_EOL,"My selection: $comIndex (KEY=$comKey).",PHP_EOL;
    $playerIndex = displayOptions(0, count($argv) - 1, $tableGenerator, "Choose your dice:", $argv);
    $playerDice = $argv[$playerIndex];
    echo "You chose the [$playerDice] dice.".PHP_EOL;
}

$comRoll = rollDice($comDice, $hmacGenerator, $fairNumberGenerator, $tableGenerator);
echo "My roll result is $comRoll.",PHP_EOL;
$playerRoll = rollDice($playerDice, $hmacGenerator, $fairNumberGenerator, $tableGenerator);
echo "Your roll result is $playerRoll.",PHP_EOL;
displayGameResult($playerRoll, $comRoll);