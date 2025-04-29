<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad i_documentos_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class Documento
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_doc de Documento
     *
     * @var int
     */
    private int $iid_doc;
    /**
     * Id_tipo_doc de Documento
     *
     * @var int
     */
    private int $iid_tipo_doc;
    /**
     * Id_ubi de Documento
     *
     * @var int
     */
    private int $iid_ubi;
    /**
     * Id_lugar de Documento
     *
     * @var int|null
     */
    private int|null $iid_lugar = null;
    /**
     * F_recibido de Documento
     *
     * @var DateTimeLocal|NullDateTimeLocal|null
     */
    private DateTimeLocal|NullDateTimeLocal|null $df_recibido = null;
    /**
     * F_asignado de Documento
     *
     * @var DateTimeLocal|NullDateTimeLocal|null
     */
    private DateTimeLocal|NullDateTimeLocal|null $df_asignado = null;
    /**
     * Observ de Documento
     *
     * @var string|null
     */
    private string|null $sobserv = null;
    /**
     * ObservCtr de Documento
     *
     * @var string|null
     */
    private string|null $sobservCtr = null;
    /**
     * F_ult_comprobacion de Documento
     *
     * @var DateTimeLocal|NullDateTimeLocal|null
     */
    private DateTimeLocal|NullDateTimeLocal|null $df_ult_comprobacion = null;
    /**
     * En_busqueda de Documento
     *
     * @var bool|null
     */
    private bool|null $ben_busqueda = null;
    /**
     * Perdido de Documento
     *
     * @var bool|null
     */
    private bool|null $bperdido = null;
    /**
     * F_perdido de Documento
     *
     * @var DateTimeLocal|NullDateTimeLocal|null
     */
    private DateTimeLocal|NullDateTimeLocal|null $df_perdido = null;
    /**
     * Eliminado de Documento
     *
     * @var bool|null
     */
    private bool|null $beliminado = null;
    /**
     * F_eliminado de Documento
     *
     * @var DateTimeLocal|NullDateTimeLocal|null
     */
    private DateTimeLocal|NullDateTimeLocal|null $df_eliminado = null;
    /**
     * Num_reg de Documento
     *
     * @var int|null
     */
    private int|null $inum_reg = null;
    /**
     * Num_ini de Documento
     *
     * @var int|null
     */
    private int|null $inum_ini = null;
    /**
     * Num_fin de Documento
     *
     * @var int|null
     */
    private int|null $inum_fin = null;
    /**
     * Identificador de Documento
     *
     * @var string|null
     */
    private string|null $sidentificador = null;
    /**
     * Num_ejemplares de Documento
     *
     * @var int|null
     */
    private int|null $inum_ejemplares = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Documento
     */
    public function setAllAttributes(array $aDatos): Documento
    {
        if (array_key_exists('id_doc', $aDatos)) {
            $this->setId_doc($aDatos['id_doc']);
        }
        if (array_key_exists('id_tipo_doc', $aDatos)) {
            $this->setId_tipo_doc($aDatos['id_tipo_doc']);
        }
        if (array_key_exists('id_ubi', $aDatos)) {
            $this->setId_ubi($aDatos['id_ubi']);
        }
        if (array_key_exists('id_lugar', $aDatos)) {
            $this->setId_lugar($aDatos['id_lugar']);
        }
        if (array_key_exists('f_recibido', $aDatos)) {
            $this->setF_recibido($aDatos['f_recibido']);
        }
        if (array_key_exists('f_asignado', $aDatos)) {
            $this->setF_asignado($aDatos['f_asignado']);
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObserv($aDatos['observ']);
        }
        if (array_key_exists('observ_ctr', $aDatos)) {
            $this->setObservCtr($aDatos['observ_ctr']);
        }
        if (array_key_exists('f_ult_comprobacion', $aDatos)) {
            $this->setF_ult_comprobacion($aDatos['f_ult_comprobacion']);
        }
        if (array_key_exists('en_busqueda', $aDatos)) {
            $this->setEn_busqueda(is_true($aDatos['en_busqueda']));
        }
        if (array_key_exists('perdido', $aDatos)) {
            $this->setPerdido(is_true($aDatos['perdido']));
        }
        if (array_key_exists('f_perdido', $aDatos)) {
            $this->setF_perdido($aDatos['f_perdido']);
        }
        if (array_key_exists('eliminado', $aDatos)) {
            $this->setEliminado(is_true($aDatos['eliminado']));
        }
        if (array_key_exists('f_eliminado', $aDatos)) {
            $this->setF_eliminado($aDatos['f_eliminado']);
        }
        if (array_key_exists('num_reg', $aDatos)) {
            $this->setNum_reg($aDatos['num_reg']);
        }
        if (array_key_exists('num_ini', $aDatos)) {
            $this->setNum_ini($aDatos['num_ini']);
        }
        if (array_key_exists('num_fin', $aDatos)) {
            $this->setNum_fin($aDatos['num_fin']);
        }
        if (array_key_exists('identificador', $aDatos)) {
            $this->setIdentificador($aDatos['identificador']);
        }
        if (array_key_exists('num_ejemplares', $aDatos)) {
            $this->setNum_ejemplares($aDatos['num_ejemplares']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_doc
     */
    public function getId_doc(): int
    {
        return $this->iid_doc;
    }

    /**
     *
     * @param int $iid_doc
     */
    public function setId_doc(int $iid_doc): void
    {
        $this->iid_doc = $iid_doc;
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
     * @return int $iid_ubi
     */
    public function getId_ubi(): int
    {
        return $this->iid_ubi;
    }

    /**
     *
     * @param int $iid_ubi
     */
    public function setId_ubi(int $iid_ubi): void
    {
        $this->iid_ubi = $iid_ubi;
    }

    /**
     *
     * @return int|null $iid_lugar
     */
    public function getId_lugar(): ?int
    {
        return $this->iid_lugar;
    }

    /**
     *
     * @param int|null $iid_lugar
     */
    public function setId_lugar(?int $iid_lugar = null): void
    {
        $this->iid_lugar = $iid_lugar;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_recibido
     */
    public function getF_recibido(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_recibido ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|NullDateTimeLocal|null $df_recibido
     */
    public function setF_recibido(DateTimeLocal|NullDateTimeLocal|null $df_recibido = null): void
    {
        $this->df_recibido = $df_recibido;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_asignado
     */
    public function getF_asignado(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_asignado ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|NullDateTimeLocal|null $df_asignado
     */
    public function setF_asignado(DateTimeLocal|NullDateTimeLocal|null $df_asignado = null): void
    {
        $this->df_asignado = $df_asignado;
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
     * @return string|null $sobserv_ctr
     */
    public function getObservCtr(): ?string
    {
        return $this->sobservCtr;
    }

    /**
     *
     * @param string|null $sobservCtr
     */
    public function setObservCtr(?string $sobservCtr = null): void
    {
        $this->sobservCtr = $sobservCtr;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_ult_comprobacion
     */
    public function getF_ult_comprobacion(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_ult_comprobacion ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|NullDateTimeLocal|null $df_ult_comprobacion
     */
    public function setF_ult_comprobacion(DateTimeLocal|NullDateTimeLocal|null $df_ult_comprobacion = null): void
    {
        $this->df_ult_comprobacion = $df_ult_comprobacion;
    }

    /**
     *
     * @return bool|null $ben_busqueda
     */
    public function isEn_busqueda(): ?bool
    {
        return $this->ben_busqueda;
    }

    /**
     *
     * @param bool|null $ben_busqueda
     */
    public function setEn_busqueda(?bool $ben_busqueda = null): void
    {
        $this->ben_busqueda = $ben_busqueda;
    }

    /**
     *
     * @return bool|null $bperdido
     */
    public function isPerdido(): ?bool
    {
        return $this->bperdido;
    }

    /**
     *
     * @param bool|null $bperdido
     */
    public function setPerdido(?bool $bperdido = null): void
    {
        $this->bperdido = $bperdido;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_perdido
     */
    public function getF_perdido(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_perdido ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|NullDateTimeLocal|null $df_perdido
     */
    public function setF_perdido(DateTimeLocal|NullDateTimeLocal|null $df_perdido = null): void
    {
        $this->df_perdido = $df_perdido;
    }

    /**
     *
     * @return bool|null $beliminado
     */
    public function isEliminado(): ?bool
    {
        return $this->beliminado;
    }

    /**
     *
     * @param bool|null $beliminado
     */
    public function setEliminado(?bool $beliminado = null): void
    {
        $this->beliminado = $beliminado;
    }

    /**
     *
     * @return DateTimeLocal|NullDateTimeLocal|null $df_eliminado
     */
    public function getF_eliminado(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_eliminado ?? new NullDateTimeLocal;
    }

    /**
     *
     * @param DateTimeLocal|NullDateTimeLocal|null $df_eliminado
     */
    public function setF_eliminado(DateTimeLocal|NullDateTimeLocal|null $df_eliminado = null): void
    {
        $this->df_eliminado = $df_eliminado;
    }

    /**
     *
     * @return int|null $inum_reg
     */
    public function getNum_reg(): ?int
    {
        return $this->inum_reg;
    }

    /**
     *
     * @param int|null $inum_reg
     */
    public function setNum_reg(?int $inum_reg = null): void
    {
        $this->inum_reg = $inum_reg;
    }

    /**
     *
     * @return int|null $inum_ini
     */
    public function getNum_ini(): ?int
    {
        return $this->inum_ini;
    }

    /**
     *
     * @param int|null $inum_ini
     */
    public function setNum_ini(?int $inum_ini = null): void
    {
        $this->inum_ini = $inum_ini;
    }

    /**
     *
     * @return int|null $inum_fin
     */
    public function getNum_fin(): ?int
    {
        return $this->inum_fin;
    }

    /**
     *
     * @param int|null $inum_fin
     */
    public function setNum_fin(?int $inum_fin = null): void
    {
        $this->inum_fin = $inum_fin;
    }

    /**
     *
     * @return string|null $sidentificador
     */
    public function getIdentificador(): ?string
    {
        return $this->sidentificador;
    }

    /**
     *
     * @param string|null $sidentificador
     */
    public function setIdentificador(?string $sidentificador = null): void
    {
        $this->sidentificador = $sidentificador;
    }

    /**
     *
     * @return int|null $inum_ejemplares
     */
    public function getNum_ejemplares(): ?int
    {
        return $this->inum_ejemplares;
    }

    /**
     *
     * @param int|null $inum_ejemplares
     */
    public function setNum_ejemplares(?int $inum_ejemplares = null): void
    {
        $this->inum_ejemplares = $inum_ejemplares;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_doc';
    }

    function getDatosCampos()
    {
        $oDocumentoSet = new Set();

        $oDocumentoSet->add($this->getDatosId_ubi());
        $oDocumentoSet->add($this->getDatosId_tipo_doc());
        $oDocumentoSet->add($this->getDatosId_lugar());
        $oDocumentoSet->add($this->getDatosF_recibido());
        $oDocumentoSet->add($this->getDatosF_asignado());
        $oDocumentoSet->add($this->getDatosObserv());
        $oDocumentoSet->add($this->getDatosObservCtr());
        $oDocumentoSet->add($this->getDatosF_ult_comprobacion());
        $oDocumentoSet->add($this->getDatosEn_busqueda());
        $oDocumentoSet->add($this->getDatosPerdido());
        $oDocumentoSet->add($this->getDatosF_perdido());
        $oDocumentoSet->add($this->getDatosEliminado());
        $oDocumentoSet->add($this->getDatosF_eliminado());
        $oDocumentoSet->add($this->getDatosNum_reg());
        $oDocumentoSet->add($this->getDatosNum_ini());
        $oDocumentoSet->add($this->getDatosNum_fin());
        $oDocumentoSet->add($this->getDatosIdentificador());
        $oDocumentoSet->add($this->getDatosNum_ejemplares());
        return $oDocumentoSet->getTot();
    }


    function getDatosId_tipo_doc()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_doc');
        $oDatosCampo->setMetodoGet('getId_tipo_doc');
        $oDatosCampo->setMetodoSet('setId_tipo_doc');
        $oDatosCampo->setEtiqueta(_("documento tipo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('src\\inventario\\domain\\repositories\\TipoDocRepository');
        $oDatosCampo->setArgument2('getSigla');
        $oDatosCampo->setArgument3('getArrayTipoDoc');
        return $oDatosCampo;
    }

    function getDatosId_ubi()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_ubi');
        $oDatosCampo->setMetodoGet('getId_ubi');
        $oDatosCampo->setMetodoSet('setId_ubi');
        $oDatosCampo->setEtiqueta(_("centro/casa"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('src\\inventario\\domain\\repositories\\UbiInventarioRepository');
        $oDatosCampo->setArgument2('getNom_ubi');
        $oDatosCampo->setArgument3('getArrayUbisInventario');
        $oDatosCampo->setAccion('id_lugar'); // campo que hay que actualizar al cambiar este.
        return $oDatosCampo;
    }

    function getDatosId_lugar()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_lugar');
        $oDatosCampo->setMetodoGet('getId_lugar');
        $oDatosCampo->setMetodoSet('setId_lugar');
        $oDatosCampo->setEtiqueta(_("lugar"));
        $oDatosCampo->setTipo('depende');
        $oDatosCampo->setArgument('src\\inventario\\domain\\repositories\\LugarRepository');
        $oDatosCampo->setArgument2('getNom_lugar');
        $oDatosCampo->setArgument3('getArrayLugares');
        $oDatosCampo->setDepende('id_ubi');
        return $oDatosCampo;
    }

    function getDatosF_recibido()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_recibido');
        $oDatosCampo->setMetodoGet('getF_recibido');
        $oDatosCampo->setMetodoSet('setF_recibido');
        $oDatosCampo->setEtiqueta(_("fecha recibido"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosF_asignado()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_asignado');
        $oDatosCampo->setMetodoGet('getF_asignado');
        $oDatosCampo->setMetodoSet('setF_asignado');
        $oDatosCampo->setEtiqueta(_("fecha asignado"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosObserv()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('observ');
        $oDatosCampo->setMetodoGet('getObserv');
        $oDatosCampo->setMetodoSet('setObserv');
        $oDatosCampo->setEtiqueta(_("observaciones"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    function getDatosObservCtr()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('observ_ctr');
        $oDatosCampo->setMetodoGet('getObservCtr');
        $oDatosCampo->setMetodoSet('setObservCtr');
        $oDatosCampo->setEtiqueta(_("observaciones para el centro"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    function getDatosF_ult_comprobacion()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_ult_comprobacion');
        $oDatosCampo->setMetodoGet('getF_ult_comprobacion');
        $oDatosCampo->setMetodoSet('setF_ult_comprobacion');
        $oDatosCampo->setEtiqueta(_("fecha última comprobación"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosEn_busqueda()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('en_busqueda');
        $oDatosCampo->setMetodoGet('isEn_busqueda');
        $oDatosCampo->setMetodoSet('setEn_busqueda');
        $oDatosCampo->setEtiqueta(_("en búsqueda"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosPerdido()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('perdido');
        $oDatosCampo->setMetodoGet('isPerdido');
        $oDatosCampo->setMetodoSet('setPerdido');
        $oDatosCampo->setEtiqueta(_("perdido"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosF_perdido()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_perdido');
        $oDatosCampo->setMetodoGet('getF_perdido');
        $oDatosCampo->setMetodoSet('setF_perdido');
        $oDatosCampo->setEtiqueta(_("fecha perdido"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosEliminado()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('eliminado');
        $oDatosCampo->setMetodoGet('isEliminado');
        $oDatosCampo->setMetodoSet('setEliminado');
        $oDatosCampo->setEtiqueta(_("eliminado"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosF_eliminado()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_eliminado');
        $oDatosCampo->setMetodoGet('getF_eliminado');
        $oDatosCampo->setMetodoSet('setF_eliminado');
        $oDatosCampo->setEtiqueta(_("fecha eliminado"));
        $oDatosCampo->setTipo('fecha');
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosNum_reg()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('num_reg');
        $oDatosCampo->setMetodoGet('getNum_reg');
        $oDatosCampo->setMetodoSet('setNum_reg');
        $oDatosCampo->setEtiqueta(_("número de registro"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }

    function getDatosNum_ini()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('num_ini');
        $oDatosCampo->setMetodoGet('getNum_ini');
        $oDatosCampo->setMetodoSet('setNum_ini');
        $oDatosCampo->setEtiqueta(_("número inicial de la colección"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }

    function getDatosNum_fin()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('num_fin');
        $oDatosCampo->setMetodoGet('getNum_fin');
        $oDatosCampo->setMetodoSet('setNum_fin');
        $oDatosCampo->setEtiqueta(_("número final de la colección"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }

    function getDatosIdentificador()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('identificador');
        $oDatosCampo->setMetodoGet('getIdentificador');
        $oDatosCampo->setMetodoSet('setIdentificador');
        $oDatosCampo->setEtiqueta(_("identificador"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(25);
        return $oDatosCampo;
    }

    function getDatosNum_ejemplares()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('num_ejemplares');
        $oDatosCampo->setMetodoGet('getNum_ejemplares');
        $oDatosCampo->setMetodoSet('setNum_ejemplares');
        $oDatosCampo->setEtiqueta(_("número de ejemplares"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }

}