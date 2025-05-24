<?php
class HmacGenerator
{
    public function generateHMAC(string $data, string $key): string
    {
        return hash_hmac('sha3-256', $data, $key);
    }

    public function generateUniformlyDistributedInteger(int $min=0, int $max=1): int
    {
        return random_int($min, $max);
    }

    public function generateKey(int $length=32): string
    {
        return bin2hex(random_bytes($length));
    }
}