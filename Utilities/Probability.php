<?php
class Probability
{
    public function CalculateProbility(array $dieA, array $dieB): array
    {
        $winsA=$winsB=$draws=$total=0;
        foreach ($dieA as $a){
            foreach ($dieB as $b){
                if($a>$b){$winsA++;}
                elseif($a<$b){$winsB++;}
                else{$draws++;}
                $total++;
            }
        }
        return $this->GetPercentage($total, $winsA, $winsB, $draws);
    }

    private function GetPercentage($total, ...$numbers): array
    {
        foreach ($numbers as $number)
        {
            $ar[] = round(($number / $total) * 100, 2);
        }
        return $ar;
    }
}