<?php

namespace src\procesos\domain\entity;

use src\procesos\domain\value_objects\ActividadId;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\ProcesoTipoId;
use src\procesos\domain\value_objects\TareaId;
use src\shared\domain\traits\Hydratable;


class ActividadProcesoTarea
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private ProcesoTipoId $id_tipo_proceso;

    private ActividadId $id_activ;

    private FaseId|null $id_fase = null;

    private TareaId|null $id_tarea = null;

    private ?bool $completado = null;

    private ?string $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getIdTipoProcesoVo(): ProcesoTipoId
    {
        return $this->id_tipo_proceso;
    }

    public function setIdTipoProcesoVo(ProcesoTipoId|int|null $id_tipo_proceso): void
    {
        $this->id_tipo_proceso = $id_tipo_proceso instanceof ProcesoTipoId
            ? $id_tipo_proceso
            : new ProcesoTipoId($id_tipo_proceso);
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
        $this->id_tipo_proceso = ProcesoTipoId::fromNullable($id_tipo_proceso);
    }


    public function getIdActividadVo(): ActividadId
    {
        return $this->id_activ;
    }

    public function setIdActividadVo(ActividadId|int|null $id_activ): void
    {
        $this->id_activ = $id_activ instanceof ActividadId
            ? $id_activ
            : ActividadId::fromNullable($id_activ);
    }

    /**
     * @deprecated use getIdActividadVo()
     */
    public function getId_activ(): int
    {
        return $this->id_activ->value();
    }

    /**
     * @deprecated use setIdActividadVo()
     */
    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = ActividadId::fromNullable($id_activ);
    }


    public function getIdFaseVo(): ?FaseId
    {
        return $this->id_fase;
    }

    public function setIdFaseVo(FaseId|int|null $id_fase = null): void
    {
        $this->id_fase = $id_fase instanceof FaseId
            ? $id_fase
            : FaseId::fromNullable($id_fase);
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
        $this->id_fase = FaseId::fromNullable($id_fase);
    }


    public function getIdTareaVo(): ?TareaId
    {
        return $this->id_tarea;
    }


    public function setIdTareaVo(TareaId|int|null $id_tarea = null): void
    {
        $this->id_tarea = $id_tarea instanceof TareaId
            ? $id_tarea
            : TareaId::fromNullable($id_tarea);
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
        $this->id_tarea = TareaId::fromNullable($id_tarea);
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