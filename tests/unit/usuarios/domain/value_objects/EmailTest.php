<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\Email;
use Tests\myTest;

class EmailTest extends myTest
{
    public function test_create_valid_email()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', $email->value());
    }

    public function test_empty_email_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email cannot be empty');
        new Email('');
    }

    public function test_invalid_email_format_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');
        new Email('invalid-email');
    }

    public function test_equals_returns_true_for_same_email()
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        $this->assertTrue($email1->equals($email2));
    }

    public function test_equals_returns_false_for_different_email()
    {
        $email1 = new Email('test1@example.com');
        $email2 = new Email('test2@example.com');
        $this->assertFalse($email1->equals($email2));
    }

    public function test_to_string_returns_email_value()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', (string)$email);
    }
}