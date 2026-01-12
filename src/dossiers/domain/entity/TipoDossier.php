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

    private ?TipoDossierDescripcion $descripcion = null;

    private TipoDossierTablaFrom $tabla_from;

    private ?TipoDossierTablaTo $tabla_to = null;

    private ?TipoDossierCampoTo $campo_to = null;

    private ?int $id_tipo_dossier_rel = null;

    private ?int $permiso_lectura;

    private ?int $permiso_escritura = null;

    private ?bool $depende_modificar;

    private ?TipoDossierApp $app = null;

    private ?TipoDossierClass $class = null;

    private ?TipoDossierDb $db = null;

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
        return $this->descripcion;
    }


    public function setDescripcionVo(TipoDossierDescripcion|string|null $texto = null): void
    {
        $this->descripcion = $texto instanceof TipoDossierDescripcion
            ? $texto
            : TipoDossierDescripcion::fromNullableString($texto);
    }


    /**
     * @deprecated use getDescripcionVo()
     */
    public function getDescripcion(): ?string
    {
        return $this->descripcion?->value();
    }

    /**
     * @deprecated use setDescripcionVo()
     */
    public function setDescripcion(?string $descripcion = null): void
    {
        $this->descripcion = TipodossierDescripcion::fromNullableString($descripcion);
    }

    /**
     * @return TipoDossierTablaFrom
     */
    public function getTablaFromVo(): TipoDossierTablaFrom
    {
        return $this->tabla_from;
    }


    public function setTablaFromVo(TipoDossierTablaFrom|string|null $texto): void
    {
        $this->tabla_from = $texto instanceof TipoDossierTablaFrom
            ? $texto
            : TipoDossierTablaFrom::fromNullableString($texto);
    }

    /**
     * @deprecated use getTablaFromVo()
     */
    public function getTabla_from(): string
    {
        return $this->tabla_from->value();
    }

    /**
     * @deprecated use setTablaFromVo()
     */
    public function setTabla_from(string $tabla_from): void
    {
        $this->tabla_from = TipoDossierTablaFrom::fromNullableString($tabla_from);
    }

    /**
     * @return TipoDossierTablaTo|null
     */
    public function getTablaToVo(): ?TipoDossierTablaTo
    {
        return $this->tabla_to;
    }


    public function setTablaToVo(TipoDossierTablaTo|string|null $texto = null): void
    {
        $this->tabla_to = $texto instanceof TipoDossierTablaTo
            ? $texto
            : TipoDossierTablaTo::fromNullableString($texto);
    }

    /**
     * @deprecated use getTablaToVo()
     */
    public function getTabla_to(): ?string
    {
        return $this->tabla_to?->value();
    }

    /**
     * @deprecated use setTablaToVo()
     */
    public function setTabla_to(?string $tabla_to = null): void
    {
        $this->tabla_to = TipoDossierTablaTo::fromNullableString($tabla_to);
    }

    /**
     * @return TipoDossierCampoTo|null
     */
    public function getCampoToVo(): ?TipoDossierCampoTo
    {
        return $this->campo_to;
    }


    public function setCampoToVo(TipoDossierCampoTo|string|null $texto = null): void
    {
        $this->campo_to = $texto instanceof TipoDossierCampoTo
            ? $texto
            : TipoDossierCampoTo::fromNullableString($texto);
    }

    /**
     * @deprecated use getCampoToVo()
     */
    public function getCampo_to(): ?string
    {
        return $this->campo_to?->value();
    }

    /**
     * @deprecated use setCampoToVo()
     */
    public function setCampo_to(?string $campo_to = null): void
    {
        $this->campo_to = TipoDossierCampoTo::fromNullableString($campo_to);
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
        return $this->app;
    }


    public function setAppVo(TipoDossierApp|string|null $texto = null): void
    {
        $this->app = $texto instanceof TipoDossierApp
            ? $texto
            : TipoDossierApp::fromNullableString($texto);
    }

    /**
     * @deprecated use getAppVo()
     */
    public function getApp(): ?string
    {
        return $this->app?->value();
    }

    /**
     * @deprecated use setAppVo()
     */
    public function setApp(?string $app = null): void
    {
        $this->app = TipoDossierApp::fromNullableString($app);
    }

    /**
     * @return TipoDossierClass|null
     */
    public function getClassVo(): ?TipoDossierClass
    {
        return $this->class;
    }

    /**
     * @param TipoDossierClass|null $oTipoDossierClass
     */
    public function setClassVo(TipoDossierClass|string|null $texto = null): void
    {
        $this->class = $texto instanceof TipoDossierClass
            ? $texto
            : TipoDossierClass::fromNullableString($texto);
    }

    /**
     * @deprecated use getClassVo()
     */
    public function getClass(): ?string
    {
        return $this->class?->value();
    }

    /**
     * @deprecated use setClassVo()
     */
    public function setClass(?string $class = null): void
    {
        $this->class = TipoDossierClass::fromNullableString($class);
    }

    /**
     * @return TipoDossierDb|null
     */
    public function getDbVo(): ?TipoDossierDb
    {
        return $this->db;
    }


    public function setDbVo(TipoDossierDb|int|null $valor = null): void
    {
        $this->db = $valor instanceof TipoDossierDb
            ? $valor
            : TipoDossierDb::fromNullableInt($valor);
    }

    /**
     * @deprecated use getDbVo()
     */
    public function getDb(): ?string
    {
        return $this->db?->value();
    }

    /**
     * @deprecated use setDbVo()
     */
    public function setDb(?int $db = null): void
    {
        $this->db = TipoDossierDb::fromNullableInt($db);
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