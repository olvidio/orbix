<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\Username;
use Tests\myTest;

class UsernameTest extends myTest
{
    public function test_create_valid_username()
    {
        $username = new Username('testuser');
        $this->assertEquals('testuser', $username->value());
    }

    public function test_empty_username_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Username cannot be empty');
        new Username('');
    }

    public function test_equals_returns_true_for_same_username()
    {
        $username1 = new Username('testuser');
        $username2 = new Username('testuser');
        $this->assertTrue($username1->equals($username2));
    }

    public function test_equals_returns_false_for_different_username()
    {
        $username1 = new Username('testuser1');
        $username2 = new Username('testuser2');
        $this->assertFalse($username1->equals($username2));
    }

    public function test_to_string_returns_username_value()
    {
        $username = new Username('testuser');
        $this->assertEquals('testuser', (string)$username);
    }
}