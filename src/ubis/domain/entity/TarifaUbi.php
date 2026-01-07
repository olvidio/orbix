<?php

namespace src\ubis\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\ubis\domain\value_objects\TarifaCantidad;


class TarifaUbi
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_ubi;

    private int $id_tarifa;

    private int $year;

    private float $cantidad;

    private ?string $observ = null;

    private int $id_serie;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }


    public function getId_tarifa(): int
    {
        return $this->id_tarifa;
    }


    public function setId_tarifa(int $id_tarifa): void
    {
        $this->id_tarifa = $id_tarifa;
    }


    public function getYear(): int
    {
        return $this->year;
    }


    public function setYear(int $year): void
    {
        $this->year = $year;
    }


    public function getCantidad(): float
    {
        return $this->cantidad;
    }


    public function setCantidad(float $cantidad): void
    {
        $this->cantidad = $cantidad;
    }

    public function getCantidadVo(): TarifaCantidad
    {
        return new TarifaCantidad($this->cantidad);
    }

    public function setCantidadVo(TarifaCantidad $vo): void
    {
        $this->cantidad = $vo->value();
    }


    public function getObserv(): ?string
    {
        return $this->observ;
    }


    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }


    public function getId_serie(): int
    {
        return $this->id_serie;
    }


    public function setId_serie(int $id_serie): void
    {
        $this->id_serie = $id_serie;
    }
}