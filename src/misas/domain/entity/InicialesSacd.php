<?php

namespace src\misas\domain\entity;

use src\shared\domain\traits\Hydratable;

class InicialesSacd
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ?int $id_nom = null;
    private ?string $iniciales = null;
    private ?string $color = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getId_nom(): ?int
    {
        return $this->id_nom;
    }


    public function setId_nom(?int $id_nom = null): void
    {
        $this->id_nom = $id_nom;
    }


    public function getIniciales(): ?string
    {
        return $this->iniciales;
    }


    public function setIniciales(?string $iniciales = null): void
    {
        $this->iniciales = $iniciales;
    }


    public function getColor(): ?string
    {
        return $this->color;
    }


    public function setColor(?string $color = null): void
    {
        $this->color = $color;
    }
}