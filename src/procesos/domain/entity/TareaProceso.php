<?php

namespace src\procesos\domain\entity;

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

    private int $status;

    private int|null $id_of_responsable = null;

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


    public function getFaseId(): FaseId
    {
        return $this->id_fase;
    }


    public function setFaseId(FaseId $id_fase): void
    {
        $this->id_fase = $id_fase;
    }

    /**
     * @deprecated use getFaseId()
     */
    public function getId_fase(): int
    {
        return $this->id_fase->value();
    }

    /**
     * @deprecated use setFaseId()
     */
    public function setId_fase(int $id_fase): void
    {
        $this->id_fase = new FaseId($id_fase);
    }


    public function getTareaId(): TareaId
    {
        return $this->id_tarea;
    }


    public function setTareaId(TareaId $id_tarea): void
    {
        $this->id_tarea = $id_tarea;
    }

    /**
     * @deprecated use getTareaId()
     */
    public function getId_tarea(): int
    {
        return $this->id_tarea->value();
    }

    /**
     * @deprecated use setTareaId()
     */
    public function setId_tarea(int $id_tarea): void
    {
        $this->id_tarea = new TareaId($id_tarea);
    }


    public function getStatus(): int
    {
        return $this->status;
    }


    public function setStatus(int $istatus): void
    {
        $this->status = $istatus;
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