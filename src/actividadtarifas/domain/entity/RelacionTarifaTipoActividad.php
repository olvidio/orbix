<?php

namespace src\actividadtarifas\domain\entity;

use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\shared\domain\traits\Hydratable;


class RelacionTarifaTipoActividad
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_tarifa;

    private int $id_tipo_activ;

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

    /**
     * @deprecated Usar getIdTarifaVo(): TarifaId
     */
    public function getId_tarifa(): int
    {
        return $this->id_tarifa;
    }

    /**
     * @deprecated Usar setIdTarifaVo(TarifaId $id): void
     */
    public function setId_tarifa(int $id_tarifa): void
    {
        $this->id_tarifa = $id_tarifa;
    }

    public function getIdTarifaVo(): TarifaId
    {
        return new TarifaId($this->id_tarifa);
    }

    public function setIdTarifaVo(TarifaId $id): void
    {
        $this->id_tarifa = $id->value();
    }

    /**
     * @deprecated Usar getIdTipoActividadVo(): TipoActividadId
     */
    public function getId_tipo_activ(): int
    {
        return $this->id_tipo_activ;
    }

    /**
     * @deprecated Usar setIdTipoActividadVo(TipoActividadId $id): void
     */
    public function setId_tipo_activ(int $id_tipo_activ): void
    {
        $this->id_tipo_activ = $id_tipo_activ;
    }

    public function getIdTipoActividadVo(): ActividadTipoId
    {
        return new ActividadTipoId($this->id_tipo_activ);
    }

    public function setIdTipoActividadVo(ActividadTipoId $id): void
    {
        $this->id_tipo_activ = $id->value();
    }

    /**
     * @deprecated Usar getIdSerieVo(): SerieId
     */
    public function getId_serie(): int
    {
        return $this->id_serie;
    }

    /**
     * @deprecated Usar setIdSerieVo(SerieId $id): void
     */
    public function setId_serie(int $id_serie): void
    {
        $this->id_serie = $id_serie;
    }

    public function getIdSerieVo(): SerieId
    {
        return new SerieId($this->id_serie);
    }

    public function setIdSerieVo(SerieId $id): void
    {
        $this->id_serie = $id->value();
    }
}