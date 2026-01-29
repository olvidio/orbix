<?php

namespace src\actividadestudios\domain\entity;

use src\actividadestudios\domain\value_objects\ActividadAsignaturaPk;
use src\actividadestudios\domain\value_objects\AvisProfesor;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;

class ActividadAsignatura
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_schema;
    private int $id_activ;
    private AsignaturaId $id_asignatura;
    private ?int $id_profesor = null;
    private ?AvisProfesor $avis_profesor = null;
    private ?TipoActividadAsignatura $tipo = null;
    private ?DateTimeLocal $f_ini = null;
    private ?DateTimeLocal $f_fin = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getActividadAsignaturaPk()
    {
        return ActividadAsignaturaPk::fromArray([
            'id_activ' => $this->id_activ,
            'id_asignatura' => $this->id_asignatura,
        ]);
    }


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

    /**
     * @deprecated Usar `getIdAsignaturaVo(): AsignaturaId` en su lugar.
     */
    public function getId_asignatura(): int
    {
        return $this->id_asignatura->value();
    }
    public function getIdAsignaturaVo(): AsignaturaId
    {
        return $this->id_asignatura;
    }

    /**
     * @deprecated usar setIdAsignaturaVo()
     */
    public function setId_asignatura(int $id_asignatura): void
    {
        $this->id_asignatura = AsignaturaId::fromNullableInt($id_asignatura);
    }
    public function setIdAsignaturaVo(AsignaturaId|int $id_asignatura): void
    {
        $this->id_asignatura = $id_asignatura instanceof AsignaturaId
            ? $id_asignatura
            : AsignaturaId::fromNullableInt($id_asignatura);
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
     * @deprecated usar getTipoActividadAsignaturaVo()
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
     * @deprecated usar setTipoActividadAsignaturaVo()
     */
    public function setTipo(?string $tipo = null): void
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
        return $this->f_ini ?? new NullDateTimeLocal;
    }

    public function setF_ini(DateTimeLocal|null $f_ini = null): void
    {
        $this->f_ini = $f_ini;
    }

    public function getF_fin(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_fin ?? new NullDateTimeLocal;
    }

    public function setF_fin(DateTimeLocal|null $f_fin = null): void
    {
        $this->f_fin = $f_fin;
    }

}