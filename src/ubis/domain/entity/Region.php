<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;
use src\ubis\domain\value_objects\RegionCode;
use src\ubis\domain\value_objects\RegionId;

/**
 * Clase que implementa la entidad xu_region
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
class Region
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_region de Region
     *
     * @var int|null
     */
    private int|null $iid_region = null;
    /**
     * Region de Region
     *
     * @var string
     */
    private string $sregion;
    /**
     * Nombre_region de Region
     *
     * @var string|null
     */
    private string|null $snombre_region = null;
    /**
     * Status de Region
     *
     * @var bool|null
     */
    private bool|null $bstatus = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Region
     */
    public function setAllAttributes(array $aDatos): Region
    {
        if (array_key_exists('id_region', $aDatos)) {
            $this->setId_region($aDatos['id_region']);
        }
        if (array_key_exists('region', $aDatos)) {
            $this->setRegion($aDatos['region']);
        }
        if (array_key_exists('nombre_region', $aDatos)) {
            $this->setNombre_region($aDatos['nombre_region']);
        }
        if (array_key_exists('status', $aDatos)) {
            $this->setStatus(is_true($aDatos['status']));
        }
        return $this;
    }

    /**
     *
     * @return int|null $iid_region
     */
    /**
     * @deprecated Usar `getIdRegionVo(): ?RegionId` en su lugar.
     */
    public function getId_region(): ?int
    {
        return $this->iid_region;
    }

    /**
     *
     * @param int|null $iid_region
     */
    /**
     * @deprecated Usar `setIdRegionVo(?RegionId $id): void` en su lugar.
     */
    public function setId_region(?int $iid_region = null): void
    {
        $this->iid_region = $iid_region;
    }

    // Value Object API for id_region
    public function getIdRegionVo(): ?RegionId
    {
        return $this->iid_region !== null ? new RegionId($this->iid_region) : null;
    }

    public function setIdRegionVo(?RegionId $id = null): void
    {
        $this->iid_region = $id?->value();
    }

    /**
     *
     * @return string $sregion
     */
    /**
     * @deprecated Usar `getRegionVo(): ?RegionCode` en su lugar.
     */
    public function getRegion(): string
    {
        return $this->sregion;
    }

    /**
     *
     * @param string $sregion
     */
    /**
     * @deprecated Usar `setRegionVo(?RegionCode $code): void` en su lugar.
     */
    public function setRegion(string $sregion): void
    {
        $this->sregion = $sregion;
    }

    // Value Object API for region code (sigla)
    public function getRegionVo(): ?RegionCode
    {
        // Return null if empty or unset to allow optional usage
        return isset($this->sregion) && $this->sregion !== '' ? new RegionCode($this->sregion) : null;
    }

    public function setRegionVo(?RegionCode $code = null): void
    {
        $this->sregion = $code?->value() ?? '';
    }

    /**
     *
     * @return string|null $snombre_region
     */
    public function getNombre_region(): ?string
    {
        return $this->snombre_region;
    }

    /**
     *
     * @param string|null $snombre_region
     */
    public function setNombre_region(?string $snombre_region = null): void
    {
        $this->snombre_region = $snombre_region;
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

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_region';
    }

    function getDatosCampos()
    {
        $oRegionSet = new Set();

        //$oRegionSet->add($this->getDatosId_region());
        $oRegionSet->add($this->getDatosRegion());
        $oRegionSet->add($this->getDatosNombre_region());
        $oRegionSet->add($this->getDatosStatus());
        return $oRegionSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut iid_region de Region
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosRegion()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('region');
        $oDatosCampo->setMetodoGet('getRegion');
        $oDatosCampo->setMetodoSet('setRegion');
        $oDatosCampo->setEtiqueta(_("sigla"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(6);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_region de Region
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_region()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_region');
        $oDatosCampo->setMetodoGet('getNombre_region');
        $oDatosCampo->setMetodoSet('setNombre_region');
        $oDatosCampo->setEtiqueta(_("nombre de la región"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bstatus de Region
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosStatus()
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