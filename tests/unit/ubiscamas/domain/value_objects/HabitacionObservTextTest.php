<?php
declare(strict_types=1);

namespace Tests\unit\ubiscamas\domain\value_objects;

use InvalidArgumentException;
use src\ubiscamas\domain\value_objects\HabitacionObservText;
use Tests\myTest;

class HabitacionObservTextTest extends myTest
{
    public function test_it_should_create_from_string()
    {
        $value = 'Alguna observación';
        $vo = new HabitacionObservText($value);
        $this->assertEquals($value, $vo->value());
        $this->assertEquals($value, (string)$vo);
    }

    public function test_it_should_fail_if_too_long()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Las observaciones no pueden superar los 250 caracteres');
        
        $longString = str_repeat('a', 251);
        new HabitacionObservText($longString);
    }

    public function test_it_should_allow_250_characters()
    {
        $maxString = str_repeat('a', 250);
        $vo = new HabitacionObservText($maxString);
        $this->assertEquals($maxString, $vo->value());
    }

    public function test_it_should_create_from_nullable_string()
    {
        $this->assertNull(HabitacionObservText::fromNullableString(null));
        $this->assertNull(HabitacionObservText::fromNullableString(''));
        
        $vo = HabitacionObservText::fromNullableString('Test');
        $this->assertInstanceOf(HabitacionObservText::class, $vo);
        $this->assertEquals('Test', $vo->value());
    }
}
