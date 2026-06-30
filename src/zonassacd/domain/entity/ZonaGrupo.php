<?php

namespace src\zonassacd\domain\entity;

use src\shared\domain\DatosCampo;
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

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_grupo';
    }

    /**
     * @return list<DatosCampo>
     */
    public function getDatosCampos(): array
    {
        return [
            $this->getDatosNombre_grupo(),
            $this->getDatosOrden(),
        ];
    }

    /**
     * Recupera las propiedades del atributo nombre_zona de Zona
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombre_grupo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_grupo');
        $oDatosCampo->setMetodoGet('getNombre_grupo');
        $oDatosCampo->setMetodoSet('setNombre_grupo');
        $oDatosCampo->setEtiqueta(_("nombre grupo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo orden de Zona
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosOrden(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('orden');
        $oDatosCampo->setMetodoGet('getOrden');
        $oDatosCampo->setMetodoSet('setOrden');
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('5');
        return $oDatosCampo;
    }

}