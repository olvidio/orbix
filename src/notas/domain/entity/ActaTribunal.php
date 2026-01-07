<?php

namespace src\notas\domain\entity;

use src\notas\domain\value_objects\ActaNumero;
use src\notas\domain\value_objects\Examinador;
use src\notas\domain\value_objects\Orden;
use src\shared\domain\traits\Hydratable;


class ActaTribunal
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private ActaNumero $acta;

    private ?Examinador $examinador = null;

    private ?Orden $orden = null;

    private int $id_item;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated use getActaVo()
     */
    public function getActa(): string
    {
        return $this->acta->value();
    }

    public function getActaVo(): ActaNumero
    {
        return $this->acta;
    }

    /**
     * @deprecated use setActaVo()
     */
    public function setActa(string $acta): void
    {
        $this->acta = ActaNumero::fromNullableString($acta);
    }

    public function setActaVo(ActaNumero|string|null $texto = null): void
    {
        $this->acta = $texto instanceof ActaNumero
            ? $texto
            : ActaNumero::fromNullableString($texto);
    }

    public function getExaminadorVo(): ?Examinador
    {
        return $this->examinador;
    }


    public function setExaminadorVo(Examinador|string|null $texto = null): void
    {
        $this->examinador = $texto instanceof Examinador
            ? $texto
            : Examinador::fromNullableString($texto);
    }

    /**
     * @deprecated use getExaminadorVo()
     */
    public function getExaminador(): ?string
    {
        return $this->examinador?->value();
    }

    /**
     * @deprecated use setExaminadorVo()
     */
    public function setExaminador(?string $examinador = null): void
    {
        $this->examinador = Examinador::fromNullableString($examinador);
    }

    public function getOrdenVo(): ?Orden
    {
        return $this->orden;
    }

    public function setOrdenVo(Orden|int|null $valor = null): void
    {
        $this->orden = $valor instanceof Orden
            ? $valor
            : Orden::fromNullable($valor);
    }

    /**
     * @deprecated use getOrdenVo()
     */
    public function getOrden(): ?string
    {
        return $this->orden?->value();
    }

    /**
     * @deprecated use setOrdenVo()
     */
    public function setOrden(?int $orden = null): void
    {
        $this->orden = Orden::fromNullable($orden);
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