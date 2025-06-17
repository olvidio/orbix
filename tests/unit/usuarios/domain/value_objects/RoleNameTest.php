<?php

namespace Tests\unit\usuarios\domain\value_objects;

use src\usuarios\domain\value_objects\RoleName;
use Tests\myTest;

class RoleNameTest extends myTest
{
    public function test_create_valid_role_name()
    {
        $roleName = new RoleName('admin');
        $this->assertEquals('admin', $roleName->value());
    }

    public function test_empty_role_name_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Role name cannot be empty');
        new RoleName('');
    }

    public function test_equals_returns_true_for_same_role_name()
    {
        $roleName1 = new RoleName('admin');
        $roleName2 = new RoleName('admin');
        $this->assertTrue($roleName1->equals($roleName2));
    }

    public function test_equals_returns_false_for_different_role_name()
    {
        $roleName1 = new RoleName('admin');
        $roleName2 = new RoleName('user');
        $this->assertFalse($roleName1->equals($roleName2));
    }

    public function test_to_string_returns_role_name_value()
    {
        $roleName = new RoleName('admin');
        $this->assertEquals('admin', (string)$roleName);
    }
}