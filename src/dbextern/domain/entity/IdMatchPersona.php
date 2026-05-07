<?php

namespace src\dbextern\domain\entity;

use src\shared\domain\traits\Hydratable;

class IdMatchPersona
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $iid_listas;
    private ?int $iid_orbix;
    private string $sid_tabla;


    function getId_listas(): int
    {
        return $this->iid_listas;
    }

    function setId_listas(int $iid_listas)
    {
        $this->iid_listas = $iid_listas;
    }

    function getId_orbix(): ?int
    {
        return $this->iid_orbix;
    }

    function setId_orbix(?int $iid_orbix = null)
    {
        $this->iid_orbix = $iid_orbix;
    }

    function getId_tabla(): string
    {
        return $this->sid_tabla;
    }

    function setId_tabla(?string $sid_tabla = null)
    {
        $this->sid_tabla = $sid_tabla;
    }
}