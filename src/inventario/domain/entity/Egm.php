<?php

namespace src\inventario\domain\entity;

use src\inventario\domain\value_objects\{EgmEquipajeId, EgmGrupoId, EgmItemId, EgmLugarId, EgmTexto};
use src\shared\domain\traits\Hydratable;

class Egm
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private ?EgmEquipajeId $id_equipaje = null;

    private ?EgmGrupoId $id_grupo = null;

    private ?EgmLugarId $id_lugar = null;

    private ?EgmTexto $texto = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }

    public function setId_item(?int $id = null): void
    {
        $this->id_item = $id;
    }

    public function getId_equipaje(): ?string
    {
        return $this->id_equipaje?->value();
    }


    public function setId_equipaje(?int $id_equipaje = null): void
    {
        $this->id_equipaje = EgmEquipajeId::fromNullableInt($id_equipaje);
    }


    public function getId_grupo(): ?string
    {
        return $this->id_grupo?->value();
    }


    public function setId_grupo(?int $id_grupo = null): void
    {
        $this->id_grupo = EgmGrupoId::fromNullableInt($id_grupo);
    }


    public function getId_lugar(): ?string
    {
        return $this->id_lugar?->value();
    }


    public function setId_lugar(?int $id_lugar = null): void
    {
        $this->id_lugar = EgmLugarId::fromNullableInt($id_lugar);
    }


    public function getTexto(): ?string
    {
        return $this->texto?->value();
    }


    public function setTexto(?string $texto = null): void
    {
        $this->texto = EgmTexto::fromNullableString($texto);
    }

    // Value Object API (duplicada con legacy)
    public function getIdEquipajeVo(): ?EgmEquipajeId
    {
        return $this->id_equipaje;
    }

    public function setIdEquipajeVo(EgmEquipajeId|int|null $id = null): void
    {
        $this->id_equipaje = $id instanceof EgmEquipajeId
            ? $id
            : EgmEquipajeId::fromNullableInt($id);
    }

    public function getIdGrupoVo(): ?EgmGrupoId
    {
        return $this->id_grupo;
    }

    public function setIdGrupoVo(EgmGrupoId|int|null $id = null): void
    {
        $this->id_grupo = $id instanceof EgmGrupoId
            ? $id
            : EgmGrupoId::fromNullableInt($id);
    }

    public function getIdLugarVo(): ?EgmLugarId
    {
        return $this->id_lugar;
    }

    public function setIdLugarVo(EgmLugarId|int|null $id = null): void
    {
        $this->id_lugar = $id instanceof EgmLugarId
            ? $id
            : EgmLugarId::fromNullableInt($id);
    }

    public function getTextoVo(): ?EgmTexto
    {
        return $this->texto;
    }

    public function setTextoVo(EgmTexto|string|null $texto = null): void
    {
        $this->texto = $texto instanceof EgmTexto
            ? $texto
            : EgmTexto::fromNullableString($texto);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_item';
    }
}