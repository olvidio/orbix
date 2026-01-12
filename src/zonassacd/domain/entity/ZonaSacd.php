<?php

namespace src\zonassacd\domain\entity;

use src\shared\domain\traits\Hydratable;

class ZonaSacd
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_nom;

    private int $id_zona;

    private bool $propia;

    private?bool $dw1 = null;

    private?bool $dw2 = null;

    private?bool $dw3 = null;

    private?bool $dw4 = null;

    private?bool $dw5 = null;

    private?bool $dw6 = null;

    private?bool $dw7 = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }


    public function getId_zona(): int
    {
        return $this->id_zona;
    }


    public function setId_zona(int $id_zona): void
    {
        $this->id_zona = $id_zona;
    }


    public function isPropia(): bool
    {
        return $this->propia;
    }


    public function setPropia(bool $propia): void
    {
        $this->propia = $propia;
    }


    public function isDw1(): ?bool
    {
        return $this->dw1;
    }


    public function setDw1(?bool $dw1 = null): void
    {
        $this->dw1 = $dw1;
    }


    public function isDw2(): ?bool
    {
        return $this->dw2;
    }


    public function setDw2(?bool $dw2 = null): void
    {
        $this->dw2 = $dw2;
    }


    public function isDw3(): ?bool
    {
        return $this->dw3;
    }


    public function setDw3(?bool $dw3 = null): void
    {
        $this->dw3 = $dw3;
    }


    public function isDw4(): ?bool
    {
        return $this->dw4;
    }


    public function setDw4(?bool $dw4 = null): void
    {
        $this->dw4 = $dw4;
    }


    public function isDw5(): ?bool
    {
        return $this->dw5;
    }


    public function setDw5(?bool $dw5 = null): void
    {
        $this->dw5 = $dw5;
    }


    public function isDw6(): ?bool
    {
        return $this->dw6;
    }


    public function setDw6(?bool $dw6 = null): void
    {
        $this->dw6 = $dw6;
    }


    public function isDw7(): ?bool
    {
        return $this->dw7;
    }


    public function setDw7(?bool $dw7 = null): void
    {
        $this->dw7 = $dw7;
    }
}