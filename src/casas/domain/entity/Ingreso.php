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
    public function getIngresos(): ?IngresoImporte
    {
        return $this->ingresos;
    }

    /**
     *
     * @param IngresoImporte|null $ingresos
     */
    public function setIngresos(?IngresoImporte $ingresos = null): void
    {
        $this->ingresos = $ingresos;
    }

    public function getNum_asistentes(): ?IngresoNumAsistentes
    {
        return $this->num_asistentes;
    }

    public function setNum_asistentes(?IngresoNumAsistentes $num_asistentes = null): void
    {
        $this->num_asistentes = $num_asistentes;
    }


    public function getIngresos_previstos(): ?IngresoImporte
    {
        return $this->ingresos_previstos;
    }


    public function setIngresos_previstos(?IngresoImporte $ingresos_previstos = null): void
    {
        $this->ingresos_previstos = $ingresos_previstos;
    }


    public function getNum_asistentes_previstos(): ?IngresoNumAsistentes
    {
        return $this->num_asistentes_previstos;
    }


    public function setNum_asistentes_previstos(?IngresoNumAsistentes $num_asistentes_previstos = null): void
    {
        $this->num_asistentes_previstos = $num_asistentes_previstos;
    }


    public function getObserv(): ?IngresoObserv
    {
        return $this->observ;
    }


    public function setObserv(?IngresoObserv $sobserv = null): void
    {
        $this->observ = $sobserv;
    }
}