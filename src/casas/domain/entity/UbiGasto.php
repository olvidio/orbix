<?php

namespace src\casas\domain\entity;

use src\casas\domain\value_objects\UbiGastoTipo;
use src\casas\domain\value_objects\UbiGastoCantidad;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Clase que implementa la entidad du_gastos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 22/12/2025
 */
class UbiGasto
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de UbiGasto
     *
     */
    private int $iid_item;
    /**
     * Id_ubi de UbiGasto
     *
     */
    private int $iid_ubi;
    /**
     * F_gasto de UbiGasto
     *
     * @var DateTimeLocal
     */
    private DateTimeLocal $df_gasto;
    /**
     * Tipo de UbiGasto
     *
     * @var UbiGastoTipo|null
     */
    private ?UbiGastoTipo $itipo = null;
    /**
     * Cantidad de UbiGasto
     *
     * @var UbiGastoCantidad|null
     */
    private ?UbiGastoCantidad $icantidad = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return UbiGasto
     */
    public function setAllAttributes(array $aDatos): UbiGasto
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $this->setId_ubi($aDatos['id_ubi']);
        }
        if (array_key_exists('f_gasto', $aDatos)) {
            $this->setF_gasto($aDatos['f_gasto']);
        }
        if (array_key_exists('tipo', $aDatos)) {
            $this->setTipoVo(UbiGastoTipo::fromNullableInt($aDatos['tipo']));
        }
        if (array_key_exists('cantidad', $aDatos)) {
            $this->setCantidadVo(UbiGastoCantidad::fromNullableFloat($aDatos['cantidad']));
        }
        return $this;
    }

    public function getId_item(): int
    {
        return $this->iid_item;
    }

    public function setId_item($iid_item): void
    {
        $this->iid_item = $iid_item;
    }

    public function getId_ubi(): int
    {
        return $this->iid_ubi;
    }

    public function setId_ubi(int $iid_ubi): void
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_gasto
     */
    public function getF_gasto(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_gasto ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|null $df_gasto
     */
    public function setF_gasto(DateTimeLocal|null $df_gasto = null): void
    {
        $this->df_gasto = $df_gasto;
    }

    /**
     *
     * @return UbiGastoTipo|null $itipo
     */
    public function getTipoVo(): ?UbiGastoTipo
    {
        return $this->itipo;
    }

    /**
     *
     * @param UbiGastoTipo|null $itipo
     */
    public function setTipoVo(?UbiGastoTipo $itipo = null): void
    {
        $this->itipo = $itipo;
    }

    /**
     *
     * @return UbiGastoCantidad|null $icantidad
     */
    public function getCantidadVo(): ?UbiGastoCantidad
    {
        return $this->icantidad;
    }

    /**
     *
     * @param UbiGastoCantidad|null $icantidad
     */
    public function setCantidadVo(?UbiGastoCantidad $icantidad = null): void
    {
        $this->icantidad = $icantidad;
    }
}