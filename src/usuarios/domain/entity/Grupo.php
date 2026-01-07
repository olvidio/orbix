<?php

namespace src\usuarios\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\usuarios\domain\value_objects\Username;


class Grupo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_usuario;

    private UserName $usuario;

    private ?int $id_role = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_usuario(): int
    {
        return $this->id_usuario;
    }


    public function setId_usuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }


    public function getUsuarioVo(): Username
    {
        return $this->usuario;
    }


    public function setUsuarioVo(Username|string $usuario): void
    {
        $this->usuario = $usuario instanceof Username
            ? $usuario
            : new Username($usuario);
    }


    public function getUsuarioAsString(): string
    {
        return $this->usuario->value();
    }


    public function getId_role(): ?int
    {
        return $this->id_role;
    }


    public function setId_role(?int $id_role = null): void
    {
        $this->id_role = $id_role;
    }
}