<?php

namespace src\actividadcargos\domain\entity;

use core\DatosCampo;
use core\Set;
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

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_cargo de Cargo
     */
    private int $iid_cargo;
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
    private bool|null $bsf = null;
    /**
     * Sv de Cargo
     *
     * @var bool|null
     */
    private bool|null $bsv = null;
    /**
     * Tipo del Cargo (código)
     */
    private ?TipoCargoCode $tipoCargo = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Cargo
     */
    public function setAllAttributes(array $aDatos): Cargo
    {
        if (array_key_exists('id_cargo', $aDatos)) {
            $this->setId_cargo($aDatos['id_cargo']);
        }
        if (array_key_exists('cargo', $aDatos)) {
            $valor = $aDatos['cargo'] ?? '';
            $this->setCargoVo(new CargoCode((string)$valor));
        }
        if (array_key_exists('orden_cargo', $aDatos)) {
            $this->setOrdenCargoVo(OrdenCargo::fromNullable(isset($aDatos['orden_cargo']) && $aDatos['orden_cargo'] !== '' ? (int)$aDatos['orden_cargo'] : null));
        }
        if (array_key_exists('sf', $aDatos)) {
            $this->setSf(is_true($aDatos['sf']));
        }
        if (array_key_exists('sv', $aDatos)) {
            $this->setSv(is_true($aDatos['sv']));
        }
        if (array_key_exists('tipo_cargo', $aDatos)) {
            $this->setTipoCargoVo(TipoCargoCode::fromNullableString($aDatos['tipo_cargo'] ?? null));
        }
        return $this;
    }

    // -------- VO API --------
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

    /**
     *
     * @return int $iid_cargo
     */
    public function getId_cargo(): int
    {
        return $this->iid_cargo;
    }

    /**
     *
     * @param int $iid_cargo
     */
    public function setId_cargo(int $iid_cargo): void
    {
        $this->iid_cargo = $iid_cargo;
    }

    /**
     *
     * @return string $scargo
     */
    public function getCargo(): string
    {
        return $this->cargo->value();
    }

    /**
     *
     * @param string $scargo
     */
    public function setCargo(string $scargo): void
    {
        $scargo = trim($scargo);
        $this->cargo = new CargoCode($scargo);
    }

    /**
     *
     * @return int|null $iorden_cargo
     */
    public function getOrden_cargo(): ?int
    {
        return $this->ordenCargo?->value();
    }

    /**
     *
     * @param int|null $iorden_cargo
     */
    public function setOrden_cargo(?int $iorden_cargo = null): void
    {
        $this->ordenCargo = OrdenCargo::fromNullable($iorden_cargo);
    }

    /**
     *
     * @return bool|null $bsf
     */
    public function isSf(): ?bool
    {
        return $this->bsf;
    }

    /**
     *
     * @param bool|null $bsf
     */
    public function setSf(?bool $bsf = null): void
    {
        $this->bsf = $bsf;
    }

    /**
     *
     * @return bool|null $bsv
     */
    public function isSv(): ?bool
    {
        return $this->bsv;
    }

    /**
     *
     * @param bool|null $bsv
     */
    public function setSv(?bool $bsv = null): void
    {
        $this->bsv = $bsv;
    }

    /**
     *
     * @return string|null $stipo_cargo
     */
    public function getTipo_cargo(): ?string
    {
        return $this->tipoCargo?->value();
    }

    /**
     *
     * @param string|null $stipo_cargo
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
    function getDatosCampos()
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
     * Recupera les propietats de l'atribut scargo de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosCargo()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('cargo');
        $oDatosCampo->setMetodoGet('getCargo');
        $oDatosCampo->setMetodoSet('setCargo');
        $oDatosCampo->setEtiqueta(_("cargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(2);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden_cargo de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosOrden_cargo()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('orden_cargo');
        $oDatosCampo->setMetodoGet('getOrden_cargo');
        $oDatosCampo->setMetodoSet('setOrden_cargo');
        $oDatosCampo->setEtiqueta(_("orden cargo"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(8);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut bsf de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosSf()
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
     * Recupera les propietats de l'atribut bsv de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosSv()
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
     * Recupera les propietats de l'atribut stipo_cargo de cargo
     * en una clase del tipus DatosCampo
     *
     * @return object DatosCampo
     */
    function getDatosTipo_cargo()
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