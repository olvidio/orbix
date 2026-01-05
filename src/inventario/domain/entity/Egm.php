<?php

namespace src\inventario\domain\entity;

use src\inventario\domain\value_objects\{EgmEquipajeId, EgmGrupoId, EgmItemId, EgmLugarId, EgmTexto};
use src\shared\domain\traits\Hydratable;

class Egm
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int|null $id_equipaje = null;

    private int|null $id_grupo = null;

    private int|null $id_lugar = null;

    private string|null $texto = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_equipaje(): ?int
    {
        return $this->id_equipaje;
    }


    public function setId_equipaje(?int $id_equipaje = null): void
    {
        $this->id_equipaje = $id_equipaje;
    }


    public function getId_grupo(): ?int
    {
        return $this->id_grupo;
    }


    public function setId_grupo(?int $id_grupo = null): void
    {
        $this->id_grupo = $id_grupo;
    }


    public function getId_lugar(): ?int
    {
        return $this->id_lugar;
    }


    public function setId_lugar(?int $id_lugar = null): void
    {
        $this->id_lugar = $id_lugar;
    }


    public function getTexto(): ?string
    {
        return $this->texto;
    }


    public function setTexto(?string $texto = null): void
    {
        $this->texto = $texto;
    }

    // Value Object API (duplicada con legacy)
    public function getIdItemVo(): EgmItemId
    {
        return new EgmItemId($this->id_item);
    }

    public function setIdItemVo(?EgmItemId $id = null): void
    {
        if ($id === null) {
            return;
        }
        $this->id_item = $id->value();
    }

    public function getIdEquipajeVo(): ?EgmEquipajeId
    {
        return $this->id_equipaje !== null ? new EgmEquipajeId($this->id_equipaje) : null;
    }

    public function setIdEquipajeVo(?EgmEquipajeId $id = null): void
    {
        $this->id_equipaje = $id?->value();
    }

    public function getIdGrupoVo(): ?EgmGrupoId
    {
        return $this->id_grupo !== null ? new EgmGrupoId($this->id_grupo) : null;
    }

    public function setIdGrupoVo(?EgmGrupoId $id = null): void
    {
        $this->id_grupo = $id?->value();
    }

    public function getIdLugarVo(): ?EgmLugarId
    {
        return $this->id_lugar !== null ? new EgmLugarId($this->id_lugar) : null;
    }

    public function setIdLugarVo(?EgmLugarId $id = null): void
    {
        $this->id_lugar = $id?->value();
    }

    public function getTextoVo(): ?EgmTexto
    {
        return EgmTexto::fromNullableString($this->texto);
    }

    public function setTextoVo(?EgmTexto $texto = null): void
    {
        $this->texto = $texto?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item';
    }
}