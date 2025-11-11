<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Clase que implementa la entidad i_tipo_documento_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class TipoDoc
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_tipo_doc de TipoDoc
     *
     * @var int
     */
    private int $iid_tipo_doc;
    /**
     * Nom_doc de TipoDoc
     *
     * @var string|null
     */
    private string|null $snom_doc = null;
    /**
     * Sigla de TipoDoc
     *
     * @var string
     */
    private string $ssigla;
    /**
     * Observ de TipoDoc
     *
     * @var string|null
     */
    private string|null $sobserv = null;
    /**
     * Id_coleccion de TipoDoc
     *
     * @var int|null
     */
    private int|null $iid_coleccion = null;
    /**
     * Bajo_llave de TipoDoc
     *
     * @var bool|null
     */
    private bool|null $bbajo_llave = null;
    /**
     * Vigente de TipoDoc
     *
     * @var bool|null
     */
    private bool|null $bvigente = null;
    /**
     * Numerado de TipoDoc
     *
     * @var bool
     */
    private bool $bnumerado;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TipoDoc
     */
    public function setAllAttributes(array $aDatos): TipoDoc
    {
        if (array_key_exists('id_tipo_doc', $aDatos)) {
            $this->setId_tipo_doc($aDatos['id_tipo_doc']);
        }
        if (array_key_exists('nom_doc', $aDatos)) {
            $this->setNom_doc($aDatos['nom_doc']);
        }
        if (array_key_exists('sigla', $aDatos)) {
            $this->setSigla($aDatos['sigla']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('id_coleccion', $aDatos)) {
            $this->setId_coleccion($aDatos['id_coleccion']);
        }
        if (array_key_exists('bajo_llave', $aDatos)) {
            $this->setBajo_llave(is_true($aDatos['bajo_llave']));
        }
        if (array_key_exists('vigente', $aDatos)) {
            $this->setVigente(is_true($aDatos['vigente']));
        }
        if (array_key_exists('numerado', $aDatos)) {
            $this->setNumerado(is_true($aDatos['numerado']));
        }
        return $this;
    }

    /**
     *
     * @return int $iid_tipo_doc
     */
    public function getId_tipo_doc(): int
    {
        return $this->iid_tipo_doc;
    }

    /**
     *
     * @param int $iid_tipo_doc
     */
    public function setId_tipo_doc(int $iid_tipo_doc): void
    {
        $this->iid_tipo_doc = $iid_tipo_doc;
    }

    /**
     *
     * @return string|null $snom_doc
     */
    public function getNom_doc(): ?string
    {
        return $this->snom_doc;
    }

    /**
     *
     * @param string|null $snom_doc
     */
    public function setNom_doc(?string $snom_doc = null): void
    {
        $this->snom_doc = $snom_doc;
    }

    /**
     *
     * @return string $ssigla
     */
    public function getSigla(): string
    {
        return $this->ssigla;
    }

    /**
     *
     * @param string $ssigla
     */
    public function setSigla(string $ssigla): void
    {
        $this->ssigla = $ssigla;
    }

    /**
     *
     * @return string|null $sobserv
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     *
     * @param string|null $sobserv
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }

    /**
     *
     * @return int|null $iid_coleccion
     */
    public function getId_coleccion(): ?int
    {
        return $this->iid_coleccion;
    }

    /**
     *
     * @param int|null $iid_coleccion
     */
    public function setId_coleccion(?int $iid_coleccion = null): void
    {
        $this->iid_coleccion = $iid_coleccion;
    }

    /**
     *
     * @return bool|null $bbajo_llave
     */
    public function isBajo_llave(): ?bool
    {
        return $this->bbajo_llave;
    }

    /**
     *
     * @param bool|null $bbajo_llave
     */
    public function setBajo_llave(?bool $bbajo_llave = null): void
    {
        $this->bbajo_llave = $bbajo_llave;
    }

    /**
     *
     * @return bool|null $bvigente
     */
    public function isVigente(): ?bool
    {
        return $this->bvigente;
    }

    /**
     *
     * @param bool|null $bvigente
     */
    public function setVigente(?bool $bvigente = null): void
    {
        $this->bvigente = $bvigente;
    }

    /**
     *
     * @return bool $bnumerado
     */
    public function isNumerado(): bool
    {
        return $this->bnumerado;
    }

    /**
     *
     * @param bool $bnumerado
     */
    public function setNumerado(?bool $bnumerado): void
    {
        $this->bnumerado = $bnumerado?? False;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_tipo_doc';
    }

    function getDatosCampos()
    {
        $oTipoDocSet = new Set();

        $oTipoDocSet->add($this->getDatosSigla());
        $oTipoDocSet->add($this->getDatosNom_doc());
        $oTipoDocSet->add($this->getDatosObserv());
        $oTipoDocSet->add($this->getDatosId_coleccion());
        $oTipoDocSet->add($this->getDatosBajo_llave());
        $oTipoDocSet->add($this->getDatosVigente());
        $oTipoDocSet->add($this->getDatosNumerado());
        return $oTipoDocSet->getTot();
    }

    function getDatosNom_doc()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nom_doc');
        $oDatosCampo->setMetodoGet('getNom_doc');
        $oDatosCampo->setMetodoSet('setNom_doc');
        $oDatosCampo->setEtiqueta(_("detalle"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    function getDatosSigla()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('sigla');
        $oDatosCampo->setMetodoGet('getSigla');
        $oDatosCampo->setMetodoSet('setSigla');
        $oDatosCampo->setEtiqueta(_("sigla/nombre"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    function getDatosObserv()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('observ');
        $oDatosCampo->setMetodoGet('getObserv');
        $oDatosCampo->setMetodoSet('setObserv');
        $oDatosCampo->setEtiqueta(_("observ"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(90);
        return $oDatosCampo;
    }

    function getDatosId_coleccion()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_coleccion');
        $oDatosCampo->setMetodoGet('getId_coleccion');
        $oDatosCampo->setMetodoSet('setId_coleccion');
        $oDatosCampo->setEtiqueta(_("coleccion"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('src\\inventario\\application\\repositories\\ColeccionRepository');
        $oDatosCampo->setArgument2('getNom_coleccion');
        $oDatosCampo->setArgument3('getArrayColecciones');

        return $oDatosCampo;
    }

    function getDatosBajo_llave()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('bajo_llave');
        $oDatosCampo->setMetodoGet('isBajo_llave');
        $oDatosCampo->setMetodoSet('setBajo_llave');
        $oDatosCampo->setEtiqueta(_("bajo llave"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosVigente()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('vigente');
        $oDatosCampo->setMetodoGet('isVigente');
        $oDatosCampo->setMetodoSet('setVigente');
        $oDatosCampo->setEtiqueta(_("vigente"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosNumerado()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('numerado');
        $oDatosCampo->setMetodoGet('isNumerado');
        $oDatosCampo->setMetodoSet('setNumerado');
        $oDatosCampo->setEtiqueta(_("numerado"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

}