<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\Password;
use Tests\myTest;

class PasswordTest extends myTest
{
    public function test_create_valid_password()
    {
        $password = new Password('securepassword');
        $this->assertEquals('securepassword', $password->value());
    }

    public function test_empty_password_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Password cannot be empty');
        new Password('');
    }

    public function test_hash_returns_hashed_password()
    {
        $password = new Password('securepassword');
        $hashedPassword = $password->hash();
        
        // Verify that the hash is not the same as the original password
        $this->assertNotEquals('securepassword', $hashedPassword);
        
        // Verify that the hash is a valid password hash
        $this->assertTrue(password_verify('securepassword', $hashedPassword));
    }

    public function test_verify_returns_true_for_correct_password()
    {
        $plainPassword = 'securepassword';
        $password = new Password($plainPassword);
        $hashedPassword = $password->hash();
        
        $this->assertTrue(Password::verify($plainPassword, $hashedPassword));
    }

    public function test_verify_returns_false_for_incorrect_password()
    {
        $password = new Password('securepassword');
        $hashedPassword = $password->hash();
        
        $this->assertFalse(Password::verify('wrongpassword', $hashedPassword));
    }

    public function test_equals_returns_true_for_same_password()
    {
        $password1 = new Password('securepassword');
        $password2 = new Password('securepassword');
        $this->assertTrue($password1->equals($password2));
    }

    public function test_equals_returns_false_for_different_password()
    {
        $password1 = new Password('securepassword1');
        $password2 = new Password('securepassword2');
        $this->assertFalse($password1->equals($password2));
    }
}