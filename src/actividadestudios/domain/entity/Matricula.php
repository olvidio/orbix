<?php

namespace src\actividadestudios\domain\entity;

use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\value_objects\NotaSituacion;
use src\shared\domain\traits\Hydratable;
use src\actividadestudios\domain\value_objects\Acta;
use src\actividadestudios\domain\value_objects\NotaMax;
use src\actividadestudios\domain\value_objects\NotaNum;

class Matricula
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_activ;
    private AsignaturaId $id_asignatura;
    private int $id_nom;
    private ?NotaSituacion $id_situacion = null;
    private ?bool $preceptor = null;
    private ?NivelStgrId $id_nivel = null;
    private ?NotaNum $nota_num = null;
    private ?NotaMax $nota_max = null;
    private ?int $id_preceptor = null;
    private ?Acta $acta = null;

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
     * @deprecated use getIdAsignaturaVo()
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
     * @deprecated use setIdAsignaturaVo()
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


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    /**
     * @deprecated usar getIdSituacionVo()
     */
    public function getId_situacion(): ?int
    {
        return $this->id_situacion?->value();
    }
    public function getIdSituacionVo(): ?NotaSituacion
    {
        return $this->id_situacion;
    }

    /**
     * @deprecated usar setIdSituacionVo()
     */
    public function setId_situacion(?int $id_situacion = null): void
    {
        $this->id_situacion = NotaSituacion::fromNullableInt($id_situacion);
    }
    public function setIdSituacionVo(NotaSituacion|int|null $valor = null): void
    {
        $this->id_situacion = $valor instanceof NotaSituacion
            ? $valor
            : NotaSituacion::fromNullableInt($valor);
    }


    public function isPreceptor(): ?bool
    {
        return $this->preceptor;
    }


    public function setPreceptor(?bool $preceptor = null): void
    {
        $this->preceptor = $preceptor;
    }

    /**
     * @deprecated usar getIdNivelVo()
     */
    public function getId_nivel(): ?string
    {
        return $this->id_nivel?->value();
    }

    public function getIdNivelVo(): ?NivelStgrId
    {
        return $this->id_nivel;
    }

    /**
     * @deprecated usar setIdNivelVo()
     */
    public function setId_nivel(?int $id_nivel = null): void
    {
        $this->id_nivel = NivelStgrId::fromNullableInt($id_nivel);
    }

    public function setIdNivelVo(NivelStgrId|int|null $valor = null): void
    {
        $this->id_nivel = $valor instanceof NivelStgrId
            ? $valor
            : NivelStgrId::fromNullableInt($valor);
    }

    /**
     * @deprecated usar getNotaNumVo()
     */
    public function getNota_num(): ?string
    {
        return $this->nota_num?->value();
    }

    /**
     * @return NotaNum|null
     */
    public function getNotaNumVo(): ?NotaNum
    {
        return $this->nota_num;
    }

    /**
     * @deprecated usar setNotaNumVo()
     */
    public function setNota_num(?float $nota_num = null): void
    {
        $this->nota_num = NotaNum::fromNullableFloat($nota_num);
    }


    public function setNotaNumVo(NotaNum|float|null $valor = null): void
    {
        $this->nota_num = $valor instanceof NotaNum
            ? $valor
            : NotaNum::fromNullableFloat($valor);
    }

    /**
     * @deprecated usar getNotaMaxVo()
     */
    public function getNota_max(): ?string
    {
        return $this->nota_max?->value();
    }

    /**
     * @return NotaMax|null
     */
    public function getNotaMaxVo(): ?NotaMax
    {
        return $this->nota_max;
    }

    /**
     * @deprecated usar setNotaMaxVo()
     */
    public function setNota_max(?int $nota_max = null): void
    {
        $this->nota_max = NotaMax::fromNullableInt($nota_max);
    }


    public function setNotaMaxVo(NotaMax|int|null $valor = null): void
    {
        $this->nota_max = $valor instanceof NotaMax
            ? $valor
            : NotaMax::fromNullableInt($valor);
    }


    public function getId_preceptor(): ?int
    {
        return $this->id_preceptor;
    }


    public function setId_preceptor(?int $id_preceptor = null): void
    {
        $this->id_preceptor = $id_preceptor;
    }

    /**
     * @deprecated usar getActaVo()
     */
    public function getActa(): ?string
    {
        return $this->acta?->value();
    }

    /**
     * @return Acta|null
     */
    public function getActaVo(): ?Acta
    {
        return $this->acta;
    }

    /**
     * @deprecated usar setActaVo()
     */
    public function setActa(?string $acta = null): void
    {
        $this->acta = Acta::fromNullableString($acta);
    }


    public function setActaVo(Acta|string|null $texto = null): void
    {
        $this->acta = $texto instanceof Acta
            ? $texto
            : Acta::fromNullableString($texto);
    }
}