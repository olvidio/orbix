<?php

namespace Tests\unit\misas\application;

use src\misas\application\InicialesColorHex;
use Tests\myTest;

class InicialesColorHexTest extends myTest
{
    public function test_empty_returns_empty(): void
    {
        $this->assertSame('', InicialesColorHex::normalizeForStorage(''));
        $this->assertSame('', InicialesColorHex::normalizeForStorage('   '));
    }

    public function test_six_hex_without_hash_lowercase(): void
    {
        $this->assertSame('ab12ef', InicialesColorHex::normalizeForStorage('AB12EF'));
        $this->assertSame('ab12ef', InicialesColorHex::normalizeForStorage('ab12ef'));
    }

    public function test_six_hex_with_hash(): void
    {
        $this->assertSame('ff0000', InicialesColorHex::normalizeForStorage('#FF0000'));
    }

    public function test_three_hex_expands(): void
    {
        $this->assertSame('ff0000', InicialesColorHex::normalizeForStorage('#f00'));
        $this->assertSame('aabbcc', InicialesColorHex::normalizeForStorage('abc'));
    }

    public function test_invalid_returns_empty(): void
    {
        $this->assertSame('', InicialesColorHex::normalizeForStorage('red'));
        $this->assertSame('', InicialesColorHex::normalizeForStorage('#ff00'));
        $this->assertSame('', InicialesColorHex::normalizeForStorage('ff00ff00'));
    }
}
