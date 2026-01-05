<?php

namespace src\procesos\domain\entity;

use src\procesos\domain\value_objects\ActividadId;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\ProcesoTipoId;
use src\procesos\domain\value_objects\TareaId;
use src\shared\domain\traits\Hydratable;
use function core\is_true;


class ActividadProcesoTarea
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private ProcesoTipoId $id_tipo_proceso;

    private ActividadId $id_activ;

    private FaseId|null $id_fase = null;

    private TareaId|null $id_tarea = null;

    private bool|null $completado = null;

    private string|null $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getProcesoTipoId(): ProcesoTipoId
    {
        return $this->id_tipo_proceso;
    }


    public function setProcesoTipoId(ProcesoTipoId $id_tipo_proceso): void
    {
        $this->id_tipo_proceso = $id_tipo_proceso;
    }

    /**
     * @deprecated use getProcesoTipoId()
     */
    public function getId_tipo_proceso(): int
    {
        return $this->id_tipo_proceso->value();
    }

    /**
     * @deprecated use setProcesoTipoId()
     */
    public function setId_tipo_proceso(int $id_tipo_proceso): void
    {
        $this->id_tipo_proceso = new ProcesoTipoId($id_tipo_proceso);
    }


    public function getActividadId(): ActividadId
    {
        return $this->id_activ;
    }


    public function setActividadId(ActividadId $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    /**
     * @deprecated use getActividadId()
     */
    public function getId_activ(): int
    {
        return $this->id_activ->value();
    }

    /**
     * @deprecated use setActividadId()
     */
    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = new ActividadId($id_activ);
    }


    public function getFaseId(): ?FaseId
    {
        return $this->id_fase;
    }


    public function setFaseId(?FaseId $id_fase = null): void
    {
        $this->id_fase = $id_fase;
    }

    /**
     * @deprecated use getFaseId()
     */
    public function getId_fase(): ?int
    {
        return $this->id_fase?->value();
    }

    /**
     * @deprecated use setFaseId()
     */
    public function setId_fase(?int $id_fase = null): void
    {
        $this->id_fase = $id_fase !== null ? new FaseId($id_fase) : null;
    }


    public function getTareaId(): ?TareaId
    {
        return $this->id_tarea;
    }


    public function setTareaId(?TareaId $id_tarea = null): void
    {
        $this->id_tarea = $id_tarea;
    }

    /**
     * @deprecated use getTareaId()
     */
    public function getId_tarea(): ?int
    {
        return $this->id_tarea?->value();
    }

    /**
     * @deprecated use setTareaId()
     */
    public function setId_tarea(?int $id_tarea = null): void
    {
        $this->id_tarea = $id_tarea !== null ? new TareaId($id_tarea) : null;
    }


    public function isCompletado(): ?bool
    {
        return $this->completado;
    }


    public function setCompletado(?bool $completado = null): void
    {
        $this->completado = $completado;
    }


    public function getObserv(): ?string
    {
        return $this->observ;
    }


    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }
}