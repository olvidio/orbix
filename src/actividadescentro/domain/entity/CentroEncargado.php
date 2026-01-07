<?php

namespace src\actividadescentro\domain\entity;

use src\actividadescentro\domain\value_objects\CentroEncargadoOrden;
use src\actividadescentro\domain\value_objects\CentroEncargadoTexto;
use src\shared\domain\traits\Hydratable;

class CentroEncargado
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_activ;
    private int $id_ubi;
    private ?CentroEncargadoOrden $num_orden = null;
    private ?CentroEncargadoTexto $encargo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_activ(): int
    {
        return $this->id_activ;
    }

    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
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
     * @deprecated use getNumOrdenVo()
     */
    public function getNum_orden(): ?string
    {
        return $this->num_orden?->value();
    }

    /**
     * @deprecated use setNumOrdenVo()
     */
    public function setNum_orden(?int $num_orden = null): void
    {
        $this->num_orden = CentroEncargadoOrden::fromNullable($num_orden);
    }

    /**
     * @return CentroEncargadoOrden|null
     */
    public function getNumOrdenVo(): ?CentroEncargadoOrden
    {
        return $this->num_orden;
    }

    public function setNumOrdenVo(CentroEncargadoOrden|int|null $valor = null): void
    {
        $this->num_orden = $valor instanceof CentroEncargadoOrden
            ? $valor
            : CentroEncargadoOrden::fromNullable($valor);
    }

    /**
     * @deprecated use getEncargoVo()
     */
    public function getEncargo(): ?string
    {
        return $this->encargo?->value();
    }

    /**
     * @deprecated use setEncargoVo()
     */
    public function setEncargo(?string $encargo = null): void
    {
        $this->encargo = CentroEncargadoTexto::fromNullableString($encargo);
    }

    /**
     * @return CentroEncargadoTexto|null
     */
    public function getEncargoVo(): ?CentroEncargadoTexto
    {
        return $this->encargo;
    }

    public function setEncargoVo(CentroEncargadoTexto|string|null $texto = null): void
    {
        $this->encargo = $texto instanceof CentroEncargadoTexto
            ? $texto
            : CentroEncargadoTexto::fromNullableString($texto);
    }
}