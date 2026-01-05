<?php

namespace src\menus\domain\entity;

use src\shared\domain\traits\Hydratable;

class GrupMenuRole
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int|null $id_grupmenu = null;

    private int|null $id_role = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_grupmenu(): ?int
    {
        return $this->id_grupmenu;
    }


    public function setId_grupmenu(?int $id_grupmenu = null): void
    {
        $this->id_grupmenu = $id_grupmenu;
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