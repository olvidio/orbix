<?php

namespace src\encargossacd\domain\entity;
use src\encargossacd\domain\value_objects\EncargoText;
use src\encargossacd\domain\value_objects\EncargoTextClave;
use src\encargossacd\domain\value_objects\IdiomaCode;
use src\shared\domain\traits\Hydratable;

class EncargoTexto
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private IdiomaCode $idioma;

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
        $this->idioma = IdiomaCode::fromNullableString($idioma);
    }

    public function getIdiomaVo(): IdiomaCode
    {
        return $this->idioma;
    }

    public function setIdiomaVo(IdiomaCode|string|null $vo): void
    {
        $this->idioma = $vo instanceof IdiomaCode
            ? $vo
            : IdiomaCode::fromNullableString($vo);
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
        $this->clave = EncargoTextClave::fromNullableString($clave);
    }
    public function setClaveVo(EncargoTextClave|string|null $vo): void
    {
        $this->clave = $vo instanceof EncargoTextClave
            ? $vo
            : EncargoTextClave::fromNullableString($vo);
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
        $this->texto = $texto;
    }
    public function setTextoVo(EncargoText|string|null $texto = null): void
    {
        $this->texto = $texto instanceof EncargoText
            ? $texto
            : EncargoText::fromNullableString($texto);
    }
}