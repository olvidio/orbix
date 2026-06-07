<?php

namespace src\dbextern\domain\entity;

use src\shared\domain\traits\Hydratable;

class IdMatchPersona
{
    use Hydratable;

    /* ATRIBUTOS (nombres = columnas BD; Hydratable usa getId_* ↔ id_*) ------------- */

    private int $id_listas;
    private ?int $id_orbix;
    private string $id_tabla;


    function getId_listas(): int
    {
        return $this->id_listas;
    }

    function setId_listas(int $id_listas): void
    {
        $this->id_listas = $id_listas;
    }

    function getId_orbix(): ?int
    {
        return $this->id_orbix;
    }

    function setId_orbix(?int $id_orbix = null): void
    {
        $this->id_orbix = $id_orbix;
    }

    function getId_tabla(): string
    {
        return $this->id_tabla;
    }

    function setId_tabla(?string $id_tabla = null): void
    {
        $this->id_tabla = $id_tabla ?? '';
    }
}