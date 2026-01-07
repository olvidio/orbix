<?php

namespace src\encargossacd\domain\entity;
use src\encargossacd\domain\value_objects\IdiomaCode;
use src\shared\domain\traits\Hydratable;

class EncargoTexto
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private IdiomaCode $idioma;

    private string $clave;

    private ?string $texto = null;

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


    public function getClave(): string
    {
        return $this->clave;
    }


    public function setClave(string $clave): void
    {
        $this->clave = $clave;
    }


    public function getTexto(): ?string
    {
        return $this->texto;
    }


    public function setTexto(?string $texto = null): void
    {
        $this->texto = $texto;
    }
}