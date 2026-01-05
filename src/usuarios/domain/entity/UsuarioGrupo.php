<?php

namespace src\usuarios\domain\entity;

use src\shared\domain\traits\Hydratable;

class UsuarioGrupo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_usuario;

    private int $id_grupo;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_usuario(): int
    {
        return $this->id_usuario;
    }


    public function setId_usuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }


    public function getId_grupo(): int
    {
        return $this->id_grupo;
    }


    public function setId_grupo(int $id_grupo): void
    {
        $this->id_grupo = $id_grupo;
    }
}