<?php

namespace src\encargossacd\domain\entity;

use src\encargossacd\domain\value_objects\ObservText;
use src\shared\domain\traits\Hydratable;

class EncargoSacdObserv
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_nom;

    private ?ObservText $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    /**
     * @deprecated Usar `getObservVo(): ?ObservText` en su lugar.
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }

    /**
     * @deprecated Usar `setObservVo(?ObservText $vo): void` en su lugar.
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = ObservText::fromNullableString($observ);
    }

    public function getObservVo(): ?ObservText
    {
        return $this->observ;
    }

    public function setObservVo(ObservText|string|null $vo): void
    {
        $this->observ = $vo instanceof ObservText
            ? $vo
            : ObservText::fromNullableString($vo);
    }
}