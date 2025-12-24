<?php

namespace src\dossiers\domain\entity;

use core\DatosCampo;
use core\Set;
use function core\is_true;

/**
 * Clase que implementa la entidad d_tipos_dossiers
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
class TipoDossier
{
    // db constantes.
    const DB_COMUN = 1; // Base de datos comun
    const DB_INTERIOR = 2; // Base de datos interior (sv)
    const DB_EXTERIOR = 3; // Base de datos exterior (sv-e)

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_tipo_dossier de TipoDossier
     *
     * @var int
     */
    private int $iid_tipo_dossier;
    /**
     * Descripcion de TipoDossier
     *
     * @var string|null
     */
    private string|null $sdescripcion = null;
    /**
     * Tabla_from de TipoDossier
     *
     * @var string
     */
    private string $stabla_from;
    /**
     * Tabla_to de TipoDossier
     *
     * @var string|null
     */
    private string|null $stabla_to = null;
    /**
     * Campo_to de TipoDossier
     *
     * @var string|null
     */
    private string|null $scampo_to = null;
    /**
     * Id_tipo_dossier_rel de TipoDossier
     *
     * @var int|null
     */
    private int|null $iid_tipo_dossier_rel = null;
    /**
     * Permiso_lectura de TipoDossier
     *
     * @var int
     */
    private int $ipermiso_lectura;
    /**
     * Permiso_escritura de TipoDossier
     *
     * @var int|null
     */
    private int|null $ipermiso_escritura = null;
    /**
     * Depende_modificar de TipoDossier
     *
     * @var bool
     */
    private bool $bdepende_modificar;
    /**
     * App de TipoDossier
     *
     * @var string|null
     */
    private string|null $sapp = null;
    /**
     * Class de TipoDossier
     *
     * @var string|null
     */
    private string|null $sclass = null;
    /**
     * Db de TipoDossier
     *
     * @var int|null
     */
    private int|null $idb = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return TipoDossier
     */
    public function setAllAttributes(array $aDatos): TipoDossier
    {
        if (array_key_exists('id_tipo_dossier', $aDatos)) {
            $this->setId_tipo_dossier($aDatos['id_tipo_dossier']);
        }
        if (array_key_exists('descripcion', $aDatos)) {
            $this->setDescripcion($aDatos['descripcion']);
        }
        if (array_key_exists('tabla_from', $aDatos)) {
            $this->setTabla_from($aDatos['tabla_from']);
        }
        if (array_key_exists('tabla_to', $aDatos)) {
            $this->setTabla_to($aDatos['tabla_to']);
        }
        if (array_key_exists('campo_to', $aDatos)) {
            $this->setCampo_to($aDatos['campo_to']);
        }
        if (array_key_exists('id_tipo_dossier_rel', $aDatos)) {
            $this->setId_tipo_dossier_rel($aDatos['id_tipo_dossier_rel']);
        }
        if (array_key_exists('permiso_lectura', $aDatos)) {
            $this->setPermiso_lectura($aDatos['permiso_lectura']);
        }
        if (array_key_exists('permiso_escritura', $aDatos)) {
            $this->setPermiso_escritura($aDatos['permiso_escritura']);
        }
        if (array_key_exists('depende_modificar', $aDatos)) {
            $this->setDepende_modificar(is_true($aDatos['depende_modificar']));
        }
        if (array_key_exists('app', $aDatos)) {
            $this->setApp($aDatos['app']);
        }
        if (array_key_exists('class', $aDatos)) {
            $this->setClass($aDatos['class']);
        }
        if (array_key_exists('db', $aDatos)) {
            $this->setDb($aDatos['db']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_tipo_dossier
     */
    public function getId_tipo_dossier(): int
    {
        return $this->iid_tipo_dossier;
    }

    /**
     *
     * @param int $iid_tipo_dossier
     */
    public function setId_tipo_dossier(int $iid_tipo_dossier): void
    {
        $this->iid_tipo_dossier = $iid_tipo_dossier;
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
    public function setDescripcion(?string $sdescripcion = null): void
    {
        $this->sdescripcion = $sdescripcion;
    }

    /**
     *
     * @return string $stabla_from
     */
    public function getTabla_from(): string
    {
        return $this->stabla_from;
    }

    /**
     *
     * @param string $stabla_from
     */
    public function setTabla_from(string $stabla_from): void
    {
        $this->stabla_from = $stabla_from;
    }

    /**
     *
     * @return string|null $stabla_to
     */
    public function getTabla_to(): ?string
    {
        return $this->stabla_to;
    }

    /**
     *
     * @param string|null $stabla_to
     */
    public function setTabla_to(?string $stabla_to = null): void
    {
        $this->stabla_to = $stabla_to;
    }

    /**
     *
     * @return string|null $scampo_to
     */
    public function getCampo_to(): ?string
    {
        return $this->scampo_to;
    }

    /**
     *
     * @param string|null $scampo_to
     */
    public function setCampo_to(?string $scampo_to = null): void
    {
        $this->scampo_to = $scampo_to;
    }

    /**
     *
     * @return int|null $iid_tipo_dossier_rel
     */
    public function getId_tipo_dossier_rel(): ?int
    {
        return $this->iid_tipo_dossier_rel;
    }

    /**
     *
     * @param int|null $iid_tipo_dossier_rel
     */
    public function setId_tipo_dossier_rel(?int $iid_tipo_dossier_rel = null): void
    {
        $this->iid_tipo_dossier_rel = $iid_tipo_dossier_rel;
    }

    /**
     *
     * @return int $ipermiso_lectura
     */
    public function getPermiso_lectura(): int
    {
        return $this->ipermiso_lectura;
    }

    /**
     *
     * @param int $ipermiso_lectura
     */
    public function setPermiso_lectura(int $ipermiso_lectura): void
    {
        $this->ipermiso_lectura = $ipermiso_lectura;
    }

    /**
     *
     * @return int|null $ipermiso_escritura
     */
    public function getPermiso_escritura(): ?int
    {
        return $this->ipermiso_escritura;
    }

    /**
     *
     * @param int|null $ipermiso_escritura
     */
    public function setPermiso_escritura(?int $ipermiso_escritura = null): void
    {
        $this->ipermiso_escritura = $ipermiso_escritura;
    }

    /**
     *
     * @return bool $bdepende_modificar
     */
    public function isDepende_modificar(): bool
    {
        return $this->bdepende_modificar;
    }

    /**
     *
     * @param bool $bdepende_modificar
     */
    public function setDepende_modificar(bool $bdepende_modificar): void
    {
        $this->bdepende_modificar = $bdepende_modificar;
    }

    /**
     *
     * @return string|null $sapp
     */
    public function getApp(): ?string
    {
        return $this->sapp;
    }

    /**
     *
     * @param string|null $sapp
     */
    public function setApp(?string $sapp = null): void
    {
        $this->sapp = $sapp;
    }

    /**
     *
     * @return string|null $sclass
     */
    public function getClass(): ?string
    {
        return $this->sclass;
    }

    /**
     *
     * @param string|null $sclass
     */
    public function setClass(?string $sclass = null): void
    {
        $this->sclass = $sclass;
    }

    /**
     *
     * @return int|null $idb
     */
    public function getDb(): ?int
    {
        return $this->idb;
    }

    /**
     *
     * @param int|null $idb
     */
    public function setDb(?int $idb = null): void
    {
        $this->idb = $idb;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tipo_dossier';
    }


    function getDatosCampos():array
    {
        $oTipoDossierSet = new Set();

        $oTipoDossierSet->add($this->getDatosDescripcion());
        $oTipoDossierSet->add($this->getDatosTabla_from());
        $oTipoDossierSet->add($this->getDatosTabla_to());
        $oTipoDossierSet->add($this->getDatosCampo_to());
        $oTipoDossierSet->add($this->getDatosId_tipo_dossier_rel());
        $oTipoDossierSet->add($this->getDatosPermiso_lectura());
        $oTipoDossierSet->add($this->getDatosPermiso_escritura());
        $oTipoDossierSet->add($this->getDatosDepende_modificar());
        $oTipoDossierSet->add($this->getDatosApp());
        $oTipoDossierSet->add($this->getDatosClass());
        $oTipoDossierSet->add($this->getDatosDb());
        return $oTipoDossierSet->getTot();
    }


    function getDatosDescripcion():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('descripcion');
        $oDatosCampo->setMetodoGet('getDescripcion');
        $oDatosCampo->setMetodoSet('setDescripcion');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(70);
        return $oDatosCampo;
    }

    function getDatosTabla_from():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tabla_from');
        $oDatosCampo->setMetodoGet('getTabla_from');
        $oDatosCampo->setMetodoSet('setTabla_from');
        $oDatosCampo->setEtiqueta(_("tabla_from"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(1);
        return $oDatosCampo;
    }

    function getDatosTabla_to():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tabla_to');
        $oDatosCampo->setMetodoGet('getTabla_to');
        $oDatosCampo->setMetodoSet('setTabla_to');
        $oDatosCampo->setEtiqueta(_("tabla_to"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    function getDatosCampo_to():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('campo_to');
        $oDatosCampo->setMetodoGet('getCampo_to');
        $oDatosCampo->setMetodoSet('setCampo_to');
        $oDatosCampo->setEtiqueta(_("campo_to"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(20);
        return $oDatosCampo;
    }

    function getDatosId_tipo_dossier_rel():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_dossier_rel');
        $oDatosCampo->setMetodoGet('getId_tipo_dossier_rel');
        $oDatosCampo->setMetodoSet('setId_tipo_dossier_rel');
        $oDatosCampo->setEtiqueta(_("id_tipo_dossier_rel"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    function getDatosPermiso_lectura():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('permiso_lectura');
        $oDatosCampo->setMetodoGet('getPermiso_lectura');
        $oDatosCampo->setMetodoSet('setPermiso_lectura');
        $oDatosCampo->setEtiqueta(_("permiso de lectura"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    function getDatosPermiso_escritura():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('permiso_escritura');
        $oDatosCampo->setMetodoGet('getPermiso_escritura');
        $oDatosCampo->setMetodoSet('setPermiso_escritura');
        $oDatosCampo->setEtiqueta(_("permiso de escritura"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    function getDatosDepende_modificar():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('depende_modificar');
        $oDatosCampo->setMetodoGet('IsDepende_modificar');
        $oDatosCampo->setMetodoSet('setDepende_modificar');
        $oDatosCampo->setEtiqueta(_("depende modificar"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosApp(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('app');
        $oDatosCampo->setMetodoGet('getApp');
        $oDatosCampo->setMetodoSet('setApp');
        $oDatosCampo->setEtiqueta(_("app"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(20);
        return $oDatosCampo;
    }

    function getDatosClass(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('Class');
        $oDatosCampo->setMetodoGet('getClass');
        $oDatosCampo->setMetodoSet('setClass');
        $oDatosCampo->setEtiqueta(_("class"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(20);
        return $oDatosCampo;
    }

    function getDatosDb(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('db');
        $oDatosCampo->setMetodoGet('getDb');
        $oDatosCampo->setMetodoSet('setDb');
        $oDatosCampo->setEtiqueta(_("db"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(1);
        return $oDatosCampo;
    }
}