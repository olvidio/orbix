<?php

namespace src\actividadessacd\domain\entity;

use src\actividadessacd\domain\value_objects\SacdTextoClave;
use src\actividadessacd\domain\value_objects\SacdTextoTexto;
use src\encargossacd\domain\value_objects\IdiomaCode;
use src\shared\domain\traits\Hydratable;

class ActividadSacdTexto
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;
    private string $idioma;
    private string $clave;
    private string|null $texto = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }

    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }

    /**
     * @deprecated use getIdiomaVo()
     */
    public function getIdioma(): string
    {
        return $this->idioma;
    }

    /**
     * @deprecated use setIdiomaVo()
     */
    public function setIdioma(string $sidioma): void
    {
        $this->idioma = $sidioma;
    }

    /**
     * @return IdiomaCode
     */
    public function getIdiomaVo(): IdiomaCode
    {
        return new IdiomaCode($this->idioma);
    }

    /**
     * @param IdiomaCode $oIdiomaCode
     */
    public function setIdiomaVo(IdiomaCode $oIdiomaCode): void
    {
        $this->idioma = $oIdiomaCode->value();
    }

    /**
     * @deprecated use getClaveVo()
     */
    public function getClave(): string
    {
        return $this->clave;
    }

    /**
     * @deprecated use setClaveVo()
     */
    public function setClave(string $sclave): void
    {
        $this->clave = $sclave;
    }

    /**
     * @return SacdTextoClave
     */
    public function getClaveVo(): SacdTextoClave
    {
        return new SacdTextoClave($this->clave);
    }

    /**
     * @param SacdTextoClave $oSacdTextoClave
     */
    public function setClaveVo(SacdTextoClave $oSacdTextoClave): void
    {
        $this->clave = $oSacdTextoClave->value();
    }

    /**
     * @deprecated use getTextoVo()
     */
    public function getTexto(): ?string
    {
        return $this->texto;
    }

    /**
     * @deprecated use setTextoVo()
     */
    public function setTexto(?string $stexto = null): void
    {
        $this->texto = $stexto;
    }

    /**
     * @return SacdTextoTexto|null
     */
    public function getTextoVo(): ?SacdTextoTexto
    {
        return SacdTextoTexto::fromNullableString($this->texto);
    }

    /**
     * @param SacdTextoTexto|null $oSacdTextoTexto
     */
    public function setTextoVo(?SacdTextoTexto $oSacdTextoTexto = null): void
    {
        $this->texto = $oSacdTextoTexto?->value();
    }
}