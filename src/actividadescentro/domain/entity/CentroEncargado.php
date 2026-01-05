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
    private int|null $num_orden = null;
    private string|null $encargo = null;

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
    public function getNum_orden(): ?int
    {
        return $this->num_orden;
    }

    /**
     * @deprecated use setNumOrdenVo()
     */
    public function setNum_orden(?int $num_orden = null): void
    {
        $this->num_orden = $num_orden;
    }

    /**
     * @return CentroEncargadoOrden|null
     */
    public function getNumOrdenVo(): ?CentroEncargadoOrden
    {
        return $this->num_orden !== null ? new CentroEncargadoOrden($this->num_orden) : null;
    }

    /**
     * @param CentroEncargadoOrden|null $oCentroEncargadoOrden
     */
    public function setNumOrdenVo(?CentroEncargadoOrden $oCentroEncargadoOrden = null): void
    {
        $this->num_orden = $oCentroEncargadoOrden?->value();
    }

    /**
     * @deprecated use getEncargoVo()
     */
    public function getEncargo(): ?string
    {
        return $this->encargo;
    }

    /**
     * @deprecated use setEncargoVo()
     */
    public function setEncargo(?string $encargo = null): void
    {
        $this->encargo = $encargo;
    }

    /**
     * @return CentroEncargadoTexto|null
     */
    public function getEncargoVo(): ?CentroEncargadoTexto
    {
        return $this->encargo !== null ? new CentroEncargadoTexto($this->encargo) : null;
    }

    /**
     * @param CentroEncargadoTexto|null $oCentroEncargadoTexto
     */
    public function setEncargoVo(?CentroEncargadoTexto $oCentroEncargadoTexto = null): void
    {
        $this->encargo = $oCentroEncargadoTexto?->value();
    }
}