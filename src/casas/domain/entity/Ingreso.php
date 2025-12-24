<?php

namespace src\casas\domain\entity;

use src\casas\domain\value_objects\IngresoImporte;
use src\casas\domain\value_objects\IngresoNumAsistentes;
use src\casas\domain\value_objects\IngresoObserv;

/**
 * Clase que implementa la entidad da_ingresos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 22/12/2025
 */
class Ingreso
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_activ de Ingreso
     *
     */
    private int $iid_activ;
    /**
     * Ingresos de Ingreso
     *
     * @var IngresoImporte|null
     */
    private ?IngresoImporte $iingresos = null;
    /**
     * Num_asistentes de Ingreso
     *
     * @var IngresoNumAsistentes|null
     */
    private ?IngresoNumAsistentes $inum_asistentes = null;
    /**
     * Ingresos_previstos de Ingreso
     *
     * @var IngresoImporte|null
     */
    private ?IngresoImporte $iingresos_previstos = null;
    /**
     * Num_asistentes_previstos de Ingreso
     *
     * @var IngresoNumAsistentes|null
     */
    private ?IngresoNumAsistentes $inum_asistentes_previstos = null;
    /**
     * Observ de Ingreso
     *
     * @var IngresoObserv|null
     */
    private ?IngresoObserv $sobserv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Ingreso
     */
    public function setAllAttributes(array $aDatos): Ingreso
    {
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        if (array_key_exists('ingresos', $aDatos)) {
            $this->setIngresos(IngresoImporte::fromNullableFloat($aDatos['ingresos']));
        }
        if (array_key_exists('num_asistentes', $aDatos)) {
            $this->setNum_asistentes(IngresoNumAsistentes::fromNullableInt($aDatos['num_asistentes']));
        }
        if (array_key_exists('ingresos_previstos', $aDatos)) {
            $this->setIngresos_previstos(IngresoImporte::fromNullableFloat($aDatos['ingresos_previstos']));
        }
        if (array_key_exists('num_asistentes_previstos', $aDatos)) {
            $this->setNum_asistentes_previstos(IngresoNumAsistentes::fromNullableInt($aDatos['num_asistentes_previstos']));
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv(IngresoObserv::fromNullableString($aDatos['observ']));
        }
        return $this;
    }

    public function getId_activ(): int
    {
        return $this->iid_activ;
    }

    public function setId_activ(int $iid_activ): void
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     *
     * @return IngresoImporte|null $iingresos
     */
    public function getIngresos(): ?IngresoImporte
    {
        return $this->iingresos;
    }

    /**
     *
     * @param IngresoImporte|null $iingresos
     */
    public function setIngresos(?IngresoImporte $iingresos = null): void
    {
        $this->iingresos = $iingresos;
    }

    /**
     *
     * @return IngresoNumAsistentes|null $inum_asistentes
     */
    public function getNum_asistentes(): ?IngresoNumAsistentes
    {
        return $this->inum_asistentes;
    }

    /**
     *
     * @param IngresoNumAsistentes|null $inum_asistentes
     */
    public function setNum_asistentes(?IngresoNumAsistentes $inum_asistentes = null): void
    {
        $this->inum_asistentes = $inum_asistentes;
    }

    /**
     *
     * @return IngresoImporte|null $iingresos_previstos
     */
    public function getIngresos_previstos(): ?IngresoImporte
    {
        return $this->iingresos_previstos;
    }

    /**
     *
     * @param IngresoImporte|null $iingresos_previstos
     */
    public function setIngresos_previstos(?IngresoImporte $iingresos_previstos = null): void
    {
        $this->iingresos_previstos = $iingresos_previstos;
    }

    /**
     *
     * @return IngresoNumAsistentes|null $inum_asistentes_previstos
     */
    public function getNum_asistentes_previstos(): ?IngresoNumAsistentes
    {
        return $this->inum_asistentes_previstos;
    }

    /**
     *
     * @param IngresoNumAsistentes|null $inum_asistentes_previstos
     */
    public function setNum_asistentes_previstos(?IngresoNumAsistentes $inum_asistentes_previstos = null): void
    {
        $this->inum_asistentes_previstos = $inum_asistentes_previstos;
    }

    /**
     *
     * @return IngresoObserv|null $sobserv
     */
    public function getObserv(): ?IngresoObserv
    {
        return $this->sobserv;
    }

    /**
     *
     * @param IngresoObserv|null $sobserv
     */
    public function setObserv(?IngresoObserv $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }
}