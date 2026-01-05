<?php

namespace src\usuarios\domain\entity;

use src\shared\domain\traits\Hydratable;

class PermMenu
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_usuario;

    private int|null $menu_perm = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_usuario(): int
    {
        return $this->id_usuario;
    }


    public function setId_usuario(int $id_usuario): void
    {
        $this->id_usuario = $id_usuario;
    }


    public function getMenu_perm(): ?int
    {
        return $this->menu_perm;
    }


    public function setMenu_perm(?int $menu_perm = null): void
    {
        $this->menu_perm = $menu_perm;
    }
}