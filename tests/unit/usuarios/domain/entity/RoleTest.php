<?php

namespace Tests\unit\usuarios\domain\entity;

use src\usuarios\domain\entity\Role;
use src\usuarios\domain\value_objects\RoleName;
use src\usuarios\domain\value_objects\PauType;
use Tests\myTest;

class RoleTest extends myTest
{
    private Role $role;

    public function setUp(): void
    {
        parent::setUp();
        $this->role = new Role();
        $this->role->setId_role(1);
        $this->role->setRole(new RoleName('admin'));
        $this->role->setSf(true);
        $this->role->setSv(false);
    }

    public function test_get_id_role()
    {
        $this->assertEquals(1, $this->role->getId_role());
    }

    public function test_set_and_get_id_role()
    {
        $this->role->setId_role(2);
        $this->assertEquals(2, $this->role->getId_role());
    }

    public function test_get_role()
    {
        $this->assertInstanceOf(RoleName::class, $this->role->getRole());
        $this->assertEquals('admin', $this->role->getRoleAsString());
    }

    public function test_set_and_get_role()
    {
        $roleName = new RoleName('user');
        $this->role->setRole($roleName);
        $this->assertInstanceOf(RoleName::class, $this->role->getRole());
        $this->assertEquals('user', $this->role->getRoleAsString());
    }

    public function test_get_sf()
    {
        $this->assertTrue($this->role->isSf());
    }

    public function test_set_and_get_sf()
    {
        $this->role->setSf(false);
        $this->assertFalse($this->role->isSf());
    }

    public function test_get_sv()
    {
        $this->assertFalse($this->role->isSv());
    }

    public function test_set_and_get_sv()
    {
        $this->role->setSv(true);
        $this->assertTrue($this->role->isSv());
    }

    public function test_get_pau()
    {
        // Default should normalize to PAU_NONE
        $this->assertInstanceOf(PauType::class, $this->role->getPau());
        $this->assertEquals(PauType::PAU_NONE, $this->role->getPauAsString());
    }

    public function test_set_and_get_pau()
    {
        $pauType = new PauType(PauType::PAU_CDC);
        $this->role->setPau($pauType);
        $this->assertInstanceOf(PauType::class, $this->role->getPau());
        $this->assertEquals(PauType::PAU_CDC, $this->role->getPauAsString());
    }

    public function test_get_dmz()
    {
        $this->assertNull($this->role->isDmz());
    }

    public function test_set_and_get_dmz()
    {
        $this->role->setDmz(true);
        $this->assertTrue($this->role->isDmz());
    }

    public function test_set_all_attributes()
    {
        $role = new Role();
        $attributes = [
            'id_role' => 1,
            'role' => new RoleName('admin'),
            'sf' => true,
            'sv' => false,
            'pau' => new PauType(PauType::PAU_CDC),
            'dmz' => true
        ];
        $role->setAllAttributes($attributes);

        $this->assertEquals(1, $role->getId_role());
        $this->assertEquals('admin', $role->getRoleAsString());
        $this->assertTrue($role->isSf());
        $this->assertFalse($role->isSv());
        $this->assertEquals(PauType::PAU_CDC, $role->getPauAsString());
        $this->assertTrue($role->isDmz());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $role = new Role();
        $attributes = [
            'id_role' => 1,
            'role' => 'admin',
            'sf' => true,
            'sv' => false,
            'pau' => PauType::PAU_CDC,
            'dmz' => true
        ];
        $role->setAllAttributes($attributes);

        $this->assertEquals(1, $role->getId_role());
        $this->assertEquals('admin', $role->getRoleAsString());
        $this->assertTrue($role->isSf());
        $this->assertFalse($role->isSv());
        $this->assertEquals(PauType::PAU_CDC, $role->getPauAsString());
        $this->assertTrue($role->isDmz());
    }

    public function test_set_all_attributes_with_null_pau_defaults_to_none()
    {
        $role = new Role();
        $attributes = [
            'id_role' => 3,
            'role' => 'editor',
            'sf' => false,
            'sv' => true,
            'pau' => null,
            'dmz' => false
        ];
        $role->setAllAttributes($attributes);

        $this->assertEquals(PauType::PAU_NONE, $role->getPauAsString());
    }

    public function test_set_all_attributes_with_empty_pau_defaults_to_none()
    {
        $role = new Role();
        $attributes = [
            'id_role' => 4,
            'role' => 'viewer',
            'sf' => false,
            'sv' => false,
            'pau' => '',
            'dmz' => null
        ];
        $role->setAllAttributes($attributes);

        $this->assertEquals(PauType::PAU_NONE, $role->getPauAsString());
    }

    // Note: The isRole and isRolePau methods depend on the RoleRepository,
    // which would require mocking for proper testing. This is beyond the scope
    // of this basic test suite.
}