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

    private ?PresNombreText $pres_nom = null;

    private ?PresTelefonoText $pres_telf = null;

    private ?PresEmailText $pres_mail = null;

    private ?PresZonaText $zona = null;

    private ?PresObservText $observ = null;

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
        return $this->pres_nom;
    }

    /**
     * @param PresNombreText|null $oPresNombreText
     */
    public function setPresNomVo(PresNombreText|string|null $texto = null): void
    {
        $this->pres_nom = $texto instanceof PresNombreText
            ? $texto
            : PresNombreText::fromNullableString($texto);
    }

    /**
     * @deprecated use getPresNomVo()
     */
    public function getPres_nom(): ?string
    {
        return $this->pres_nom?->value();
    }

    /**
     * @deprecated use setPresNomVo()
     */
    public function setPres_nom(?string $pres_nom = null): void
    {
        $this->pres_nom = PresNombreText::fromNullableString($pres_nom);
    }

    /**
     * @return PresTelefonoText|null
     */
    public function getPresTelfVo(): ?PresTelefonoText
    {
        return $this->pres_telf;
    }

    /**
     * @param PresTelefonoText|null $oPresTelefonoText
     */
    public function setPresTelfVo(PresTelefonoText|string|null $texto = null): void
    {
        $this->pres_telf = $texto instanceof PresTelefonoText
            ? $texto
            : PresTelefonoText::fromNullableString($texto);
    }

    /**
     * @deprecated use getPresTelfVo()
     */
    public function getPres_telf(): ?string
    {
        return $this->pres_telf?->value();
    }

    /**
     * @deprecated use setPresTelfVo()
     */
    public function setPres_telf(?string $pres_telf = null): void
    {
        $this->pres_telf = PresTelefonoText::fromNullableString($pres_telf);
    }

    /**
     * @return PresEmailText|null
     */
    public function getPresMailVo(): ?PresEmailText
    {
        return $this->pres_mail;
    }

    /**
     * @param PresEmailText|null $oPresEmailText
     */
    public function setPresMailVo(PresEmailText|string|null $texto = null): void
    {
        $this->pres_mail = $texto instanceof PresEmailText
            ? $texto
            : PresEmailText::fromNullableString($texto);
    }

    /**
     *
     * @deprecated use getPresMailVo()
     */
    public function getPres_mail(): ?string
    {
        return $this->pres_mail?->value();
    }

    /**
     * @deprecated use setPresMailVo()
     */
    public function setPres_mail(?string $pres_mail = null): void
    {
        $this->pres_mail = PresEmailText::fromNullableString($pres_mail);
    }

    /**
     * @return PresZonaText|null
     */
    public function getZonaVo(): ?PresZonaText
    {
        return $this->zona;
    }

    /**
     * @param PresZonaText|null $oPresZonaText
     */
    public function setZonaVo(PresZonaText|string|null $texto = null): void
    {
        $this->zona = $texto instanceof PresZonaText
            ? $texto
            : PresZonaText::fromNullableString($texto);
    }

    /**
     * @deprecated use getZonaVo()
     */
    public function getZona(): ?string
    {
        return $this->zona?->value();
    }

    /**
     * @deprecated use setZonaVo()
     */
    public function setZona(?string $zona = null): void
    {
        $this->zona = PresZonaText::fromNullableString($zona);
    }

    /**
     * @return PresObservText|null
     */
    public function getObservVo(): ?PresObservText
    {
        return $this->observ;
    }

    /**
     * @param PresObservText|null $oPresObservText
     */
    public function setObservVo(PresObservText|string|null $texto = null): void
    {
        $this->observ = $texto instanceof PresObservText
            ? $texto
            : PresObservText::fromNullableString($texto);
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }

    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = PresObservText::fromNullableString($observ);
    }
}