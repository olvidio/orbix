<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use function core\is_true;
use src\ubis\domain\value_objects\{DelegacionId, DelegacionCode, RegionCode, DelegacionName, DelegacionGrupoEstudios, DelegacionRegionStgr};

class Delegacion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_dl de Delegacion
     */
    private DelegacionId $id_dl;
    /**
     * Dl de Delegacion (sigla)
     */
    private DelegacionCode $dl;
    /**
     * Region de Delegacion (código)
     */
    private RegionCode $region;
    /**
     * Nombre de la Delegacion
     */
    private ?DelegacionName $nombre_dl = null;
    /**
     * active de Delegacion
     */
    private bool $active = true;
    /**
     * Grupo de estudios
     */
    private ?DelegacionGrupoEstudios $grupo_estudios = null;
    /**
     * Región STGR
     */
    private ?DelegacionRegionStgr $region_stgr = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getIdDlVo(): DelegacionId
    {
        return $this->id_dl;
    }

    public function setIdDlVo(DelegacionId $id_dl): void
    {
        $this->id_dl = $id_dl;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getId_dl(): int
    {
        return $this->id_dl?->value();
    }

    public function setId_dl(int $id_dl): void
    {
        $this->id_dl = new DelegacionId($id_dl);
    }


    // VO API
    public function getDlVo(): DelegacionCode
    {
        return $this->dl;
    }

    public function setDlVo(DelegacionCode $dl): void
    {
        $this->dl = $dl;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getDl(): string
    {
        return $this->dl?->value();
    }

    public function setDl(string $dl): void
    {
        $this->dl = new DelegacionCode($dl);
    }

    // VO API
    public function getRegionVo(): RegionCode
    {
        return $this->region;
    }

    public function setRegionVo(RegionCode $region): void
    {
        $this->region = $region;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getRegion(): string
    {
        return $this->region?->value();
    }

    public function setRegion(string $region): void
    {
        $this->region = new RegionCode($region);
    }

    // VO API
    public function getNombreDlVo(): ?DelegacionName
    {
        return $this->nombre_dl;
    }

    public function setNombreDlVo(?DelegacionName $nombre_dl = null): void
    {
        $this->nombre_dl = $nombre_dl;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getNombre_dl(): ?string
    {
        return $this->nombre_dl?->value();
    }

    public function setNombre_dl(?string $nombre_dl = null): void
    {
        $this->nombre_dl = DelegacionName::fromNullableString($nombre_dl);
    }

    // VO API
    public function getActiveVo(): bool
    {
        return $this->active;
    }

    public function setActiveVo(bool $active = true): void
    {
        $this->active = $active;
    }

    public function getGrupoEstudiosVo(): ?DelegacionGrupoEstudios
    {
        return $this->grupo_estudios;
    }

    public function setGrupoEstudiosVo(?DelegacionGrupoEstudios $grupo_estudios = null): void
    {
        $this->grupo_estudios = $grupo_estudios;
    }

    public function getRegionStgrVo(): ?DelegacionRegionStgr
    {
        return $this->region_stgr;
    }

    public function setRegionStgrVo(?DelegacionRegionStgr $region_stgr = null): void
    {
        $this->region_stgr = $region_stgr;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active = true): void
    {
        $this->active = $active;
    }

    public function getGrupo_estudios(): ?string
    {
        return $this->grupo_estudios?->value();
    }

    public function setGrupo_estudios(?string $grupo_estudios = null): void
    {
        $this->grupo_estudios = DelegacionGrupoEstudios::fromNullableString($grupo_estudios);
    }

    public function getRegion_stgr(): ?string
    {
        return $this->region_stgr?->value();
    }

    public function setRegion_stgr(?string $region_stgr = null): void
    {
        $this->region_stgr = DelegacionRegionStgr::fromNullableString($region_stgr);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_dl';
    }

    public function getDatosCampos():array
    {
        $oDelegacionSet = new Set();

        //$oDelegacionSet->add($this->getDatosId_dl());
        $oDelegacionSet->add($this->getDatosRegion());
        $oDelegacionSet->add($this->getDatosDl());
        $oDelegacionSet->add($this->getDatosNombre_dl());
        $oDelegacionSet->add($this->getDatosGrupo_estudios());
        $oDelegacionSet->add($this->getDatosRegion_stgr());
        $oDelegacionSet->add($this->getDatosactive());
        return $oDelegacionSet->getTot();
    }

    /**
     * DatosCampo for campo 'dl'
     * @return DatosCampo
     */
    private function getDatosDl(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('dl');
        $oDatosCampo->setMetodoGet('getDl');
        $oDatosCampo->setMetodoSet('setDl');
        $oDatosCampo->setEtiqueta(_("sigla"));
        $oDatosCampo->setTipo('texto');
        return $oDatosCampo;
    }

    /**
     * DatosCampo for campo 'nombre_dl'
     * @return DatosCampo
     */
    private function getDatosNombre_dl(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_dl');
        $oDatosCampo->setMetodoGet('getNombre_dl');
        $oDatosCampo->setMetodoSet('setNombre_dl');
        $oDatosCampo->setEtiqueta(_("nombre de la delegación"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * DatosCampo for campo 'region'
     * @return DatosCampo
     */
    private function getDatosRegion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('region');
        $oDatosCampo->setMetodoGet('getRegion');
        $oDatosCampo->setMetodoSet('setRegion');
        $oDatosCampo->setEtiqueta(_("nombre de la región"));
        $oDatosCampo->setTipo('texto');
        return $oDatosCampo;
    }

    /**
     * DatosCampo for campo 'grupo_estudios'
     * @return DatosCampo
     */
    private function getDatosGrupo_estudios(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('grupo_estudios');
        $oDatosCampo->setMetodoGet('getGrupo_estudios');
        $oDatosCampo->setMetodoSet('setGrupo_estudios');
        $oDatosCampo->setEtiqueta(_("grupo del stgr"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(3);
        return $oDatosCampo;
    }

    /**
     * DatosCampo for campo 'region_stgr'
     * @return DatosCampo
     */
    private function getDatosRegion_stgr(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('region_stgr');
        $oDatosCampo->setMetodoGet('getRegion_stgr');
        $oDatosCampo->setMetodoSet('setRegion_stgr');
        $oDatosCampo->setEtiqueta(_("región del stgr"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    /**
     * DatosCampo for campo 'active'
     * @return DatosCampo
     */
    private function getDatosActive(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('active');
        $oDatosCampo->setMetodoGet('isActive');
        $oDatosCampo->setMetodoSet('setActive');
        $oDatosCampo->setEtiqueta(_("en activo"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}
