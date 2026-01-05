<?php

namespace src\actividadestudios\domain\entity;

use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use src\actividadestudios\domain\value_objects\AvisProfesor;
use src\actividadestudios\domain\value_objects\FechaFin;
use src\actividadestudios\domain\value_objects\FechaInicio;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;

class ActividadAsignatura
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_schema;
    private int $id_activ;
    private int $id_asignatura;
    private int|null $id_profesor = null;
    private string|null $avis_profesor = null;
    private string|null $tipo = null;
    private DateTimeLocal|null $df_ini = null;
    private DateTimeLocal|null $df_fin = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_schema(): int
    {
        return $this->id_schema;
    }


    public function setId_schema(int $id_schema): void
    {
        $this->id_schema = $id_schema;
    }


    public function getId_activ(): int
    {
        return $this->id_activ;
    }


    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }


    public function getId_asignatura(): int
    {
        return $this->id_asignatura;
    }


    public function setId_asignatura(int $id_asignatura): void
    {
        $this->id_asignatura = $id_asignatura;
    }


    public function getId_profesor(): ?int
    {
        return $this->id_profesor;
    }


    public function setId_profesor(?int $id_profesor = null): void
    {
        $this->id_profesor = $id_profesor;
    }

    /**
     * @return string|null $savis_profesor
     */
    public function getAvis_profesor(): ?string
    {
        return $this->avis_profesor;
    }

    /**
     * @return AvisProfesor|null
     */
    public function getAvisProfesor(): ?AvisProfesor
    {
        return AvisProfesor::fromNullable($this->avis_profesor);
    }

    /**
     * @deprecated usar setAvisProfesor()
     */
    public function setAvis_profesor(?string $avis_profesor = null): void
    {
        $this->avis_profesor = $avis_profesor;
    }

    /**
     * @param AvisProfesor|null $AvisProfesor
     */
    public function setAvisProfesor(?AvisProfesor $AvisProfesor = null): void
    {
        $this->avis_profesor = $AvisProfesor?->value();
    }

    /**
     * @deprecated usar getTipoVo()
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * @return TipoActividadAsignatura|null
     */
    public function getTipoVo(): ?TipoActividadAsignatura
    {
        return TipoActividadAsignatura::fromNullable($this->tipo);
    }

    /**
     * @deprecated usar setTipoVo()
     */
    public function setTipo(string|null $tipo = null): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @param TipoActividadAsignatura|null $Tipo
     */
    public function setTipoVo(?TipoActividadAsignatura $Tipo = null): void
    {
        $this->tipo = $Tipo?->value();
    }

    public function getF_ini(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_ini ?? new NullDateTimeLocal;
    }

    public function setF_ini(DateTimeLocal|null $df_ini = null): void
    {
        $this->df_ini = $df_ini;
    }

    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_fin ?? new NullDateTimeLocal;
    }

    public function setF_fin(DateTimeLocal|null $df_fin = null): void
    {
        $this->df_fin = $df_fin;
    }

}