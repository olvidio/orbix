<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\Secret2FA;
use Tests\myTest;

class Secret2FATest extends myTest
{
    public function test_create_valid_secret_2fa()
    {
        $secret = new Secret2FA('ABCDEFGHIJKLMNOP');
        $this->assertEquals('ABCDEFGHIJKLMNOP', $secret->value());
    }

    public function test_empty_secret_2fa_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('2FA secret cannot be empty');
        new Secret2FA('');
    }

    public function test_equals_returns_true_for_same_secret_2fa()
    {
        $secret1 = new Secret2FA('ABCDEFGHIJKLMNOP');
        $secret2 = new Secret2FA('ABCDEFGHIJKLMNOP');
        $this->assertTrue($secret1->equals($secret2));
    }

    public function test_equals_returns_false_for_different_secret_2fa()
    {
        $secret1 = new Secret2FA('ABCDEFGHIJKLMNOP');
        $secret2 = new Secret2FA('QRSTUVWXYZ123456');
        $this->assertFalse($secret1->equals($secret2));
    }

    public function test_to_string_returns_secret_2fa_value()
    {
        $secret = new Secret2FA('ABCDEFGHIJKLMNOP');
        $this->assertEquals('ABCDEFGHIJKLMNOP', (string)$secret);
    }
}