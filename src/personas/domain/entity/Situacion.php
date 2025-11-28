<?php

namespace src\personas\domain\entity;

use core\DatosCampo;
use core\Set;
use src\personas\domain\value_objects\{SituacionCode, SituacionName};

/**
 * Clase que implementa la entidad xp_situacion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class Situacion
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Código de Situación
     */
    private SituacionCode $situacion;
    /**
     * Nombre de la Situación
     */
    private ?SituacionName $nombreSituacion = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Situacion
     */
    public function setAllAttributes(array $aDatos): Situacion
    {
        if (array_key_exists('situacion', $aDatos)) {
            $valor = $aDatos['situacion'] ?? '';
            $this->setSituacionVo(new SituacionCode((string)$valor));
        }
        if (array_key_exists('nombre_situacion', $aDatos)) {
            $this->setNombreSituacionVo(SituacionName::fromNullableString($aDatos['nombre_situacion'] ?? null));
        }
        return $this;
    }

    // VO API
    public function getSituacionVo(): SituacionCode
    {
        return $this->situacion;
    }

    public function setSituacionVo(SituacionCode $codigo): void
    {
        $this->situacion = $codigo;
    }

    public function getNombreSituacionVo(): ?SituacionName
    {
        return $this->nombreSituacion;
    }

    public function setNombreSituacionVo(?SituacionName $nombre = null): void
    {
        $this->nombreSituacion = $nombre;
    }

    /**
     *
     * @return string $ssituacion
     */
    public function getSituacion(): string
    {
        return $this->situacion->value();
    }

    /**
     *
     * @param string $ssituacion
     */
    public function setSituacion(string $ssituacion): void
    {
        $ssituacion = trim($ssituacion);
        $this->situacion = new SituacionCode($ssituacion);
    }

    /**
     *
     * @return string|null $snombre_situacion
     */
    public function getNombre_situacion(): ?string
    {
        return $this->nombreSituacion?->value();
    }

    /**
     *
     * @param string|null $snombre_situacion
     */
    public function setNombre_situacion(?string $snombre_situacion = null): void
    {
        $this->nombreSituacion = SituacionName::fromNullableString($snombre_situacion);
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'situacion';
    }

    function getDatosCampos()
    {
        $oSituacionSet = new Set();

        $oSituacionSet->add($this->getDatosSituacion());
        $oSituacionSet->add($this->getDatosNombre_situacion());
        return $oSituacionSet->getTot();
    }

    function getDatosSituacion()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('situacion');
        $oDatosCampo->setMetodoGet('getSituacionVo');
        $oDatosCampo->setMetodoSet('setSituacion');
        $oDatosCampo->setEtiqueta(_("situacion"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(1);
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut snombre_situacion de Situacion
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_situacion()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_situacion');
        $oDatosCampo->setMetodoGet('getNombreSituacionVo');
        $oDatosCampo->setMetodoSet('setNombre_situacion');
        $oDatosCampo->setEtiqueta(_("nombre situación"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(60);
        return $oDatosCampo;
    }
}