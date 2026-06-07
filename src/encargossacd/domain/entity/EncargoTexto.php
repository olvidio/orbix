<?php

namespace src\encargossacd\domain\entity;
use src\encargossacd\domain\value_objects\EncargoText;
use src\encargossacd\domain\value_objects\EncargoTextClave;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\LocaleCode;

class EncargoTexto
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private LocaleCode $idioma;

    private EncargoTextClave $clave;

    private ?EncargoText $texto = null;

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
     * @deprecated usar getIdiomaVo()
     */
    public function getIdioma(): string
    {
        return $this->idioma->value();
    }

    /**
     * @deprecated usar setIdiomaVo()
     */
    public function setIdioma(string $idioma): void
    {
        $this->idioma = (LocaleCode::fromNullableString($idioma) ?? throw new \InvalidArgumentException('idioma cannot be empty'));
    }

    public function getIdiomaVo(): LocaleCode
    {
        return $this->idioma;
    }

    public function setIdiomaVo(LocaleCode|string|null $vo): void
    {
        $this->idioma = $vo instanceof LocaleCode
            ? $vo
            : (LocaleCode::fromNullableString(is_string($vo) ? $vo : null) ?? throw new \InvalidArgumentException('idioma cannot be empty'));
    }

    /**
     * @deprecated usar getClaveVo()
     */
    public function getClave(): string
    {
        return $this->clave->value();
    }
    public function getClaveVo(): EncargoTextClave
    {
        return $this->clave;
    }

    /**
     * @deprecated usar setClaveVo()
     */
    public function setClave(string $clave): void
    {
        $this->clave = (EncargoTextClave::fromNullableString($clave) ?? throw new \InvalidArgumentException('clave cannot be empty'));
    }
    public function setClaveVo(EncargoTextClave|string|null $vo): void
    {
        $this->clave = $vo instanceof EncargoTextClave
            ? $vo
            : (EncargoTextClave::fromNullableString(is_string($vo) ? $vo : null) ?? throw new \InvalidArgumentException('clave cannot be empty'));
    }

    /**
     * @deprecated usar getTextoVo()
     */
    public function getTexto(): ?string
    {
        return $this->texto?->value();
    }
    public function getTextoVo(): ?EncargoText
    {
        return $this->texto;
    }

    /**
     * @deprecated usar setTextoVo()
     */
    public function setTexto(?string $texto = null): void
    {
        $this->texto = EncargoText::fromNullableString($texto);
    }
    public function setTextoVo(EncargoText|string|null $texto = null): void
    {
        $this->texto = $texto instanceof EncargoText
            ? $texto
            : EncargoText::fromNullableString($texto);
    }
}