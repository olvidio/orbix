<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;
use src\ubis\domain\value_objects\{DelegacionId, DelegacionCode, RegionCode, DelegacionName, DelegacionStatus, DelegacionGrupoEstudios, DelegacionRegionStgr};

/**
 * Clase que implementa la entidad xu_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
class Delegacion
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_dl de Delegacion
     */
    private ?DelegacionId $idDl = null;
    /**
     * Dl de Delegacion (sigla)
     */
    private ?DelegacionCode $dl = null;
    /**
     * Region de Delegacion (código)
     */
    private ?RegionCode $region = null;
    /**
     * Nombre de la Delegacion
     */
    private ?DelegacionName $nombreDl = null;
    /**
     * Status de Delegacion
     */
    private ?DelegacionStatus $status = null;
    /**
     * Grupo de estudios
     */
    private ?DelegacionGrupoEstudios $grupoEstudios = null;
    /**
     * Región STGR
     */
    private ?DelegacionRegionStgr $regionStgr = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Delegacion
     */
    public function setAllAttributes(array $aDatos): Delegacion
    {
        if (array_key_exists('id_dl', $aDatos)) {
            $this->setIdDlVo(isset($aDatos['id_dl']) ? new DelegacionId((int)$aDatos['id_dl']) : null);
        }
        if (array_key_exists('dl', $aDatos)) {
            $this->setDlVo(isset($aDatos['dl']) && $aDatos['dl'] !== '' ? new DelegacionCode((string)$aDatos['dl']) : null);
        }
        if (array_key_exists('region', $aDatos)) {
            $this->setRegionVo(isset($aDatos['region']) && $aDatos['region'] !== '' ? new RegionCode((string)$aDatos['region']) : null);
        }
        if (array_key_exists('nombre_dl', $aDatos)) {
            $this->setNombreDlVo(DelegacionName::fromNullableString($aDatos['nombre_dl'] ?? null));
        }
        if (array_key_exists('status', $aDatos)) {
            $this->setStatusVo(isset($aDatos['status']) ? DelegacionStatus::fromScalar($aDatos['status']) : null);
        }
        if (array_key_exists('grupo_estudios', $aDatos)) {
            $this->setGrupoEstudiosVo(DelegacionGrupoEstudios::fromNullableString($aDatos['grupo_estudios'] ?? null));
        }
        if (array_key_exists('region_stgr', $aDatos)) {
            $this->setRegionStgrVo(DelegacionRegionStgr::fromNullableString($aDatos['region_stgr'] ?? null));
        }
        return $this;
    }

    // VO API
    public function getIdDlVo(): ?DelegacionId
    {
        return $this->idDl;
    }

    public function setIdDlVo(?DelegacionId $iid_dl = null): void
    {
        $this->idDl = $iid_dl;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getId_dl(): ?int
    {
        return $this->idDl?->value();
    }

    public function setId_dl(?int $iid_dl = null): void
    {
        $this->idDl = $iid_dl !== null ? new DelegacionId($iid_dl) : null;
    }

    /**
     *
     * @return string $sdl
     */
    // VO API
    public function getDlVo(): ?DelegacionCode
    {
        return $this->dl;
    }

    public function setDlVo(?DelegacionCode $sdl): void
    {
        $this->dl = $sdl;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getDl(): string
    {
        return $this->dl?->value() ?? '';
    }

    public function setDl(string $sdl): void
    {
        $this->dl = $sdl !== '' ? new DelegacionCode($sdl) : null;
    }

    // VO API
    public function getRegionVo(): ?RegionCode
    {
        return $this->region;
    }

    public function setRegionVo(?RegionCode $sregion): void
    {
        $this->region = $sregion;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getRegion(): string
    {
        return $this->region?->value() ?? '';
    }

    public function setRegion(string $sregion): void
    {
        $this->region = $sregion !== '' ? new RegionCode($sregion) : null;
    }

    // VO API
    public function getNombreDlVo(): ?DelegacionName
    {
        return $this->nombreDl;
    }

    public function setNombreDlVo(?DelegacionName $snombre_dl = null): void
    {
        $this->nombreDl = $snombre_dl;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function getNombre_dl(): ?string
    {
        return $this->nombreDl?->value();
    }

    public function setNombre_dl(?string $snombre_dl = null): void
    {
        $this->nombreDl = DelegacionName::fromNullableString($snombre_dl);
    }

    // VO API
    public function getStatusVo(): ?DelegacionStatus
    {
        return $this->status;
    }

    public function setStatusVo(?DelegacionStatus $bstatus = null): void
    {
        $this->status = $bstatus;
    }

    public function getGrupoEstudiosVo(): ?DelegacionGrupoEstudios
    {
        return $this->grupoEstudios;
    }

    public function setGrupoEstudiosVo(?DelegacionGrupoEstudios $sgrupo_estudios = null): void
    {
        $this->grupoEstudios = $sgrupo_estudios;
    }

    public function getRegionStgrVo(): ?DelegacionRegionStgr
    {
        return $this->regionStgr;
    }

    public function setRegionStgrVo(?DelegacionRegionStgr $sregion_stgr = null): void
    {
        $this->regionStgr = $sregion_stgr;
    }

    // Legacy scalar API (kept for mod_tabla/UI)
    public function isStatus(): ?bool
    {
        return $this->status?->value();
    }

    public function setStatus(?bool $bstatus = null): void
    {
        $this->status = ($bstatus === null) ? null : DelegacionStatus::fromScalar($bstatus);
    }

    public function getGrupo_estudios(): ?string
    {
        return $this->grupoEstudios?->value();
    }

    public function setGrupo_estudios(?string $sgrupo_estudios = null): void
    {
        $this->grupoEstudios = DelegacionGrupoEstudios::fromNullableString($sgrupo_estudios);
    }

    public function getRegion_stgr(): ?string
    {
        return $this->regionStgr?->value();
    }

    public function setRegion_stgr(?string $sregion_stgr = null): void
    {
        $this->regionStgr = DelegacionRegionStgr::fromNullableString($sregion_stgr);
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
        $oDelegacionSet->add($this->getDatosStatus());
        return $oDelegacionSet->getTot();
    }

    /**
     * DatosCampo for campo 'dl'
     * @return DatosCampo
     */
    public function getDatosDl(): DatosCampo
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
    public function getDatosNombre_dl(): DatosCampo
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
    public function getDatosRegion(): DatosCampo
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
    public function getDatosGrupo_estudios(): DatosCampo
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
    public function getDatosRegion_stgr(): DatosCampo
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
     * DatosCampo for campo 'status'
     * @return DatosCampo
     */
    public function getDatosStatus(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('status');
        $oDatosCampo->setMetodoGet('isStatus');
        $oDatosCampo->setMetodoSet('setStatus');
        $oDatosCampo->setEtiqueta(_("en activo"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }
}
