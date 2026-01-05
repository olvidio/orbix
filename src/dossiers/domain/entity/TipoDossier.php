<?php

namespace src\dossiers\domain\entity;

use core\DatosCampo;
use core\Set;
use src\dossiers\domain\value_objects\TipoDossierApp;
use src\dossiers\domain\value_objects\TipoDossierCampoTo;
use src\dossiers\domain\value_objects\TipoDossierClass;
use src\dossiers\domain\value_objects\TipoDossierDb;
use src\dossiers\domain\value_objects\TipoDossierDescripcion;
use src\dossiers\domain\value_objects\TipoDossierTablaFrom;
use src\dossiers\domain\value_objects\TipoDossierTablaTo;
use src\shared\domain\traits\Hydratable;
use function core\is_true;


class TipoDossier
{
    use Hydratable;

    // db constantes.
    const DB_COMUN = 1; // Base de datos comun
    const DB_INTERIOR = 2; // Base de datos interior (sv)
    const DB_EXTERIOR = 3; // Base de datos exterior (sv-e)

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_tipo_dossier;

    private string|null $descripcion = null;

    private string $tabla_from;

    private string|null $tabla_to = null;

    private string|null $campo_to = null;

    private int|null $id_tipo_dossier_rel = null;

    private int $permiso_lectura;

    private int|null $permiso_escritura = null;

    private bool $depende_modificar;

    private string|null $app = null;

    private string|null $class = null;

    private int|null $db = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_tipo_dossier(): int
    {
        return $this->id_tipo_dossier;
    }


    public function setId_tipo_dossier(int $id_tipo_dossier): void
    {
        $this->id_tipo_dossier = $id_tipo_dossier;
    }


    public function getDescripcionVo(): ?TipoDossierDescripcion
    {
        return $this->descripcion !== null ? new TipoDossierDescripcion($this->descripcion) : null;
    }


    public function setDescripcionVo(?TipoDossierDescripcion $oTipoDossierDescripcion = null): void
    {
        $this->descripcion = $oTipoDossierDescripcion?->value();
    }


    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    /**
     * @deprecated use setDescripcionVo()
     */
    public function setDescripcion(?string $descripcion = null): void
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @return TipoDossierTablaFrom
     */
    public function getTablaFromVo(): TipoDossierTablaFrom
    {
        return new TipoDossierTablaFrom($this->tabla_from);
    }

    /**
     * @param TipoDossierTablaFrom $oTipoDossierTablaFrom
     */
    public function setTablaFromVo(TipoDossierTablaFrom $oTipoDossierTablaFrom): void
    {
        $this->tabla_from = $oTipoDossierTablaFrom->value();
    }

    /**
     * @deprecated use getTablaFromVo()
     */
    public function getTabla_from(): string
    {
        return $this->tabla_from;
    }

    /**
     * @deprecated use setTablaFromVo()
     */
    public function setTabla_from(string $tabla_from): void
    {
        $this->tabla_from = $tabla_from;
    }

    /**
     * @return TipoDossierTablaTo|null
     */
    public function getTablaToVo(): ?TipoDossierTablaTo
    {
        return $this->tabla_to !== null ? new TipoDossierTablaTo($this->tabla_to) : null;
    }

    /**
     * @param TipoDossierTablaTo|null $oTipoDossierTablaTo
     */
    public function setTablaToVo(?TipoDossierTablaTo $oTipoDossierTablaTo = null): void
    {
        $this->tabla_to = $oTipoDossierTablaTo?->value();
    }

    /**
     * @deprecated use getTablaToVo()
     */
    public function getTabla_to(): ?string
    {
        return $this->tabla_to;
    }

    /**
     * @deprecated use setTablaToVo()
     */
    public function setTabla_to(?string $tabla_to = null): void
    {
        $this->tabla_to = $tabla_to;
    }

    /**
     * @return TipoDossierCampoTo|null
     */
    public function getCampoToVo(): ?TipoDossierCampoTo
    {
        return $this->campo_to !== null ? new TipoDossierCampoTo($this->campo_to) : null;
    }

    /**
     * @param TipoDossierCampoTo|null $oTipoDossierCampoTo
     */
    public function setCampoToVo(?TipoDossierCampoTo $oTipoDossierCampoTo = null): void
    {
        $this->campo_to = $oTipoDossierCampoTo?->value();
    }

    /**
     * @deprecated use getCampoToVo()
     */
    public function getCampo_to(): ?string
    {
        return $this->campo_to;
    }

    /**
     * @deprecated use setCampoToVo()
     */
    public function setCampo_to(?string $campo_to = null): void
    {
        $this->campo_to = $campo_to;
    }


    public function getId_tipo_dossier_rel(): ?int
    {
        return $this->id_tipo_dossier_rel;
    }


    public function setId_tipo_dossier_rel(?int $id_tipo_dossier_rel = null): void
    {
        $this->id_tipo_dossier_rel = $id_tipo_dossier_rel;
    }


    public function getPermiso_lectura(): int
    {
        return $this->permiso_lectura;
    }


    public function setPermiso_lectura(int $permiso_lectura): void
    {
        $this->permiso_lectura = $permiso_lectura;
    }


    public function getPermiso_escritura(): ?int
    {
        return $this->permiso_escritura;
    }


    public function setPermiso_escritura(?int $permiso_escritura = null): void
    {
        $this->permiso_escritura = $permiso_escritura;
    }


    public function isDepende_modificar(): bool
    {
        return $this->depende_modificar;
    }


    public function setDepende_modificar(bool $depende_modificar): void
    {
        $this->depende_modificar = $depende_modificar;
    }

    /**
     * @return TipoDossierApp|null
     */
    public function getAppVo(): ?TipoDossierApp
    {
        return $this->app !== null ? new TipoDossierApp($this->app) : null;
    }

    /**
     * @param TipoDossierApp|null $oTipoDossierApp
     */
    public function setAppVo(?TipoDossierApp $oTipoDossierApp = null): void
    {
        $this->app = $oTipoDossierApp?->value();
    }

    /**
     * @deprecated use getAppVo()
     */
    public function getApp(): ?string
    {
        return $this->app;
    }

    /**
     * @deprecated use setAppVo()
     */
    public function setApp(?string $app = null): void
    {
        $this->app = $app;
    }

    /**
     * @return TipoDossierClass|null
     */
    public function getClassVo(): ?TipoDossierClass
    {
        return $this->class !== null ? new TipoDossierClass($this->class) : null;
    }

    /**
     * @param TipoDossierClass|null $oTipoDossierClass
     */
    public function setClassVo(?TipoDossierClass $oTipoDossierClass = null): void
    {
        $this->class = $oTipoDossierClass?->value();
    }

    /**
     * @deprecated use getClassVo()
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @deprecated use setClassVo()
     */
    public function setClass(?string $class = null): void
    {
        $this->class = $class;
    }

    /**
     * @return TipoDossierDb|null
     */
    public function getDbVo(): ?TipoDossierDb
    {
        return $this->db !== null ? new TipoDossierDb($this->db) : null;
    }

    /**
     * @param TipoDossierDb|null $oTipoDossierDb
     */
    public function setDbVo(?TipoDossierDb $oTipoDossierDb = null): void
    {
        $this->db = $oTipoDossierDb?->value();
    }

    /**
     * @deprecated use getDbVo()
     */
    public function getDb(): ?int
    {
        return $this->db;
    }

    /**
     * @deprecated use setDbVo()
     */
    public function setDb(?int $db = null): void
    {
        $this->db = $db;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_tipo_dossier';
    }


    public function getDatosCampos():array
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


    public function getDatosDescripcion():DatosCampo
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

    public function getDatosTabla_from():DatosCampo
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

    public function getDatosTabla_to():DatosCampo
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

    public function getDatosCampo_to():DatosCampo
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

    public function getDatosId_tipo_dossier_rel():DatosCampo
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

    public function getDatosPermiso_lectura():DatosCampo
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

    public function getDatosPermiso_escritura():DatosCampo
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

    public function getDatosDepende_modificar():DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('depende_modificar');
        $oDatosCampo->setMetodoGet('IsDepende_modificar');
        $oDatosCampo->setMetodoSet('setDepende_modificar');
        $oDatosCampo->setEtiqueta(_("depende modificar"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosApp(): DatosCampo
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

    private function getDatosClass(): DatosCampo
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

    private function getDatosDb(): DatosCampo
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