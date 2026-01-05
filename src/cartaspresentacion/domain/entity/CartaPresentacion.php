<?php

namespace src\cartaspresentacion\domain\entity;
use src\cartaspresentacion\domain\value_objects\PresEmailText;
use src\cartaspresentacion\domain\value_objects\PresNombreText;
use src\cartaspresentacion\domain\value_objects\PresObservText;
use src\cartaspresentacion\domain\value_objects\PresTelefonoText;
use src\cartaspresentacion\domain\value_objects\PresZonaText;
use src\shared\domain\traits\Hydratable;

class CartaPresentacion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_direccion;

    private int $id_ubi;

    private string|null $pres_nom = null;

    private string|null $pres_telf = null;

    private string|null $pres_mail = null;

    private string|null $zona = null;

    private string|null $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_direccion(): int
    {
        return $this->id_direccion;
    }


    public function setId_direccion(int $id_direccion): void
    {
        $this->id_direccion = $id_direccion;
    }


    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }

    /**
     * @return PresNombreText|null
     */
    public function getPresNomVo(): ?PresNombreText
    {
        return PresNombreText::fromNullableString($this->pres_nom);
    }

    /**
     * @param PresNombreText|null $oPresNombreText
     */
    public function setPresNomVo(?PresNombreText $oPresNombreText = null): void
    {
        $this->pres_nom = $oPresNombreText?->value();
    }

    /**
     * @deprecated use getPresNomVo()
     */
    public function getPres_nom(): ?string
    {
        return $this->pres_nom;
    }

    /**
     * @deprecated use setPresNomVo()
     */
    public function setPres_nom(?string $pres_nom = null): void
    {
        $this->pres_nom = $pres_nom;
    }

    /**
     * @return PresTelefonoText|null
     */
    public function getPresTelfVo(): ?PresTelefonoText
    {
        return PresTelefonoText::fromNullableString($this->pres_telf);
    }

    /**
     * @param PresTelefonoText|null $oPresTelefonoText
     */
    public function setPresTelfVo(?PresTelefonoText $oPresTelefonoText = null): void
    {
        $this->pres_telf = $oPresTelefonoText?->value();
    }

    /**
     * @deprecated use getPresTelfVo()
     */
    public function getPres_telf(): ?string
    {
        return $this->pres_telf;
    }

    /**
     * @deprecated use setPresTelfVo()
     */
    public function setPres_telf(?string $pres_telf = null): void
    {
        $this->pres_telf = $pres_telf;
    }

    /**
     * @return PresEmailText|null
     */
    public function getPresMailVo(): ?PresEmailText
    {
        return PresEmailText::fromNullableString($this->pres_mail);
    }

    /**
     * @param PresEmailText|null $oPresEmailText
     */
    public function setPresMailVo(?PresEmailText $oPresEmailText = null): void
    {
        $this->pres_mail = $oPresEmailText?->value();
    }

    /**
     *
     * @deprecated use getPresMailVo()
     */
    public function getPres_mail(): ?string
    {
        return $this->pres_mail;
    }

    /**
     * @deprecated use setPresMailVo()
     */
    public function setPres_mail(?string $pres_mail = null): void
    {
        $this->pres_mail = $pres_mail;
    }

    /**
     * @return PresZonaText|null
     */
    public function getZonaVo(): ?PresZonaText
    {
        return PresZonaText::fromNullableString($this->zona);
    }

    /**
     * @param PresZonaText|null $oPresZonaText
     */
    public function setZonaVo(?PresZonaText $oPresZonaText = null): void
    {
        $this->zona = $oPresZonaText?->value();
    }

    /**
     * @deprecated use getZonaVo()
     */
    public function getZona(): ?string
    {
        return $this->zona;
    }

    /**
     * @deprecated use setZonaVo()
     */
    public function setZona(?string $zona = null): void
    {
        $this->zona = $zona;
    }

    /**
     * @return PresObservText|null
     */
    public function getObservVo(): ?PresObservText
    {
        return PresObservText::fromNullableString($this->observ);
    }

    /**
     * @param PresObservText|null $oPresObservText
     */
    public function setObservVo(?PresObservText $oPresObservText = null): void
    {
        $this->observ = $oPresObservText?->value();
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }
}