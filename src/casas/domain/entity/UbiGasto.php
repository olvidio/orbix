<?php

namespace src\casas\domain\entity;

use src\casas\domain\value_objects\UbiGastoTipo;
use src\casas\domain\value_objects\UbiGastoCantidad;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class UbiGasto
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_ubi;

    private DateTimeLocal $f_gasto;

    private ?UbiGastoTipo $tipo = null;

    private ?UbiGastoCantidad $cantidad = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }

    public function setId_item($id_item): void
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


    public function getF_gasto(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_gasto ?? new NullDateTimeLocal;
    }


    public function setF_gasto(DateTimeLocal|null $f_gasto = null): void
    {
        $this->f_gasto = $f_gasto;
    }


    public function getTipoVo(): ?UbiGastoTipo
    {
        return $this->tipo;
    }


    public function setTipoVo(UbiGastoTipo|int|null $texto = null): void
    {
        $this->tipo = $texto instanceof UbiGastoTipo
            ? $texto
            : UbiGastoTipo::fromNullable($texto);
    }


    public function getCantidadVo(): ?UbiGastoCantidad
    {
        return $this->cantidad;
    }


    public function setCantidadVo(UbiGastoCantidad|float|null $valor = null): void
    {
        $this->cantidad = $valor instanceof UbiGastoCantidad
            ? $valor
            : UbiGastoCantidad::fromNullableFloat($valor);
    }
}