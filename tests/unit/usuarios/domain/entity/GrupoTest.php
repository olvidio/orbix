<?php

namespace Tests\unit\usuarios\domain\entity;

use src\usuarios\domain\entity\Grupo;
use src\usuarios\domain\value_objects\Username;
use Tests\myTest;

class GrupoTest extends myTest
{
    private string $session_org;

    /**
     * Sets up the test suite prior to every test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->session_org = $_SESSION['session_auth']['esquema'];

        $this->grupo = new Grupo();
        $this->grupo->setId_usuario(1);
        $this->grupo->setUsuarioVo(new Username('testgroup'));
    }

    private Grupo $grupo;

    public function test_get_id_usuario()
    {
        $this->assertEquals(1, $this->grupo->getId_usuario());
    }

    public function test_set_and_get_id_usuario()
    {
        $this->grupo->setId_usuario(2);
        $this->assertEquals(2, $this->grupo->getId_usuario());
    }

    public function test_get_usuario()
    {
        $this->assertEquals('testgroup', $this->grupo->getUsuarioVo());
    }

    public function test_set_and_get_usuario()
    {
        $this->grupo->setUsuarioVo(new Username('newgroup'));
        $this->assertEquals('newgroup', $this->grupo->getUsuarioVo());
    }

    public function test_get_id_role()
    {
        $this->assertNull($this->grupo->getId_role());
    }

    public function test_set_and_get_id_role()
    {
        $this->grupo->setId_role(3);
        $this->assertEquals(3, $this->grupo->getId_role());
    }

    public function test_set_all_attributes()
    {
        $grupo = new Grupo();
        $attributes = [
            'id_usuario' => 1,
            'usuario' => 'testgroup',
            'id_role' => 3
        ];
        $grupo->setAllAttributes($attributes);

        $this->assertEquals(1, $grupo->getId_usuario());
        $this->assertEquals('testgroup', $grupo->getUsuarioVo());
        $this->assertEquals(3, $grupo->getId_role());
    }

    /**
     * Runs at the end of every test.
     */
    protected function tearDown(): void
    {
        $_SESSION['session_auth']['esquema'] = $this->session_org;
        parent::tearDown();
    }
}