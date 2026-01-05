<?php

namespace src\personas\domain\entity;

use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

use src\personas\domain\value_objects\{CeNumber, CeLugarText};


class PersonaSSSC extends PersonaDl
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_auto;

    private int|null $ce = null;

    private int|null $ce_ini = null;

    private int|null $ce_fin = null;

    private string|null $ce_lugar = null;

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
     * @deprecated use getCeVo()
     */
    public function getCe(): ?int
    {
        return $this->ce;
    }

    /**
     * @deprecated use setCeVo()
     */
    public function setCe(?int $ce = null): void
    {
        $this->ce = $ce;
    }

    public function getCeVo(): ?CeNumber
    {
        return $this->ce !== null ? new CeNumber($this->ce) : null;
    }

    public function setCeVo(?CeNumber $ce = null): void
    {
        $this->ce = $ce?->value();
    }

    /**
     * @deprecated use getCeIniVo()
     */
    public function getCe_ini(): ?int
    {
        return $this->ce_ini;
    }

    /**
     * @deprecated use setCeIniVo()
     */
    public function setCe_ini(?int $ce_ini = null): void
    {
        $this->ce_ini = $ce_ini;
    }

    public function getCeIniVo(): ?CeNumber
    {
        return $this->ce_ini !== null ? new CeNumber($this->ce_ini) : null;
    }

    public function setCeIniVo(?CeNumber $ce = null): void
    {
        $this->ce_ini = $ce?->value();
    }

    /**
     * @deprecated use getCeFinVo()
     */
    public function getCe_fin(): ?int
    {
        return $this->ce_fin;
    }

    /**
     * @deprecated use setCeFinVo()
     */
    public function setCe_fin(?int $ce_fin = null): void
    {
        $this->ce_fin = $ce_fin;
    }

    public function getCeFinVo(): ?CeNumber
    {
        return $this->ce_fin !== null ? new CeNumber($this->ce_fin) : null;
    }

    public function setCeFinVo(?CeNumber $ce = null): void
    {
        $this->ce_fin = $ce?->value();
    }

    /**
     * @deprecated use getCeLugarVo()
     */
    public function getCe_lugar(): ?string
    {
        return $this->ce_lugar;
    }

    /**
     * @deprecated use setCeLugarVo()
     */
    public function setCe_lugar(?string $ce_lugar = null): void
    {
        $this->ce_lugar = $ce_lugar;
    }

    public function getCeLugarVo(): ?CeLugarText
    {
        return CeLugarText::fromNullableString($this->ce_lugar);
    }

    public function setCeLugarVo(?CeLugarText $lugar = null): void
    {
        $this->ce_lugar = $lugar?->value();
    }
}