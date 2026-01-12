<?php

namespace src\personas\domain\entity;

use src\personas\domain\value_objects\{CeCurso, CeLugarText, CeNumber};

class PersonaN extends PersonaDl
{

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_auto;

    private ?CeCurso $ce = null;

    private ?CeNumber $ce_ini = null;

    private ?CeNumber $ce_fin = null;

    private ?CeLugarText $ce_lugar = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_auto(): int
    {
        return $this->id_auto;
    }


    public function setId_auto(int $id_auto): void
    {
        $this->id_auto = $id_auto;
    }


    /**
     * @deprecated use getCeVo() instead
     */
    public function getCe(): ?string
    {
        return $this->ce?->value();
    }


    /**
     * @deprecated use setCeVo() instead
     */
    public function setCe(?int $ce = null): void
    {
        $this->ce = CeCurso::fromNullableInt($ce);
    }

    public function getCeVo(): ?CeCurso
    {
        return $this->ce;
    }

    public function setCeVo(CeCurso|int|null $ce = null): void
    {
        $this->ce = $ce instanceof CeCurso
            ? $ce
            : CeCurso::fromNullableInt($ce);
    }

    /**
     * @deprecated use getCeIniVo() instead
     */
    public function getCe_ini(): ?string
    {
        return $this->ce_ini?->value();
    }

    public function getCeIniVo(): ?CeNumber
    {
        return $this->ce_ini;
    }

    /**
     * @deprecated use setCeIniVo() instead
     */
    public function setCe_ini(?int $ce_ini = null): void
    {
        $this->ce_ini = CeNumber::fromNullableInt($ce_ini);
    }

    public function setCeIniVo(CeNumber|int|null $ce = null): void
    {
        $this->ce_ini = $ce instanceof CeNumber
            ? $ce
            : CeNumber::fromNullableInt($ce);
    }

    /**
     * @deprecated use getCeFinVo() instead
     */
    public function getCe_fin(): ?string
    {
        return $this->ce_fin?->value();
    }

    public function getCeFinVo(): ?CeNumber
    {
        return $this->ce_fin;
    }

    /**
     * @deprecated use setCeFinVo() instead
     */
    public function setCe_fin(?int $ce_fin = null): void
    {
        $this->ce_fin = CeNumber::fromNullableInt($ce_fin);
    }

    public function setCeFinVo(CeNumber|int|null $ce = null): void
    {
        $this->ce_fin = $ce instanceof CeNumber
            ? $ce
            : CeNumber::fromNullableInt($ce);
    }

    /**
     * @deprecated use getCeLugarVo() instead
     */
    public function getCe_lugar(): ?string
    {
        return $this->ce_lugar?->value();
    }

    public function getCeLugarVo(): ?CeLugarText
    {
        return $this->ce_lugar;
    }

    /**
     * @deprecated use setCeLugarVo() instead
     */
    public function setCe_lugar(?string $ce_lugar = null): void
    {
        $this->ce_lugar = CeLugarText::fromNullableString($ce_lugar);
    }

    public function setCeLugarVo(CeLugarText|string|null $lugar = null): void
    {
        $this->ce_lugar = $lugar instanceof CeLugarText
            ? $lugar
            : CeLugarText::fromNullableString($lugar);
    }
}
