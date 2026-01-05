<?php

namespace src\actividadcargos\domain\entity;

use core\DatosCampo;
use core\Set;
use src\shared\domain\traits\Hydratable;
use function core\is_true;
use src\actividadcargos\domain\value_objects\{CargoCode, OrdenCargo, TipoCargoCode};

/**
 * Clase que implementa la entidad xd_orden_cargo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class Cargo
{
    use Hydratable;
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_cargo de Cargo
     */
    private int $id_cargo;
    /**
     * Código del Cargo
     */
    private CargoCode $cargo;
    /**
     * Orden del Cargo
     */
    private ?OrdenCargo $ordenCargo = null;
    /**
     * Sf de Cargo
     *
     * @var bool|null
     */
    private bool|null $sf = null;
    /**
     * Sv de Cargo
     *
     * @var bool|null
     */
    private bool|null $sv = null;
    /**
     * Tipo del Cargo (código)
     */
    private ?TipoCargoCode $tipoCargo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getCargoVo(): CargoCode
    {
        return $this->cargo;
    }

    public function setCargoVo(CargoCode $codigo): void
    {
        $this->cargo = $codigo;
    }

    public function getOrdenCargoVo(): ?OrdenCargo
    {
        return $this->ordenCargo;
    }

    public function setOrdenCargoVo(?OrdenCargo $orden = null): void
    {
        $this->ordenCargo = $orden;
    }

    public function getTipoCargoVo(): ?TipoCargoCode
    {
        return $this->tipoCargo;
    }

    public function setTipoCargoVo(?TipoCargoCode $tipo = null): void
    {
        $this->tipoCargo = $tipo;
    }

    public function getId_cargo(): int
    {
        return $this->id_cargo;
    }

    public function setId_cargo(int $id_cargo): void
    {
        $this->id_cargo = $id_cargo;
    }

    /**
     * @deprecated usar getCargoVo()
     */
    public function getCargo(): string
    {
        return $this->cargo->value();
    }

    /**
     * @deprecated usar setCargoVo(CargoCode $codigo)
     */
    public function setCargo(string $scargo): void
    {
        $scargo = trim($scargo);
        $this->cargo = new CargoCode($scargo);
    }

    /**
     * @deprecated usar getOrdenCargoVo()
     */
    public function getOrden_cargo(): ?int
    {
        return $this->ordenCargo?->value();
    }

    /**
     * @deprecated usar setOrdenCargoVo(?OrdenCargo $orden)
     */
    public function setOrden_cargo(?int $iorden_cargo = null): void
    {
        $this->ordenCargo = OrdenCargo::fromNullable($iorden_cargo);
    }

    public function isSf(): ?bool
    {
        return $this->sf;
    }

    public function setSf(?bool $bsf = null): void
    {
        $this->sf = $bsf;
    }

    public function isSv(): ?bool
    {
        return $this->sv;
    }

    public function setSv(?bool $bsv = null): void
    {
        $this->sv = $bsv;
    }

    /**
     * @deprecated usar getTipoCargoVo()
     */
    public function getTipo_cargo(): ?string
    {
        return $this->tipoCargo?->value();
    }

    /**
     * @deprecated usar setTipoCargoVo(?TipoCargoCode $tipo)
     */
    public function setTipo_cargo(?string $stipo_cargo = null): void
    {
        $this->tipoCargo = TipoCargoCode::fromNullableString($stipo_cargo);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_cargo';
    }

    /**
     * Retorna una col·lecció d'objectes del tipus DatosCampo
     *
     */
    public function getDatosCampos(): array
    {
        $ocargoSet = new Set();

        $ocargoSet->add($this->getDatosCargo());
        $ocargoSet->add($this->getDatosOrden_cargo());
        $ocargoSet->add($this->getDatosSf());
        $ocargoSet->add($this->getDatosSv());
        $ocargoSet->add($this->getDatosTipo_cargo());
        return $ocargoSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo cargo de cargo
     * en una clase del tipo DatosCampo
     *
     * @return object DatosCampo
     */
    private function getDatosCargo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('cargo');
        $oDatosCampo->setMetodoGet('getCargo');
        $oDatosCampo->setMetodoSet('setCargo');
        $oDatosCampo->setEtiqueta(_("cargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(8);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo orden_cargo de cargo
     * en una clase del tipo DatosCampo
     *
     * @return object DatosCampo
     */
    private function getDatosOrden_cargo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('orden_cargo');
        $oDatosCampo->setMetodoGet('getOrden_cargo');
        $oDatosCampo->setMetodoSet('setOrden_cargo');
        $oDatosCampo->setEtiqueta(_("orden cargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(2);
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo sf de cargo
     * en una clase del tipo DatosCampo
     *
     * @return object DatosCampo
     */
    private function getDatosSf(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('sf');
        $oDatosCampo->setMetodoGet('isSf');
        $oDatosCampo->setMetodoSet('setSf');
        $oDatosCampo->setEtiqueta(_("sf"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo sv de cargo
     * en una clase del tipo DatosCampo
     *
     * @return object DatosCampo
     */
    private function getDatosSv(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('sv');
        $oDatosCampo->setMetodoGet('isSv');
        $oDatosCampo->setMetodoSet('setSv');
        $oDatosCampo->setEtiqueta(_("sv"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    /**
     * Recupera las propiedades del atributo tipo_cargo de cargo
     * en una clase del tipo DatosCampo
     *
     * @return object DatosCampo
     */
    private function getDatosTipo_cargo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_cargo');
        $oDatosCampo->setMetodoGet('getTipo_Cargo');
        $oDatosCampo->setMetodoSet('setTipo_Cargo');
        $oDatosCampo->setEtiqueta(_("tipo de cargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(8);
        return $oDatosCampo;
    }
}