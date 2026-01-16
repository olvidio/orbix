<?php

namespace Tests\unit\profesores\domain\value_objects;

use src\profesores\domain\value_objects\OrganizaName;
use Tests\myTest;

class OrganizaNameTest extends myTest
{
    public function test_create_valid_organizaName()
    {
        $organizaName = new OrganizaName('test value');
        $this->assertEquals('test value', $organizaName->value());
    }

    public function test_invalid_length_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        new OrganizaName(str_repeat('a', 1000)); // Assuming max length validation
    }

    public function test_to_string_returns_organizaName_value()
    {
        $organizaName = new OrganizaName('test value');
        $this->assertEquals('test value', (string)$organizaName);
    }

    public function test_fromNullableString_returns_instance_for_valid_value()
    {
        $organizaName = OrganizaName::fromNullableString('test value');
        $this->assertInstanceOf(OrganizaName::class, $organizaName);
    }

    public function test_fromNullableString_returns_null_for_null_value()
    {
        $organizaName = OrganizaName::fromNullableString(null);
        $this->assertNull($organizaName);
    }

}
