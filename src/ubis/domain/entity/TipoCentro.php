<?php

namespace src\ubis\domain\entity;
use core\DatosCampo;
use core\Set;
use src\ubis\domain\value_objects\{TipoCentroCode, TipoCentroName};

/**
 * Clase que implementa la entidad xu_tipo_ctr
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 14/11/2025
 */
class TipoCentro
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Código del Tipo de Centro
     */
    private TipoCentroCode $tipoCentro;
    /**
     * Nombre del Tipo de Centro
     */
    private ?TipoCentroName $nombreTipoCentro = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TipoCentro
     */
    public function setAllAttributes(array $aDatos): TipoCentro
    {
        if (array_key_exists('tipo_ctr', $aDatos)) {
            $valor = $aDatos['tipo_ctr'] ?? '';
            $this->setTipoCentroVo(isset($valor) && $valor !== '' ? new TipoCentroCode((string)$valor) : null);
        }
        if (array_key_exists('nombre_tipo_ctr', $aDatos)) {
            $this->setNombreTipoCentroVo(TipoCentroName::fromNullableString($aDatos['nombre_tipo_ctr'] ?? null));
        }
        return $this;
    }

    // VO API
    public function getTipoCentroVo(): TipoCentroCode
    {
        return $this->tipoCentro;
    }

    public function setTipoCentroVo(?TipoCentroCode $tipoCentro = null): void
    {
        $this->tipoCentro = $tipoCentro;
    }

    /**
     *
     * @return string $stipo_ctr
     */
    public function getTipo_ctr(): string
    {
        return $this->tipoCentro?->value();
    }

    /**
     *
     * @param string $stipo_ctr
     */
    public function setTipo_ctr(string $stipo_ctr): void
    {
        $stipo_ctr = trim($stipo_ctr);
        $this->tipoCentro = $stipo_ctr !== '' ? new TipoCentroCode($stipo_ctr) : null;
    }

    // VO API
    public function getNombreTipoCentroVo(): ?TipoCentroName
    {
        return $this->nombreTipoCentro;
    }

    public function setNombreTipoCentroVo(?TipoCentroName $nombre = null): void
    {
        $this->nombreTipoCentro = $nombre;
    }

    /**
     *
     * @return string|null $snombre_tipo_ctr
     */
    public function getNombre_tipo_ctr(): ?string
    {
        return $this->nombreTipoCentro?->value();
    }

    /**
     *
     * @param string|null $snombre_tipo_ctr
     */
    public function setNombre_tipo_ctr(?string $snombre_tipo_ctr = null): void
    {
        $this->nombreTipoCentro = TipoCentroName::fromNullableString($snombre_tipo_ctr);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'tipo_ctr';
    }

    function getDatosCampos()
    {
        $oTipoCentroSet = new Set();

        $oTipoCentroSet->add($this->getDatosTipo_ctr());
        $oTipoCentroSet->add($this->getDatosNombre_tipo_ctr());
        return $oTipoCentroSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut stipo_ctr de TipoCentro
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosTipo_ctr()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_ctr');
        $oDatosCampo->setMetodoGet('getTipo_ctr');
        $oDatosCampo->setMetodoSet('setTipo_ctr');
        $oDatosCampo->setEtiqueta(_("tipo de centro"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_tipo_ctr de TipoCentro
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_tipo_ctr()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_tipo_ctr');
        $oDatosCampo->setMetodoGet('getNombre_tipo_ctr');
        $oDatosCampo->setMetodoSet('setNombre_tipo_ctr');
        $oDatosCampo->setEtiqueta(_("nombre de tipo centro"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }
}