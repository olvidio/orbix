<?php

namespace Tests\unit\usuarios\domain\entity;

use src\usuarios\domain\entity\UsuarioGrupo;
use Tests\myTest;

class UsuarioGrupoTest extends myTest
{
    private UsuarioGrupo $usuarioGrupo;

    public function setUp(): void
    {
        parent::setUp();
        $this->usuarioGrupo = new UsuarioGrupo();
        $this->usuarioGrupo->setId_usuario(1);
        $this->usuarioGrupo->setId_grupo(2);
    }

    public function test_get_id_usuario()
    {
        $this->assertEquals(1, $this->usuarioGrupo->getId_usuario());
    }

    public function test_set_and_get_id_usuario()
    {
        $this->usuarioGrupo->setId_usuario(3);
        $this->assertEquals(3, $this->usuarioGrupo->getId_usuario());
    }

    public function test_get_id_grupo()
    {
        $this->assertEquals(2, $this->usuarioGrupo->getId_grupo());
    }

    public function test_set_and_get_id_grupo()
    {
        $this->usuarioGrupo->setId_grupo(4);
        $this->assertEquals(4, $this->usuarioGrupo->getId_grupo());
    }

    public function test_set_all_attributes()
    {
        $usuarioGrupo = new UsuarioGrupo();
        $attributes = [
            'id_usuario' => 1,
            'id_grupo' => 2
        ];
        $usuarioGrupo->setAllAttributes($attributes);

        $this->assertEquals(1, $usuarioGrupo->getId_usuario());
        $this->assertEquals(2, $usuarioGrupo->getId_grupo());
    }
}