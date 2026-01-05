<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use function core\is_true;
use src\ubis\domain\value_objects\RegionCode;
use src\ubis\domain\value_objects\RegionId;


class Region
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int|null $id_region = null;

    private string $region;

    private string|null $nombre_region = null;

    private bool|null $active = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated Usar `getIdRegionVo(): ?RegionId` en su lugar.
     */
    public function getId_region(): ?int
    {
        return $this->id_region;
    }


    /**
     * @deprecated Usar `setIdRegionVo(?RegionId $id): void` en su lugar.
     */
    public function setId_region(?int $id_region = null): void
    {
        $this->id_region = $id_region;
    }

    // Value Object API for id_region
    public function getIdRegionVo(): ?RegionId
    {
        return $this->id_region !== null ? new RegionId($this->id_region) : null;
    }

    public function setIdRegionVo(?RegionId $id = null): void
    {
        $this->id_region = $id?->value();
    }


    /**
     * @deprecated Usar `getRegionVo(): ?RegionCode` en su lugar.
     */
    public function getRegion(): string
    {
        return $this->region;
    }


    /**
     * @deprecated Usar `setRegionVo(?RegionCode $code): void` en su lugar.
     */
    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    // Value Object API for region code (sigla)
    public function getRegionVo(): ?RegionCode
    {
        // Return null if empty or unset to allow optional usage
        return isset($this->region) && $this->region !== '' ? new RegionCode($this->region) : null;
    }

    public function setRegionVo(?RegionCode $code = null): void
    {
        $this->region = $code?->value() ?? '';
    }


    public function getNombre_region(): ?string
    {
        return $this->nombre_region;
    }


    public function setNombre_region(?string $nombre_region = null): void
    {
        $this->nombre_region = $nombre_region;
    }


    public function isActive(): ?bool
    {
        return $this->active;
    }


    public function setActive(?bool $active = null): void
    {
        $this->active = $active;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_region';
    }

    public function getDatosCampos(): array
    {
        $oRegionSet = new Set();

        //$oRegionSet->add($this->getDatosId_region());
        $oRegionSet->add($this->getDatosRegion());
        $oRegionSet->add($this->getDatosNombre_region());
        $oRegionSet->add($this->getDatosStatus());
        return $oRegionSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo id_region de Region
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosRegion(): DatosCampo
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
     * Recupera las propiedades del atributo nombre_region de Region
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombre_region(): DatosCampo
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
     * Recupera las propiedades del atributo active de Region
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosStatus(): DatosCampo
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