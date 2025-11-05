<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;

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
     *
     * @var int|null
     */
    private int|null $iid_dl = null;
    /**
     * Dl de Delegacion
     *
     * @var string
     */
    private string $sdl;
    /**
     * Region de Delegacion
     *
     * @var string
     */
    private string $sregion;
    /**
     * Nombre_dl de Delegacion
     *
     * @var string|null
     */
    private string|null $snombre_dl = null;
    /**
     * Status de Delegacion
     *
     * @var bool|null
     */
    private bool|null $bstatus = null;
    /**
     * Grupo_estudios de Delegacion
     *
     * @var string|null
     */
    private string|null $sgrupo_estudios = null;
    /**
     * Region_stgr de Delegacion
     *
     * @var string|null
     */
    private string|null $sregion_stgr = null;

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
            $this->setId_dl($aDatos['id_dl']);
        }
        if (array_key_exists('dl', $aDatos)) {
            $this->setDl($aDatos['dl']);
        }
        if (array_key_exists('region', $aDatos)) {
            $this->setRegion($aDatos['region']);
        }
        if (array_key_exists('nombre_dl', $aDatos)) {
            $this->setNombre_dl($aDatos['nombre_dl']);
        }
        if (array_key_exists('status', $aDatos)) {
            $this->setStatus(is_true($aDatos['status']));
        }
        if (array_key_exists('grupo_estudios', $aDatos)) {
            $this->setGrupo_estudios($aDatos['grupo_estudios']);
        }
        if (array_key_exists('region_stgr', $aDatos)) {
            $this->setRegion_stgr($aDatos['region_stgr']);
        }
        return $this;
    }

    /**
     *
     * @return int|null $iid_dl
     */
    public function getId_dl(): ?int
    {
        return $this->iid_dl;
    }

    /**
     *
     * @param int|null $iid_dl
     */
    public function setId_dl(?int $iid_dl = null): void
    {
        $this->iid_dl = $iid_dl;
    }

    /**
     *
     * @return string $sdl
     */
    public function getDl(): string
    {
        return $this->sdl;
    }

    /**
     *
     * @param string $sdl
     */
    public function setDl(string $sdl): void
    {
        $this->sdl = $sdl;
    }

    /**
     *
     * @return string $sregion
     */
    public function getRegion(): string
    {
        return $this->sregion;
    }

    /**
     *
     * @param string $sregion
     */
    public function setRegion(string $sregion): void
    {
        $this->sregion = $sregion;
    }

    /**
     *
     * @return string|null $snombre_dl
     */
    public function getNombre_dl(): ?string
    {
        return $this->snombre_dl;
    }

    /**
     *
     * @param string|null $snombre_dl
     */
    public function setNombre_dl(?string $snombre_dl = null): void
    {
        $this->snombre_dl = $snombre_dl;
    }

    /**
     *
     * @return bool|null $bstatus
     */
    public function isStatus(): ?bool
    {
        return $this->bstatus;
    }

    /**
     *
     * @param bool|null $bstatus
     */
    public function setStatus(?bool $bstatus = null): void
    {
        $this->bstatus = $bstatus;
    }

    /**
     *
     * @return string|null $sgrupo_estudios
     */
    public function getGrupo_estudios(): ?string
    {
        return $this->sgrupo_estudios;
    }

    /**
     *
     * @param string|null $sgrupo_estudios
     */
    public function setGrupo_estudios(?string $sgrupo_estudios = null): void
    {
        $this->sgrupo_estudios = $sgrupo_estudios;
    }

    /**
     *
     * @return string|null $sregion_stgr
     */
    public function getRegion_stgr(): ?string
    {
        return $this->sregion_stgr;
    }

    /**
     *
     * @param string|null $sregion_stgr
     */
    public function setRegion_stgr(?string $sregion_stgr = null): void
    {
        $this->sregion_stgr = $sregion_stgr;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_dl';
    }

    public function getDatosCampos()
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
    public function getDatosDl()
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
    public function getDatosNombre_dl()
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
    public function getDatosRegion()
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
    public function getDatosGrupo_estudios()
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
    public function getDatosRegion_stgr()
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
    public function getDatosStatus()
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
