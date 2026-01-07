<?php

namespace src\actividadestudios\domain\entity;

use src\actividadestudios\domain\value_objects\AvisProfesor;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class ActividadAsignatura
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_schema;
    private int $id_activ;
    private int $id_asignatura;
    private ?int $id_profesor = null;
    private ?AvisProfesor $avis_profesor = null;
    private ?TipoActividadAsignatura $tipo = null;
    private ?DateTimeLocal $df_ini = null;
    private ?DateTimeLocal $df_fin = null;

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
        return $this->avis_profesor?->value();
    }

    /**
     * @return AvisProfesor|null
     */
    public function getAvisProfesorVo(): ?AvisProfesor
    {
        return $this->avis_profesor;
    }

    /**
     * @deprecated usar setAvisProfesor()
     */
    public function setAvis_profesor(?string $avis_profesor = null): void
    {
        $this->avis_profesor = AvisProfesor::fromNullableString($avis_profesor);
    }


    public function setAvisProfesorVo(AvisProfesor|string|null $texto = null): void
    {
        $this->avis_profesor = $texto instanceof AvisProfesor
            ? $texto
            : AvisProfesor::fromNullableString($texto);
    }

    /**
     * @deprecated usar getTipoVo()
     */
    public function getTipo(): ?string
    {
        return $this->tipo?->value();
    }

    /**
     * @return TipoActividadAsignatura|null
     */
    public function getTipoVo(): ?TipoActividadAsignatura
    {
        return $this->tipo;
    }

    /**
     * @deprecated usar setTipoVo()
     */
    public function setTipo(string|null $tipo = null): void
    {
        $this->tipo = TipoActividadAsignatura::fromNullableString($tipo);
    }


    public function setTipoVo(TipoActividadAsignatura|string|null $texto = null): void
    {
        $this->tipo = $texto instanceof TipoActividadAsignatura
            ? $texto
            : TipoActividadAsignatura::fromNullableString($texto);
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