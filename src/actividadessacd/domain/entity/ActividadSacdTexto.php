<?php

namespace src\actividadessacd\domain\entity;

use src\actividadessacd\domain\value_objects\SacdTextoClave;
use src\actividadessacd\domain\value_objects\SacdTextoTexto;
use src\encargossacd\domain\value_objects\LocaleCode;
use src\shared\domain\traits\Hydratable;

class ActividadSacdTexto
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;
    private LocaleCode $idioma;
    private SacdTextoClave $clave;
    private ?SacdTextoTexto $texto = null;

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
        return $this->idioma->value();
    }

    /**
     * @deprecated use setIdiomaVo()
     */
    public function setIdioma(string $idioma): void
    {
        $vo = LocaleCode::fromNullableString($idioma);
        if ($vo !== null) {
            $this->idioma = $vo;
        }
    }

    /**
     * @return LocaleCode
     */
    public function getIdiomaVo(): LocaleCode
    {
        return $this->idioma;
    }

    public function setIdiomaVo(LocaleCode|string $texto): void
    {
        if ($texto instanceof LocaleCode) {
            $this->idioma = $texto;
            return;
        }
        $vo = LocaleCode::fromNullableString($texto);
        if ($vo !== null) {
            $this->idioma = $vo;
        }
    }

    /**
     * @deprecated use getClaveVo()
     */
    public function getClave(): string
    {
        return $this->clave->value();
    }

    /**
     * @deprecated use setClaveVo()
     */
    public function setClave(string $clave): void
    {
        $vo = SacdTextoClave::fromNullableString($clave);
        if ($vo !== null) {
            $this->clave = $vo;
        }
    }

    /**
     * @return SacdTextoClave
     */
    public function getClaveVo(): SacdTextoClave
    {
        return $this->clave;
    }


    public function setClaveVo(SacdTextoClave|string $texto): void
    {
        if ($texto instanceof SacdTextoClave) {
            $this->clave = $texto;
            return;
        }
        $vo = SacdTextoClave::fromNullableString($texto);
        if ($vo !== null) {
            $this->clave = $vo;
        }
    }

    /**
     * @deprecated use getTextoVo()
     */
    public function getTexto(): ?string
    {
        return $this->texto?->value();
    }

    /**
     * @deprecated use setTextoVo()
     */
    public function setTexto(?string $texto = null): void
    {
        $this->texto = SacdTextoTexto::fromNullableString($texto);
    }

    /**
     * @return SacdTextoTexto|null
     */
    public function getTextoVo(): ?SacdTextoTexto
    {
        return $this->texto;
    }

    public function setTextoVo(SacdTextoTexto|string|null $texto = null): void
    {
        $this->texto = $texto instanceof SacdTextoTexto
            ? $texto
            : SacdTextoTexto::fromNullableString($texto);
    }
}