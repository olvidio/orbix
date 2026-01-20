<?php

namespace src\zonassacd\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\zonassacd\domain\value_objects\NombreGrupoZona;


class ZonaGrupo
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_grupo;

    private ?NombreGrupoZona $nombre_grupo = null;

    private ?int $orden = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_grupo(): int
    {
        return $this->id_grupo;
    }


    public function setId_grupo(int $id_grupo): void
    {
        $this->id_grupo = $id_grupo;
    }


    public function getNombreGrupoVo(): ?NombreGrupoZona
    {
        return $this->nombre_grupo;
    }


    public function setNombreGrupoVo(NombreGrupoZona|string|null $oNombreGrupoZona = null): void
    {
        $this->nombre_grupo = $oNombreGrupoZona instanceof NombreGrupoZona
            ? $oNombreGrupoZona
            : NombreGrupoZona::fromNullableString($oNombreGrupoZona);
    }

    /**
     * @deprecated use getNombreGrupoVo()
     */
    public function getNombre_grupo(): ?string
    {
        return $this->nombre_grupo?->value();
    }

    /**
     * @deprecated use setNombreGrupoVo()
     */
    public function setNombre_grupo(?string $nombre_grupo = null): void
    {
        $this->nombre_grupo = NombreGrupoZona::fromNullableString($nombre_grupo);
    }


    public function getOrden(): ?int
    {
        return $this->orden;
    }


    public function setOrden(?int $orden = null): void
    {
        $this->orden = $orden;
    }
}