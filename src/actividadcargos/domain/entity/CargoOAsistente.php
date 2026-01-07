<?php

namespace src\actividadcargos\domain\entity;

class CargoOAsistente
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    private int $id_activ;
    private int $id_nom;
    private bool $propio;
    private int $id_cargo;

    public function __construct(int $id_activ)
    {
        $this->id_activ = $id_activ;
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_activ(): int
    {
        return $this->id_activ;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    public function getId_nom(): int
    {
        return $this->id_nom;
    }

    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    public function isPropio(): bool
    {
        return $this->propio;
    }

    public function setPropio(bool $propio): void
    {
        $this->propio = $propio;
    }

    public function getId_cargo(): int
    {
        return $this->id_cargo;
    }

    public function setId_cargo(int $id_cargo): void
    {
        $this->id_cargo = $id_cargo;
    }

}