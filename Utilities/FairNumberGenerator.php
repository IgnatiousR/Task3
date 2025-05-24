<?php
class FairNumberGenerator
{
    public function generate(int $num1, int $num2): int
    {
        return ($num1 + $num2) % 6;
    }
}