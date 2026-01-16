<?php

namespace Tests\unit\personas\domain\value_objects;

use src\personas\domain\value_objects\PersonaNx2Text;
use Tests\myTest;

class PersonaNx2TextTest extends myTest
{
    // PersonaNx2Text must be at most 7 characters
    public function test_create_valid_personaNx2Text()
    {
        $personaNx2Text = new PersonaNx2Text('testnx');
        $this->assertEquals('testnx', $personaNx2Text->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PersonaNx2Text(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_personaNx2Text_value()
    {
        $personaNx2Text = new PersonaNx2Text('testnx');
        $this->assertEquals('testnx', (string)$personaNx2Text);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $personaNx2Text = PersonaNx2Text::fromNullableString('testnx');
        $this->assertInstanceOf(PersonaNx2Text::class, $personaNx2Text);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $personaNx2Text = PersonaNx2Text::fromNullableString(null);
        $this->assertNull($personaNx2Text);
    }

}
