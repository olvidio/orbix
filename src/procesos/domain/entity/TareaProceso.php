<?php

namespace src\procesos\domain\entity;

use src\actividades\domain\value_objects\StatusId;
use src\procesos\domain\value_objects\FaseId;
use src\procesos\domain\value_objects\ProcesoTipoId;
use src\procesos\domain\value_objects\TareaId;
use src\shared\domain\traits\Hydratable;
use stdClass;


class TareaProceso
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private ProcesoTipoId $id_tipo_proceso;

    private FaseId $id_fase;

    private TareaId $id_tarea;

    private StatusId $status;

    private ?int $id_of_responsable = null;

    private array|stdClass|null $json_fases_previas = null;

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
            : ProcesoTipoId::fromNullableInt($id_tipo_proceso);
    }

    /**
     * @deprecated use getIdTipoProcesoVo()
     */
    public function getId_tipo_proceso(): int
    {
        return $this->id_tipo_proceso->value();
    }

    /**
     * @deprecated use setIdTipoProcesoVo()
     */
    public function setId_tipo_proceso(int $id_tipo_proceso): void
    {
        $this->id_tipo_proceso = ProcesoTipoId::fromNullableInt($id_tipo_proceso);
    }


    public function getIdFaseVo(): FaseId
    {
        return $this->id_fase;
    }


    public function setIdFaseVo(FaseId|int|null $id_fase): void
    {
        $this->id_fase = $id_fase instanceof FaseId
            ? $id_fase
            : FaseId::fromNullableInt($id_fase);
    }

    /**
     * @deprecated use getIdFaseVo()
     */
    public function getId_fase(): int
    {
        return $this->id_fase->value();
    }

    /**
     * @deprecated use setIdFaseVo()
     */
    public function setId_fase(int $id_fase): void
    {
        $this->id_fase = FaseId::fromNullableInt($id_fase);
    }


    public function getIdTareaVo(): TareaId
    {
        return $this->id_tarea;
    }


    public function setIdTareaVo(TareaId|int|null $id_tarea): void
    {
        $this->id_tarea = $id_tarea instanceof TareaId
            ? $id_tarea
            : TareaId::fromNullableInt($id_tarea);
    }

    /**
     * @deprecated use getIdTareaVo()
     */
    public function getId_tarea(): int
    {
        return $this->id_tarea->value();
    }

    /**
     * @deprecated use setIdTareaVo()
     */
    public function setId_tarea(int $id_tarea): void
    {
        $this->id_tarea = TareaId::fromNullableInt($id_tarea);
    }


    /**
     * @deprecated use getStatusVo()
     */
    public function getStatus(): int
    {
        return $this->status->value();
    }

    /**
     * @deprecated use setStatusVo()
     */
    public function setStatus(int $istatus): void
    {
        $this->status = StatusId::fromNullableInt($istatus);
    }
    public function getStatusVo(): StatusId
    {
        return $this->status;
    }
    public function setStatusVo(StatusId|int|null $status): void
    {
        $this->status = $status instanceof StatusId
            ? $status
            : StatusId::fromNullableInt($status);
    }


    public function getId_of_responsable(): ?int
    {
        return $this->id_of_responsable;
    }


    public function setId_of_responsable(?int $id_of_responsable = null): void
    {
        $this->id_of_responsable = $id_of_responsable;
    }


    public function getJson_fases_previas(): array|stdClass|null
    {
        return $this->json_fases_previas;
    }


    public function setJson_fases_previas(stdClass|array|null $json_fases_previas = null): void
    {
        $this->json_fases_previas = $json_fases_previas;
    }
}