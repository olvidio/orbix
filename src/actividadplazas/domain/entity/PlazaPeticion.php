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

    private PeticionOrden $orden;

    private ?PeticionTipo $tipo = null;

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
        return $this->orden;
    }


    public function setOrdenVo(PeticionOrden|int|null $valor): void
    {
        $this->orden = $valor instanceof PeticionOrden
            ? $valor
            : PeticionOrden::fromNullableInt($valor);
    }

    /**
     * @deprecated use getOrdenVo()
     */
    public function getOrden(): int
    {
        return $this->orden->value();
    }

    /**
     * @deprecated use setOrdenVo()
     */
    public function setOrden(int $orden): void
    {
        $this->orden = PeticionOrden::fromNullableInt($orden);
    }

    /**
     * @return PeticionTipo|null
     */
    public function getTipoVo(): ?PeticionTipo
    {
        return $this->tipo;
    }


    public function setTipoVo(PeticionTipo|string|null $texto = null): void
    {
        $this->tipo = $texto instanceof PeticionTipo
            ? $texto
            : PeticionTipo::fromNullableString($texto);
    }

    /**
     * @deprecated use getTipoPlazaPeticionVo()
     */
    public function getTipo(): ?string
    {
        return $this->tipo?->value();
    }

    /**
     * @deprecated use setTipoPlazaPeticionVo()
     */
    public function setTipo(?string $tipo = null): void
    {
        $this->tipo = PeticionTipo::fromNullableString($tipo);
    }
}