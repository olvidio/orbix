<?php

namespace src\actividadplazas\domain\entity;

use src\actividadplazas\domain\value_objects\PeticionOrden;
use src\actividadplazas\domain\value_objects\PeticionTipo;
use src\shared\domain\traits\Hydratable;

class PlazaPeticion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_nom;

    private int $id_activ;

    private int $orden;

    private string|null $tipo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }


    public function getId_activ(): int
    {
        return $this->id_activ;
    }


    public function setId_activ(int $id_activ): void
    {
        $this->id_activ = $id_activ;
    }

    /**
     * @return PeticionOrden
     */
    public function getOrdenVo(): PeticionOrden
    {
        return new PeticionOrden($this->orden);
    }

    /**
     * @param PeticionOrden $oPeticionOrden
     */
    public function setOrdenVo(PeticionOrden $oPeticionOrden): void
    {
        $this->orden = $oPeticionOrden->value();
    }

    /**
     * @deprecated use getOrdenVo()
     */
    public function getOrden(): int
    {
        return $this->orden;
    }

    /**
     * @deprecated use setOrdenVo()
     */
    public function setOrden(int $orden): void
    {
        $this->orden = $orden;
    }

    /**
     * @return PeticionTipo|null
     */
    public function getTipoVo(): ?PeticionTipo
    {
        return $this->tipo !== null ? new PeticionTipo($this->tipo) : null;
    }

    /**
     * @param PeticionTipo|null $oPeticionTipo
     */
    public function setTipoVo(?PeticionTipo $oPeticionTipo = null): void
    {
        $this->tipo = $oPeticionTipo?->value();
    }

    /**
     * @deprecated use getTipoVo()
     */
    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    /**
     * @deprecated use setTipoVo()
     */
    public function setTipo(?string $tipo = null): void
    {
        $this->tipo = $tipo;
    }
}