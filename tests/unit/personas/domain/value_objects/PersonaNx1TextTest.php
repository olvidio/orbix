<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\PersonaNx1Text;
use Tests\myTest;

class PersonaNx1TextTest extends myTest
{
    // PersonaNx1Text must be at most 7 characters
    public function test_create_valid_personaNx1Text()
    {
        $personaNx1Text = new PersonaNx1Text('testnx');
        $this->assertEquals('testnx', $personaNx1Text->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PersonaNx1Text(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_personaNx1Text_value()
    {
        $personaNx1Text = new PersonaNx1Text('testnx');
        $this->assertEquals('testnx', (string)$personaNx1Text);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $personaNx1Text = PersonaNx1Text::fromNullableString('testnx');
        $this->assertInstanceOf(PersonaNx1Text::class, $personaNx1Text);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $personaNx1Text = PersonaNx1Text::fromNullableString(null);
        $this->assertNull($personaNx1Text);
    }

}
