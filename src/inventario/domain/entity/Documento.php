<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;
use src\inventario\domain\value_objects\{DocumentoId, TipoDocId, UbiInventarioId, LugarId,
    DocumentoObserv, DocumentoObservCtr, DocumentoIdentificador,
    DocumentoNumReg, DocumentoNumIni, DocumentoNumFin, DocumentoNumEjemplares,
    DocumentoEnBusqueda, DocumentoPerdido, DocumentoEliminado};

class Documento
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_doc;

    private int $id_tipo_doc;

    private int $id_ubi;

    private int|null $id_lugar = null;

    private DateTimeLocal|NullDateTimeLocal|null $f_recibido = null;

    private DateTimeLocal|NullDateTimeLocal|null $f_asignado = null;

    private string|null $observ = null;

    private string|null $observCtr = null;

    private DateTimeLocal|NullDateTimeLocal|null $f_ult_comprobacion = null;

    private bool|null $en_busqueda = null;

    private bool|null $perdido = null;

    private DateTimeLocal|NullDateTimeLocal|null $f_perdido = null;

    private bool|null $eliminado = null;

    private DateTimeLocal|NullDateTimeLocal|null $f_eliminado = null;

    private int|null $num_reg = null;

    private int|null $num_ini = null;

    private int|null $num_fin = null;

    private string|null $identificador = null;

    private int|null $num_ejemplares = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_doc(): int
    {
        return $this->id_doc;
    }


    public function setId_doc(int $id_doc): void
    {
        $this->id_doc = $id_doc;
    }


    public function getId_tipo_doc(): int
    {
        return $this->id_tipo_doc;
    }


    public function setId_tipo_doc(int $id_tipo_doc): void
    {
        $this->id_tipo_doc = $id_tipo_doc;
    }


    public function getId_ubi(): int
    {
        return $this->id_ubi;
    }


    public function setId_ubi(int $id_ubi): void
    {
        $this->id_ubi = $id_ubi;
    }


    public function getId_lugar(): ?int
    {
        return $this->id_lugar;
    }


    public function setId_lugar(?int $id_lugar = null): void
    {
        $this->id_lugar = $id_lugar;
    }

    public function getF_recibido(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_recibido ?? new NullDateTimeLocal;
    }


    public function setF_recibido(DateTimeLocal|NullDateTimeLocal|null $f_recibido = null): void
    {
        $this->f_recibido = $f_recibido instanceof NullDateTimeLocal ? null : $f_recibido;
    }

    public function getF_asignado(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_asignado ?? new NullDateTimeLocal;
    }


    public function setF_asignado(DateTimeLocal|NullDateTimeLocal|null $f_asignado = null): void
    {
        $this->f_asignado = $f_asignado instanceof NullDateTimeLocal ? null : $f_asignado;
    }


    public function getObserv(): ?string
    {
        return $this->observ;
    }


    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }


    public function getObservCtr(): ?string
    {
        return $this->observCtr;
    }


    public function setObservCtr(?string $observCtr = null): void
    {
        $this->observCtr = $observCtr;
    }


    public function getF_ult_comprobacion(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_ult_comprobacion ?? new NullDateTimeLocal;
    }


    public function setF_ult_comprobacion(DateTimeLocal|NullDateTimeLocal|null $f_ult_comprobacion = null): void
    {
        $this->f_ult_comprobacion = $f_ult_comprobacion instanceof NullDateTimeLocal ? null : $f_ult_comprobacion;
    }

    /**
     * @deprecated Usar `isEnBusqueda(): ?bool` en su lugar.
     */
    public function isEn_busqueda(): ?bool
    {
        return $this->isEnBusqueda();
    }

    public function isEnBusqueda(): ?bool
    {
        return $this->en_busqueda;
    }


    /**
     * @deprecated Usar `setEnBusqueda(?bool $enBusqueda = null): void` en su lugar.
     */
    public function setEn_busqueda(?bool $en_busqueda = null): void
    {
        $this->setEnBusqueda($en_busqueda);
    }

    public function setEnBusqueda(?bool $enBusqueda = null): void
    {
        $this->en_busqueda = $enBusqueda;
    }


    public function isPerdido(): ?bool
    {
        return $this->perdido;
    }


    public function setPerdido(?bool $perdido = null): void
    {
        $this->perdido = $perdido;
    }


    public function getF_perdido(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_perdido ?? new NullDateTimeLocal;
    }


    public function setF_perdido(DateTimeLocal|NullDateTimeLocal|null $f_perdido = null): void
    {
        $this->f_perdido = $f_perdido instanceof NullDateTimeLocal ? null : $f_perdido;
    }


    public function isEliminado(): ?bool
    {
        return $this->eliminado;
    }


    public function setEliminado(?bool $eliminado = null): void
    {
        $this->eliminado = $eliminado;
    }


    public function getF_eliminado(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_eliminado ?? new NullDateTimeLocal;
    }


    public function setF_eliminado(DateTimeLocal|NullDateTimeLocal|null $f_eliminado = null): void
    {
        $this->f_eliminado = $f_eliminado instanceof NullDateTimeLocal ? null : $f_eliminado;
    }


    public function getNum_reg(): ?int
    {
        return $this->num_reg;
    }


    public function setNum_reg(?int $num_reg = null): void
    {
        $this->num_reg = $num_reg;
    }


    public function getNum_ini(): ?int
    {
        return $this->num_ini;
    }


    public function setNum_ini(?int $num_ini = null): void
    {
        $this->num_ini = $num_ini;
    }


    public function getNum_fin(): ?int
    {
        return $this->num_fin;
    }


    public function setNum_fin(?int $num_fin = null): void
    {
        $this->num_fin = $num_fin;
    }


    public function getIdentificador(): ?string
    {
        return $this->identificador;
    }


    public function setIdentificador(?string $identificador = null): void
    {
        $this->identificador = $identificador;
    }


    public function getNum_ejemplares(): ?int
    {
        return $this->num_ejemplares;
    }


    public function setNum_ejemplares(?int $num_ejemplares = null): void
    {
        $this->num_ejemplares = $num_ejemplares;
    }

    // Value Object API (duplicada con legacy)
    public function getIdDocVo(): DocumentoId
    {
        return new DocumentoId($this->id_doc);
    }

    public function setIdDocVo(?DocumentoId $id = null): void
    {
        if ($id === null) { return; }
        $this->id_doc = $id->value();
    }

    public function getIdTipoDocVo(): TipoDocId
    {
        return new TipoDocId($this->id_tipo_doc);
    }

    public function setIdTipoDocVo(?TipoDocId $id = null): void
    {
        if ($id === null) { return; }
        $this->id_tipo_doc = $id->value();
    }

    public function getIdUbiVo(): UbiInventarioId
    {
        return new UbiInventarioId($this->id_ubi);
    }

    public function setIdUbiVo(?UbiInventarioId $id = null): void
    {
        if ($id === null) { return; }
        $this->id_ubi = $id->value();
    }

    public function getIdLugarVo(): ?LugarId
    {
        return $this->id_lugar !== null ? new LugarId($this->id_lugar) : null;
    }

    public function setIdLugarVo(?LugarId $id = null): void
    {
        $this->id_lugar = $id?->value();
    }

    public function getObservVo(): ?DocumentoObserv
    {
        return DocumentoObserv::fromNullableString($this->observ);
    }

    public function setObservVo(?DocumentoObserv $obs = null): void
    {
        $this->observ = $obs?->value();
    }

    public function getObservCtrVo(): ?DocumentoObservCtr
    {
        return DocumentoObservCtr::fromNullableString($this->observCtr);
    }

    public function setObservCtrVo(?DocumentoObservCtr $obs = null): void
    {
        $this->observCtr = $obs?->value();
    }

    public function getIdentificadorVo(): ?DocumentoIdentificador
    {
        return DocumentoIdentificador::fromNullableString($this->identificador);
    }

    public function setIdentificadorVo(?DocumentoIdentificador $ident = null): void
    {
        $this->identificador = $ident?->value();
    }

    public function getNumRegVo(): ?DocumentoNumReg
    {
        return $this->num_reg !== null ? new DocumentoNumReg($this->num_reg) : null;
    }

    public function setNumRegVo(?DocumentoNumReg $num = null): void
    {
        $this->num_reg = $num?->value();
    }

    public function getNumIniVo(): ?DocumentoNumIni
    {
        return $this->num_ini !== null ? new DocumentoNumIni($this->num_ini) : null;
    }

    public function setNumIniVo(?DocumentoNumIni $num = null): void
    {
        $this->num_ini = $num?->value();
    }

    public function getNumFinVo(): ?DocumentoNumFin
    {
        return $this->num_fin !== null ? new DocumentoNumFin($this->num_fin) : null;
    }

    public function setNumFinVo(?DocumentoNumFin $num = null): void
    {
        $this->num_fin = $num?->value();
    }

    public function getNumEjemplaresVo(): ?DocumentoNumEjemplares
    {
        return $this->num_ejemplares !== null ? new DocumentoNumEjemplares($this->num_ejemplares) : null;
    }

    public function setNumEjemplaresVo(?DocumentoNumEjemplares $num = null): void
    {
        $this->num_ejemplares = $num?->value();
    }

    public function getEnBusquedaVo(): ?DocumentoEnBusqueda
    {
        return $this->en_busqueda === null ? null : new DocumentoEnBusqueda((bool)$this->en_busqueda);
    }

    public function setEnBusquedaVo(?DocumentoEnBusqueda $flag = null): void
    {
        $this->en_busqueda = $flag?->value();
    }

    public function getPerdidoVo(): ?DocumentoPerdido
    {
        return $this->perdido === null ? null : new DocumentoPerdido((bool)$this->perdido);
    }

    public function setPerdidoVo(?DocumentoPerdido $flag = null): void
    {
        $this->perdido = $flag?->value();
    }

    public function getEliminadoVo(): ?DocumentoEliminado
    {
        return $this->eliminado === null ? null : new DocumentoEliminado((bool)$this->eliminado);
    }

    public function setEliminadoVo(?DocumentoEliminado $flag = null): void
    {
        $this->eliminado = $flag?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_doc';
    }

    public function getDatosCampos(): array
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

    private function getDatosId_tipo_doc(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_tipo_doc');
        $oDatosCampo->setMetodoGet('getId_tipo_doc');
        $oDatosCampo->setMetodoSet('setId_tipo_doc');
        $oDatosCampo->setEtiqueta(_("documento tipo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(TipoDocRepositoryInterface::class);
        $oDatosCampo->setArgument2('getSigla');
        $oDatosCampo->setArgument3('getArrayTipoDoc');
        return $oDatosCampo;
    }

    private function getDatosId_ubi(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_ubi');
        $oDatosCampo->setMetodoGet('getId_ubi');
        $oDatosCampo->setMetodoSet('setId_ubi');
        $oDatosCampo->setEtiqueta(_("centro/casa"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(UbiInventarioRepositoryInterface::class);
        $oDatosCampo->setArgument2('getNom_ubi');
        $oDatosCampo->setArgument3('getArrayUbisInventario');
        $oDatosCampo->setAccion('id_lugar'); // campo que hay que actualizar al cambiar este.
        return $oDatosCampo;
    }

    private function getDatosId_lugar(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_lugar');
        $oDatosCampo->setMetodoGet('getId_lugar');
        $oDatosCampo->setMetodoSet('setId_lugar');
        $oDatosCampo->setEtiqueta(_("lugar"));
        $oDatosCampo->setTipo('depende');
        $oDatosCampo->setArgument(LugarRepositoryInterface::class);
        $oDatosCampo->setArgument2('getNom_lugar');
        $oDatosCampo->setArgument3('getArrayLugares');
        $oDatosCampo->setDepende('id_ubi');
        return $oDatosCampo;
    }

    private function getDatosF_recibido(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_recibido');
        $oDatosCampo->setMetodoGet('getF_recibido');
        $oDatosCampo->setMetodoSet('setF_recibido');
        $oDatosCampo->setEtiqueta(_("fecha recibido"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosF_asignado(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_asignado');
        $oDatosCampo->setMetodoGet('getF_asignado');
        $oDatosCampo->setMetodoSet('setF_asignado');
        $oDatosCampo->setEtiqueta(_("fecha asignado"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosObserv(): DatosCampo
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

    private function getDatosObservCtr(): DatosCampo
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

    private function getDatosF_ult_comprobacion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_ult_comprobacion');
        $oDatosCampo->setMetodoGet('getF_ult_comprobacion');
        $oDatosCampo->setMetodoSet('setF_ult_comprobacion');
        $oDatosCampo->setEtiqueta(_("fecha última comprobación"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosEn_busqueda(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('en_busqueda');
        $oDatosCampo->setMetodoGet('isEn_busqueda');
        $oDatosCampo->setMetodoSet('setEn_busqueda');
        $oDatosCampo->setEtiqueta(_("en búsqueda"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosPerdido(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('perdido');
        $oDatosCampo->setMetodoGet('isPerdido');
        $oDatosCampo->setMetodoSet('setPerdido');
        $oDatosCampo->setEtiqueta(_("perdido"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosF_perdido(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_perdido');
        $oDatosCampo->setMetodoGet('getF_perdido');
        $oDatosCampo->setMetodoSet('setF_perdido');
        $oDatosCampo->setEtiqueta(_("fecha perdido"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosEliminado(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('eliminado');
        $oDatosCampo->setMetodoGet('isEliminado');
        $oDatosCampo->setMetodoSet('setEliminado');
        $oDatosCampo->setEtiqueta(_("eliminado"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosF_eliminado(): DatosCampo
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

    private function getDatosNum_reg(): DatosCampo
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

    private function getDatosNum_ini(): DatosCampo
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

    private function getDatosNum_fin(): DatosCampo
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

    private function getDatosIdentificador(): DatosCampo
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

    private function getDatosNum_ejemplares(): DatosCampo
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