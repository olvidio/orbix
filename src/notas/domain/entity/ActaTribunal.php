<?php

namespace src\notas\domain\entity;

use src\notas\domain\value_objects\Examinador;
use src\notas\domain\value_objects\Orden;
use src\shared\domain\traits\Hydratable;


class ActaTribunal
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private string $acta;

    private string|null $examinador = null;

    private int|null $orden = null;

    private int $id_item;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getActa(): string
    {
        return $this->acta;
    }

    public function setActa(string $acta): void
    {
        $this->acta = $acta;
    }


    public function getExaminadorVo(): ?Examinador
    {
        return Examinador::fromNullable($this->examinador);
    }


    public function setExaminadorVo(?Examinador $oExaminador): void
    {
        $this->examinador = $oExaminador?->value();
    }

    /**
     * @deprecated use getExaminadorVo()
     */
    public function getExaminador(): ?string
    {
        return $this->examinador;
    }

    /**
     * @deprecated use setExaminadorVo()
     */
    public function setExaminador(?string $examinador = null): void
    {
        $this->examinador = $examinador;
    }

    public function getOrdenVo(): ?Orden
    {
        return Orden::fromNullable($this->orden);
    }

    public function setOrdenVo(?Orden $oOrden): void
    {
        $this->orden = $oOrden?->value();
    }

    /**
     * @deprecated use getOrdenVo()
     */
    public function getOrden(): ?int
    {
        return $this->orden;
    }

    /**
     * @deprecated use setOrdenVo()
     */
    public function setOrden(?int $orden = null): void
    {
        $this->orden = $orden;
    }


    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }
}