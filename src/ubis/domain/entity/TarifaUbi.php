<?php

namespace src\ubis\domain\entity;

use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\profesores\domain\value_objects\YearNumber;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\value_objects\ObservCasaText;
use src\ubis\domain\value_objects\TarifaCantidad;


class TarifaUbi
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_ubi;

    private TarifaId $id_tarifa;

    private YearNumber $year;

    private TarifaCantidad $cantidad;

    private ?ObservCasaText $observ = null;

    private serieId $id_serie;

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

    /**
     * @deprecated use getIdTarifaVo()
     */
    public function getId_tarifa(): int
    {
        return $this->id_tarifa->value();
    }

    public function getIdTarifaVo(): TarifaId
    {
        return $this->id_tarifa;
    }


    /**
     * @deprecated use setIdTarifaVo()
     */
    public function setId_tarifa(int $id_tarifa): void
    {
        $this->id_tarifa = TarifaId::fromNullableInt( $id_tarifa);
    }
    public function setIdTarifaVo(TarifaId|int|null $valor = null): void
    {
        $this->id_tarifa = $valor instanceof TarifaId
            ? $valor
            : TarifaId::fromNullableInt($valor);
    }

    /**
     * @deprecated use getYearVo()
     */
    public function getYear(): int
    {
        return $this->year->value();
    }
    public function getYearVo(): YearNumber
    {
        return $this->year;
    }

    /**
     * @deprecated use setYearVo()
     */
    public function setYear(int $year): void
    {
        $this->year = YearNumber::fromNullableInt($year);
    }
    public function setYearVo(YearNumber|int|null $valor = null): void
    {
        $this->year = $valor instanceof YearNumber
            ? $valor
            : YearNumber::fromNullableInt($valor);
    }

    /**
     * @deprecated use getCantidadVo()
     */
    public function getCantidad(): ?float
    {
        return $this->cantidad?->value();
    }
    public function getCantidadVo(): ?TarifaCantidad
    {
       return $this->cantidad;
    }

    /**
     * @deprecated use setCantidadVo()
     */
    public function setCantidad(float $cantidad): void
    {
        $this->cantidad = TarifaCantidad::fromNullableFloat($cantidad);
    }
    public function setCantidadVo(TarifaCantidad|float|null $vo): void
    {
        $this->cantidad = $vo instanceof TarifaCantidad
            ? $vo
            : TarifaCantidad::fromNullableFloat($vo);
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }
    public function getObservVo(): ?ObservCasaText
    {
        return $this->observ;
    }

    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservCasaText::fromNullableString($observ);
    }
    public function setObservVo(ObservCasaText|string|null $texto = null): void
    {
        $this->observ = $texto instanceof ObservCasaText
            ? $texto
            : ObservCasaText::fromNullableString($texto);
    }

    /**
     * @deprecated use getIdSerieVo()
     */
    public function getId_serie(): int
    {
        return $this->id_serie->value();
    }
    public function getIdSerieVo(): SerieId
    {
        return $this->id_serie;
    }

    /**
     * @deprecated use setIdSerieVo()
     */
    public function setId_serie(int $id_serie): void
    {
        $this->id_serie = SerieId::fromNullableInt($id_serie);
    }
    public function setIdSerieVo(SerieId|int|null $valor = null): void
    {
        $this->id_serie = $valor instanceof SerieId
            ? $valor
            : SerieId::fromNullableInt($valor);
    }

}