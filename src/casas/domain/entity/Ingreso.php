<?php

namespace src\casas\domain\entity;

use src\casas\domain\value_objects\IngresoImporte;
use src\casas\domain\value_objects\IngresoNumAsistentes;
use src\casas\domain\value_objects\IngresoObserv;
use src\shared\domain\traits\Hydratable;

class Ingreso
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_activ;

    private ?IngresoImporte $ingresos = null;

    private ?IngresoNumAsistentes $num_asistentes = null;

    private ?IngresoImporte $ingresos_previstos = null;

    private ?IngresoNumAsistentes $num_asistentes_previstos = null;

    private ?IngresoObserv $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_activ(): int
    {
        return $this->id_activ;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    /**
     *
     * @return IngresoImporte|null $ingresos
     */
    public function getIngresosVo(): ?IngresoImporte
    {
        return $this->ingresos;
    }


    public function setIngresosVo(IngresoImporte|float|null $ingresos = null): void
    {
        $this->ingresos = $ingresos instanceof IngresoImporte
            ? $ingresos
            : IngresoImporte::fromNullableFloat($ingresos);
    }

    public function getNumAsistentesVo(): ?IngresoNumAsistentes
    {
        return $this->num_asistentes;
    }

    public function setNumAsistentesVo(IngresoNumAsistentes|int|null $num_asistentes = null): void
    {
        $this->num_asistentes = $num_asistentes instanceof IngresoNumAsistentes
            ? $num_asistentes
            : IngresoNumAsistentes::fromNullableInt($num_asistentes);
    }


    public function getIngresosPrevistosVo(): ?IngresoImporte
    {
        return $this->ingresos_previstos;
    }


    public function setIngresosPrevistosVo(IngresoImporte|float|null $ingresos_previstos = null): void
    {
        $this->ingresos_previstos = $ingresos_previstos instanceof IngresoImporte
            ? $ingresos_previstos
            : IngresoImporte::fromNullableFloat($ingresos_previstos);
    }


    public function getNumAsistentesPrevistosVo(): ?IngresoNumAsistentes
    {
        return $this->num_asistentes_previstos;
    }


    public function setNumAsistentesPrevistosVo(IngresoNumAsistentes|int|null $num_asistentes_previstos = null): void
    {
        $this->num_asistentes_previstos = $num_asistentes_previstos instanceof IngresoNumAsistentes
            ? $num_asistentes_previstos
            : IngresoNumAsistentes::fromNullableInt($num_asistentes_previstos);
    }


    public function getObservVo(): ?IngresoObserv
    {
        return $this->observ;
    }


    public function setObservVo(IngresoObserv|string|null $texto = null): void
    {
        $this->observ = $texto instanceof IngresoObserv
            ? $texto
            : IngresoObserv::fromNullableString($texto);
    }
}