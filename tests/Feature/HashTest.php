<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class HashTest extends TestCase
{
    public function testHash()
    {
        $password = 'rahasia';
        $hash1 = Hash::make($password);
        $hash2 = Hash::make($password);

        self::assertNotEquals($hash1, $hash2);
        Log::info(json_encode([
            'hash1' => $hash1, // $2y$04$oCnmQZoPgUwBj2HfdgUacO6LPuM2jiwvDFeb0lTuf9htjlDSccrM6
            'hash2' => $hash2, // $2y$04$BoCMPV428H0gstp3JAXnVeHpEk9Qj2nvpnhcAJAwBnwEhv5UiVSOC
        ], JSON_PRETTY_PRINT));

        $result1 = Hash::check('rahasia', $hash1);
        $result2 = Hash::check('rahasia', $hash2);

        self::assertTrue($result1);
        self::assertTrue($result2);
    }
}
