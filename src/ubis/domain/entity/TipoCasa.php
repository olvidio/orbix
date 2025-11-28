<?php

namespace src\ubis\domain\entity;

use core\DatosCampo;
use core\Set;
use src\ubis\domain\value_objects\{TipoCasaCode, TipoCasaName};

/**
 * Clase que implementa la entidad xu_tipo_casa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class TipoCasa
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Código del Tipo de Casa
     */
    private TipoCasaCode $tipoCasa;
    /**
     * Nombre del Tipo de Casa
     */
    private ?TipoCasaName $nombreTipoCasa = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TipoCasa
     */
    public function setAllAttributes(array $aDatos): TipoCasa
    {
        if (array_key_exists('tipo_casa', $aDatos)) {
            $valor = $aDatos['tipo_casa'] ?? '';
            $this->setTipoCasaVo(isset($valor) && $valor !== '' ? new TipoCasaCode((string)$valor) : null);
        }
        if (array_key_exists('nombre_tipo_casa', $aDatos)) {
            $this->setNombreTipoCasaVo(TipoCasaName::fromNullableString($aDatos['nombre_tipo_casa'] ?? null));
        }
        return $this;
    }

    // VO API
    public function getTipoCasaVo(): TipoCasaCode
    {
        return $this->tipoCasa;
    }

    public function setTipoCasaVo(TipoCasaCode $tipoCasa): void
    {
        $this->tipoCasa = $tipoCasa;
    }

    /**
     *
     * @return string $stipo_casa
     */
    public function getTipo_casa(): string
    {
        return $this->tipoCasa->value();
    }

    /**
     *
     * @param string $stipo_casa
     */
    public function setTipo_casa(string $stipo_casa): void
    {
        $stipo_casa = trim($stipo_casa);
        $this->tipoCasa = new TipoCasaCode($stipo_casa);
    }

    // VO API
    public function getNombreTipoCasaVo(): ?TipoCasaName
    {
        return $this->nombreTipoCasa;
    }

    public function setNombreTipoCasaVo(?TipoCasaName $nombre = null): void
    {
        $this->nombreTipoCasa = $nombre;
    }

    /**
     *
     * @return string|null $snombre_tipo_casa
     */
    public function getNombre_tipo_casa(): ?string
    {
        return $this->nombreTipoCasa?->value();
    }

    /**
     *
     * @param string|null $snombre_tipo_casa
     */
    public function setNombre_tipo_casa(?string $snombre_tipo_casa = null): void
    {
        $this->nombreTipoCasa = TipoCasaName::fromNullableString($snombre_tipo_casa);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'tipo_casa';
    }

    function getDatosCampos()
    {
        $oTipoDeCasaSet = new Set();

        $oTipoDeCasaSet->add($this->getDatosTipo_casa());
        $oTipoDeCasaSet->add($this->getDatosNombre_tipo_casa());
        return $oTipoDeCasaSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut stipo_casa de TipoDeCasa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_casa()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_casa');
        $oDatosCampo->setMetodoGet('getTipo_casa');
        $oDatosCampo->setMetodoSet('setTipo_casa');
        $oDatosCampo->setEtiqueta(_("tipo de casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(6);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_tipo_casa de TipoDeCasa
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_tipo_casa()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_tipo_casa');
        $oDatosCampo->setMetodoGet('getNombre_tipo_casa');
        $oDatosCampo->setMetodoSet('setNombre_tipo_casa');
        $oDatosCampo->setEtiqueta(_("nombre del tipo de casa"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}