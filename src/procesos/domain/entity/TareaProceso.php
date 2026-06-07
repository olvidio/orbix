<?php

namespace src\procesos\domain\entity;

use src\actividades\domain\value_objects\StatusId;
use src\menus\domain\PermisoMenuBits;
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

    /** @var list<array<string, mixed>>|stdClass|null */
    private array|stdClass|null $json_fases_previas = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getOf_responsable_txt(): string
    {
        // para crear el array id_oficina => oficina_txt. Uso los de los menus
        $aOpcionesOficinas = PermisoMenuBits::valueToLabel();
        $id_of_responsable = $this->getId_of_responsable();
        $of_responsable_txt = empty($aOpcionesOficinas[$id_of_responsable]) ? '' : $aOpcionesOficinas[$id_of_responsable];
        return $of_responsable_txt;
    }


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
            : (ProcesoTipoId::fromNullableInt($id_tipo_proceso) ?? throw new \InvalidArgumentException('id_tipo_proceso cannot be null'));
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
        $this->id_tipo_proceso = (ProcesoTipoId::fromNullableInt($id_tipo_proceso) ?? throw new \InvalidArgumentException('id_tipo_proceso cannot be null'));
    }


    public function getIdFaseVo(): FaseId
    {
        return $this->id_fase;
    }


    public function setIdFaseVo(FaseId|int|null $id_fase): void
    {
        $this->id_fase = $id_fase instanceof FaseId
            ? $id_fase
            : (FaseId::fromNullableInt($id_fase) ?? throw new \InvalidArgumentException('id_fase cannot be null'));
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
        $this->id_fase = (FaseId::fromNullableInt($id_fase) ?? throw new \InvalidArgumentException('id_fase cannot be null'));
    }


    public function getIdTareaVo(): TareaId
    {
        return $this->id_tarea;
    }


    public function setIdTareaVo(TareaId|int|null $id_tarea): void
    {
        $this->id_tarea = $id_tarea instanceof TareaId
            ? $id_tarea
            : (TareaId::fromNullableInt($id_tarea) ?? throw new \InvalidArgumentException('id_tarea cannot be null'));
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
        $this->id_tarea = (TareaId::fromNullableInt($id_tarea) ?? throw new \InvalidArgumentException('id_tarea cannot be null'));
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
        $this->status = (StatusId::fromNullableInt($istatus) ?? throw new \InvalidArgumentException('status cannot be null'));
    }
    public function getStatusVo(): StatusId
    {
        return $this->status;
    }
    public function setStatusVo(StatusId|int|null $status): void
    {
        $this->status = $status instanceof StatusId
            ? $status
            : (StatusId::fromNullableInt($status) ?? throw new \InvalidArgumentException('status cannot be null'));
    }


    public function getId_of_responsable(): ?int
    {
        return $this->id_of_responsable;
    }


    public function setId_of_responsable(?int $id_of_responsable = null): void
    {
        $this->id_of_responsable = $id_of_responsable;
    }


    /**
     * @return list<array<string, mixed>>|stdClass|null
     */
    public function getJson_fases_previas(): array|stdClass|null
    {
        return $this->json_fases_previas;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getJsonFasesPreviasAsList(): array
    {
        $data = $this->json_fases_previas;
        if ($data === null) {
            return [];
        }
        if ($data instanceof stdClass) {
            $data = (array) $data;
        }
        $result = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $result[] = $item;
            } elseif (is_object($item)) {
                $result[] = (array) $item;
            }
        }

        return $result;
    }

    /**
     * @param list<array<string, mixed>>|stdClass|null $json_fases_previas
     */
    public function setJson_fases_previas(stdClass|array|null $json_fases_previas = null): void
    {
        $this->json_fases_previas = $json_fases_previas;
    }
}