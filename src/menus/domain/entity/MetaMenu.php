<?php

namespace src\menus\domain\entity;

use core\DatosCampo;
use core\Set;
use src\menus\domain\value_objects\MetaMenuUrl;
use src\menus\domain\value_objects\MetaMenuParametros;
use src\menus\domain\value_objects\MetaMenuDescripcion;

/**
 * Clase que implementa la entidad aux_metamenus
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class MetaMenu
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_metamenu de MetaMenu
     *
     * @var int
     */
    private int $iid_metamenu;
    /**
     * Id_mod de MetaMenu
     *
     * @var int|null
     */
    private int|null $iid_mod = null;
    /**
     * Url de MetaMenu
     *
     * @var string|null
     */
    private string|null $surl = null;
    /**
     * Parametros de MetaMenu
     *
     * @var string|null
     */
    private string|null $sparametros = null;
    /**
     * Descripcion de MetaMenu
     *
     * @var string|null
     */
    private string|null $sdescripcion = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return MetaMenu
     */
    public function setAllAttributes(array $aDatos): MetaMenu
    {
        if (array_key_exists('id_metamenu', $aDatos)) {
            $this->setId_metamenu($aDatos['id_metamenu']);
        }
        if (array_key_exists('id_mod', $aDatos)) {
            $this->setId_mod($aDatos['id_mod']);
        }
        if (array_key_exists('url', $aDatos)) {
            $this->setUrl($aDatos['url']);
        }
        if (array_key_exists('parametros', $aDatos)) {
            $this->setParametros($aDatos['parametros']);
        }
        if (array_key_exists('descripcion', $aDatos)) {
            $this->setDescripcion($aDatos['descripcion']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_metamenu
     */
    public function getId_metamenu(): int
    {
        return $this->iid_metamenu;
    }

    /**
     *
     * @param int $iid_metamenu
     */
    public function setId_metamenu(int $iid_metamenu): void
    {
        $this->iid_metamenu = $iid_metamenu;
    }

    /**
     *
     * @return int|null $iid_mod
     */
    public function getId_mod(): ?int
    {
        return $this->iid_mod;
    }

    /**
     *
     * @param int|null $iid_mod
     */
    public function setId_mod(?int $iid_mod = null): void
    {
        $this->iid_mod = $iid_mod;
    }

    /**
     *
     * @return string|null $surl
     */
    public function getUrl(): ?string
    {
        return $this->surl;
    }

    /**
     *
     * @param string|null $surl
     */
    public function setUrl(string|MetaMenuUrl|null $surl = null): void
    {
        $this->surl = $surl instanceof MetaMenuUrl ? $surl->value() : $surl;
    }

    /**
     *
     * @return string|null $sparametros
     */
    public function getParametros(): ?string
    {
        return $this->sparametros;
    }

    /**
     *
     * @param string|null $sparametros
     */
    public function setParametros(string|MetaMenuParametros|null $sparametros = null): void
    {
        $this->sparametros = $sparametros instanceof MetaMenuParametros ? $sparametros->value() : $sparametros;
    }

    /**
     *
     * @return string|null $sdescripcion
     */
    public function getDescripcion(): ?string
    {
        return $this->sdescripcion;
    }

    /**
     *
     * @param string|null $sdescripcion
     */
    public function setDescripcion(string|MetaMenuDescripcion|null $sdescripcion = null): void
    {
        $this->sdescripcion = $sdescripcion instanceof MetaMenuDescripcion ? $sdescripcion->value() : $sdescripcion;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_metamenu';
    }

    function getDatosCampos()
    {
        $oMetamenuSet = new Set();

        $oMetamenuSet->add($this->getDatosModulo());
        $oMetamenuSet->add($this->getDatosUrl());
        $oMetamenuSet->add($this->getDatosParametros());
        $oMetamenuSet->add($this->getDatosDescripcion());
        return $oMetamenuSet->getTot();
    }

    function getDatosModulo()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_mod');
        $oDatosCampo->setMetodoGet('getId_mod');
        $oDatosCampo->setMetodoSet('setId_mod');
        $oDatosCampo->setEtiqueta(_("modulo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('src\\configuracion\\application\\repositories\\ModuloRepository'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNom'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayModulos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    function getDatosUrl()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('url');
        $oDatosCampo->setMetodoGet('getUrl');
        $oDatosCampo->setMetodoSet('setUrl');
        $oDatosCampo->setEtiqueta(_("url"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(60);
        return $oDatosCampo;
    }

    function getDatosParametros()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('parametros');
        $oDatosCampo->setMetodoGet('getParametros');
        $oDatosCampo->setMetodoSet('setParametros');
        $oDatosCampo->setEtiqueta(_("parametros"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    function getDatosDescripcion()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('descripcion');
        $oDatosCampo->setMetodoGet('getDescripcion');
        $oDatosCampo->setMetodoSet('setDescripcion');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

}