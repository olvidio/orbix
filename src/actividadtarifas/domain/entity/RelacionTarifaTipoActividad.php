<?php

namespace src\actividadtarifas\domain\entity;

use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaId;

/**
 * Clase que implementa la entidad xa_tipo_activ_tarifa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 3/12/2025
 */
class RelacionTarifaTipoActividad
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de RelacionTarifaTipoActividad
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_tarifa de RelacionTarifaTipoActividad
     *
     * @var int
     */
    private int $iid_tarifa;
    /**
     * Id_tipo_activ de RelacionTarifaTipoActividad
     *
     * @var int
     */
    private int $iid_tipo_activ;
    /**
     * Id_serie de RelacionTarifaTipoActividad
     *
     * @var int
     */
    private int $iid_serie;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return RelacionTarifaTipoActividad
     */
    public function setAllAttributes(array $aDatos): RelacionTarifaTipoActividad
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_tarifa', $aDatos)) {
            $val = $aDatos['id_tarifa'];
            if ($val instanceof TarifaId) {
                $this->setIdTarifaVo($val);
            } else {
                $this->setId_tarifa($val);
            }
        }
        if (array_key_exists('id_tipo_activ', $aDatos)) {
            $val = $aDatos['id_tipo_activ'];
            if ($val instanceof ActividadTipoId) {
                $this->setIdTipoActividadVo($val);
            } else {
                $this->setId_tipo_activ($val);
            }
        }
        if (array_key_exists('id_serie', $aDatos)) {
            $val = $aDatos['id_serie'];
            if ($val instanceof SerieId) {
                $this->setIdSerieVo($val);
            } else {
                $this->setId_serie($val);
            }
        }
        return $this;
    }

    /**
     *
     * @return int $iid_item
     */
    public function getId_item(): int
    {
        return $this->iid_item;
    }

    /**
     *
     * @param int $iid_item
     */
    public function setId_item(int $iid_item): void
    {
        $this->iid_item = $iid_item;
    }
    /**
     *
     * @return int $iid_tarifa
     */
    /**
     * @deprecated Usar getIdTarifaVo(): TarifaId
     */
    public function getId_tarifa(): int
    {
        return $this->iid_tarifa;
    }
    /**
     *
     * @param int $iid_tarifa
     */
    /**
     * @deprecated Usar setIdTarifaVo(TarifaId $id): void
     */
    public function setId_tarifa(int $iid_tarifa): void
    {
        $this->iid_tarifa = $iid_tarifa;
    }

    public function getIdTarifaVo(): TarifaId
    {
        return new TarifaId($this->iid_tarifa);
    }

    public function setIdTarifaVo(TarifaId $id): void
    {
        $this->iid_tarifa = $id->value();
    }
    /**
     *
     * @return int $iid_tipo_activ
     */
    /**
     * @deprecated Usar getIdTipoActividadVo(): TipoActividadId
     */
    public function getId_tipo_activ(): int
    {
        return $this->iid_tipo_activ;
    }
    /**
     *
     * @param int $iid_tipo_activ
     */
    /**
     * @deprecated Usar setIdTipoActividadVo(TipoActividadId $id): void
     */
    public function setId_tipo_activ(int $iid_tipo_activ): void
    {
        $this->iid_tipo_activ = $iid_tipo_activ;
    }

    public function getIdTipoActividadVo(): ActividadTipoId
    {
        return new ActividadTipoId($this->iid_tipo_activ);
    }

    public function setIdTipoActividadVo(ActividadTipoId $id): void
    {
        $this->iid_tipo_activ = $id->value();
    }
    /**
     *
     * @return int $iid_serie
     */
    /**
     * @deprecated Usar getIdSerieVo(): SerieId
     */
    public function getId_serie(): int
    {
        return $this->iid_serie;
    }
    /**
     *
     * @param int $iid_serie
     */
    /**
     * @deprecated Usar setIdSerieVo(SerieId $id): void
     */
    public function setId_serie(int $iid_serie): void
    {
        $this->iid_serie = $iid_serie;
    }

    public function getIdSerieVo(): SerieId
    {
        return new SerieId($this->iid_serie);
    }

    public function setIdSerieVo(SerieId $id): void
    {
        $this->iid_serie = $id->value();
    }
}