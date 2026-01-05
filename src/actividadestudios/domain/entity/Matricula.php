<?php

namespace src\actividadestudios\domain\entity;

use src\shared\domain\traits\Hydratable;
use function core\is_true;
use src\actividadestudios\domain\value_objects\Acta;
use src\actividadestudios\domain\value_objects\NotaMax;
use src\actividadestudios\domain\value_objects\NotaNum;

class Matricula
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_activ;
    private int $id_asignatura;
    private int $id_nom;
    private int|null $id_situacion = null;
    private bool|null $preceptor = null;
    private int|null $id_nivel = null;
    private float|null $nota_num = null;
    private int|null $nota_max = null;
    private int|null $id_preceptor = null;
    private string|null $acta = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

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


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }


    public function getId_situacion(): ?int
    {
        return $this->id_situacion;
    }


    public function setId_situacion(?int $id_situacion = null): void
    {
        $this->id_situacion = $id_situacion;
    }


    public function isPreceptor(): ?bool
    {
        return $this->preceptor;
    }


    public function setPreceptor(?bool $bpreceptor = null): void
    {
        $this->preceptor = $bpreceptor;
    }


    public function getId_nivel(): ?int
    {
        return $this->id_nivel;
    }


    public function setId_nivel(?int $id_nivel = null): void
    {
        $this->id_nivel = $id_nivel;
    }

    /**
     * @deprecated usar getNota_numVo()
     */
    public function getNota_num(): ?float
    {
        return $this->nota_num;
    }

    /**
     * @return NotaNum|null
     */
    public function getNota_numVo(): ?NotaNum
    {
        return NotaNum::fromNullable($this->nota_num);
    }

    /**
     * @deprecated usar setNota_numVo()
     */
    public function setNota_num(?float $nota_num = null): void
    {
        $this->nota_num = $nota_num;
    }

    /**
     * @param NotaNum|null $NotaNum
     */
    public function setNota_numVo(?NotaNum $NotaNum = null): void
    {
        $this->nota_num = $NotaNum?->value();
    }

    /**
     * @deprecated usar getNota_maxVo()
     */
    public function getNota_max(): ?int
    {
        return $this->nota_max;
    }

    /**
     * @return NotaMax|null
     */
    public function getNota_maxVo(): ?NotaMax
    {
        return NotaMax::fromNullable($this->nota_max);
    }

    /**
     * @deprecated usar setNota_maxVo()
     */
    public function setNota_max(?int $nota_max = null): void
    {
        $this->nota_max = $nota_max;
    }

    /**
     * @param NotaMax|null $NotaMax
     */
    public function setNota_maxVo(?NotaMax $NotaMax = null): void
    {
        $this->nota_max = $NotaMax?->value();
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
        return $this->acta;
    }

    /**
     * @return Acta|null
     */
    public function getActaVo(): ?Acta
    {
        return Acta::fromNullable($this->acta);
    }

    /**
     * @deprecated usar setActaVo()
     */
    public function setActa(?string $acta = null): void
    {
        $this->acta = $acta;
    }

    /**
     * @param Acta|null $Acta
     */
    public function setActaVo(?Acta $Acta = null): void
    {
        $this->acta = $Acta?->value();
    }
}