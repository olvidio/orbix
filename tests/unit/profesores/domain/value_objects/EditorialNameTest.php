<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\EditorialName;
use Tests\myTest;

class EditorialNameTest extends myTest
{
    public function test_create_valid_editorialName()
    {
        $editorialName = new EditorialName('test value');
        $this->assertEquals('test value', $editorialName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new EditorialName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_editorialName_value()
    {
        $editorialName = new EditorialName('test value');
        $this->assertEquals('test value', (string)$editorialName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $editorialName = EditorialName::fromNullableString('test value');
        $this->assertInstanceOf(EditorialName::class, $editorialName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $editorialName = EditorialName::fromNullableString(null);
        $this->assertNull($editorialName);
    }

}
