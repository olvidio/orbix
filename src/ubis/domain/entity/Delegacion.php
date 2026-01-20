<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use src\ubis\domain\value_objects\{DelegacionCode,
    DelegacionGrupoEstudios,
    DelegacionId,
    DelegacionName,
    DelegacionRegionStgr,
    RegionCode};

class Delegacion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private DelegacionId $id_dl;

    private DelegacionCode $dl;

    private RegionCode $region;

    private ?DelegacionName $nombre_dl = null;

    private bool $active = true;

    private ?DelegacionGrupoEstudios $grupo_estudios = null;

    private ?DelegacionRegionStgr $region_stgr = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    // VO API
    public function getIdDlVo(): DelegacionId
    {
        return $this->id_dl;
    }

    public function setIdDlVo(DelegacionId|int $id_dl): void
    {
        $this->id_dl = $id_dl instanceof DelegacionId
            ? $id_dl
            : new DelegacionId($id_dl);
    }

    /**
     * @deprecated usar getIdDlVo()
     */
    public function getId_dl(): int
    {
        return $this->id_dl->value();
    }

    /**
     * @deprecated usar setIdDlVo()
     */
    public function setId_dl(int $id_dl): void
    {
        $this->id_dl = new DelegacionId($id_dl);
    }

    public function getDlVo(): DelegacionCode
    {
        return $this->dl;
    }

    public function setDlVo(DelegacionCode|string|null $dl): void
    {
        $this->dl = $dl instanceof DelegacionCode
            ? $dl
            : DelegacionCode::fromNullableString($dl);
    }

    /**
     * @deprecated usar getDlVo()
     */
    public function getDl(): string
    {
        return $this->dl->value();
    }

    /**
     * @deprecated usar setDlVo()
     */
    public function setDl(string $dl): void
    {
        $this->dl = new DelegacionCode($dl);
    }

    public function getRegionVo(): RegionCode
    {
        return $this->region;
    }

    public function setRegionVo(RegionCode|string|null $region): void
    {
        $this->region = $region instanceof RegionCode
            ? $region
            : RegionCode::fromNullableString($region);
    }

    /**
     * @deprecated usar getRegionVo()
     */
    public function getRegion(): string
    {
        return $this->region->value();
    }

    /**
     * @deprecated usar setRegionVo()
     */
    public function setRegion(string $region): void
    {
        $this->region = new RegionCode($region);
    }

    public function getNombreDlVo(): ?DelegacionName
    {
        return $this->nombre_dl;
    }

    public function setNombreDlVo(DelegacionName|string|null $texto = null): void
    {
        $this->nombre_dl = $texto instanceof DelegacionName
            ? $texto
            : DelegacionName::fromNullableString($texto);
    }

    /**
     * @deprecated  usar getNombreDlVo()
     */
    public function getNombre_dl(): ?string
    {
        return $this->nombre_dl?->value();
    }

    /**
     * @deprecated usar setNombreDlVo()
     */
    public function setNombre_dl(?string $nombre_dl = null): void
    {
        $this->nombre_dl = DelegacionName::fromNullableString($nombre_dl);
    }

    public function getGrupoEstudiosVo(): ?DelegacionGrupoEstudios
    {
        return $this->grupo_estudios;
    }

    public function setGrupoEstudiosVo(DelegacionGrupoEstudios|string|null $texto = null): void
    {
        $this->grupo_estudios = $texto instanceof DelegacionGrupoEstudios
            ? $texto
            : DelegacionGrupoEstudios::fromNullableString($texto);
    }

    public function getRegionStgrVo(): ?DelegacionRegionStgr
    {
        return $this->region_stgr;
    }

    public function setRegionStgrVo(DelegacionRegionStgr|string|null $texto = null): void
    {
        $this->region_stgr = $texto instanceof DelegacionRegionStgr
            ? $texto
            : DelegacionRegionStgr::fromNullableString($texto);
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

    /**
     * @deprecated usar getGrupoEstudiosVo()
     */
    public function getGrupo_estudios(): ?string
    {
        return $this->grupo_estudios?->value();
    }

    /**
     * @deprecated usar setGrupoEstudiosVo()
     */
    public function setGrupo_estudios(?string $grupo_estudios = null): void
    {
        $this->grupo_estudios = DelegacionGrupoEstudios::fromNullableString($grupo_estudios);
    }

    /**
     * @deprecated usar getRegionStgrVo()
     */
    public function getRegion_stgr(): ?string
    {
        return $this->region_stgr?->value();
    }

    /**
     * @deprecated usar setRegionStgrVo()
     */
    public function setRegion_stgr(?string $region_stgr = null): void
    {
        $this->region_stgr = DelegacionRegionStgr::fromNullableString($region_stgr);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_dl';
    }

    public function getDatosCampos(): array
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
