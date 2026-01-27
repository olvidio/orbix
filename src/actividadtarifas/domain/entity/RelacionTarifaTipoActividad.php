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

    private TarifaId $id_tarifa;

    private ActividadTipoId $id_tipo_activ;

    private SerieId $id_serie;

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
        return $this->id_tarifa->value();
    }

    /**
     * @deprecated Usar setIdTarifaVo(TarifaId $id): void
     */
    public function setId_tarifa(int $id_tarifa): void
    {
        $this->id_tarifa = TarifaId::fromNullableInt($id_tarifa);
    }

    public function getIdTarifaVo(): TarifaId
    {
        return $this->id_tarifa;
    }

    public function setIdTarifaVo(TarifaId|int $id): void
    {
        $this->id_tarifa = $id instanceof TarifaId
            ? $id
            : TarifaId::fromNullableInt($id);
    }

    /**
     * @deprecated Usar getIdTipoActividadVo(): TipoActividadId
     */
    public function getId_tipo_activ(): int
    {
        return $this->id_tipo_activ->value();
    }

    /**
     * @deprecated Usar setIdTipoActividadVo(TipoActividadId $id): void
     */
    public function setId_tipo_activ(int $id_tipo_activ): void
    {
        $this->id_tipo_activ = ActividadTipoId::fromNullableInt($id_tipo_activ);
    }

    public function getIdTipoActividadVo(): ActividadTipoId
    {
        return $this->id_tipo_activ;
    }

    public function setIdTipoActividadVo(ActividadTipoId|int $id): void
    {
        $this->id_tipo_activ = $id instanceof ActividadTipoId
            ? $id
            : ActividadTipoId::fromNullableInt($id);
    }

    /**
     * @deprecated Usar getIdSerieVo(): SerieId
     */
    public function getId_serie(): int
    {
        return $this->id_serie->value();
    }

    /**
     * @deprecated Usar setIdSerieVo(SerieId $id): void
     */
    public function setId_serie(int $id_serie): void
    {
        $this->id_serie = SerieId::fromNullableInt($id_serie);
    }

    public function getIdSerieVo(): SerieId
    {
        return $this->id_serie;
    }

    public function setIdSerieVo(SerieId|int $id): void
    {
        $this->id_serie = $id instanceof SerieId
            ? $id
            : SerieId::fromNullableInt($id);
    }
}