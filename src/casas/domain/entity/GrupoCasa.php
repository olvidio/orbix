<?php

namespace src\casas\domain\entity;

use src\shared\domain\traits\Hydratable;

class GrupoCasa
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_ubi_padre;

    private int $id_ubi_hijo;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_ubi_padre(): int
    {
        return $this->id_ubi_padre;
    }


    public function setId_ubi_padre(int $id_ubi_padre): void
    {
        $this->id_ubi_padre = $id_ubi_padre;
    }


    public function getId_ubi_hijo(): int
    {
        return $this->id_ubi_hijo;
    }


    public function setId_ubi_hijo(int $id_ubi_hijo): void
    {
        $this->id_ubi_hijo = $id_ubi_hijo;
    }
}