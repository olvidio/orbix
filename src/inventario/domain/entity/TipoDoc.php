<?php

namespace src\inventario\domain\entity;

use core\DatosCampo;
use core\Set;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\shared\domain\traits\Hydratable;
use function core\is_true;
use src\inventario\domain\value_objects\{TipoDocId, TipoDocName, TipoDocSigla, TipoDocObserv, ColeccionId, TipoDocBajoLlave, TipoDocVigente, TipoDocNumerado};


class TipoDoc
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_tipo_doc;

    private string|null $nom_doc = null;

    private string $sigla;

    private string|null $observ = null;

    private int|null $id_coleccion = null;

    private bool|null $bajo_llave = null;

    private bool|null $vigente = null;

    private bool $numerado;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_tipo_doc(): int
    {
        return $this->id_tipo_doc;
    }

    public function setId_tipo_doc(int $id_tipo_doc): void
    {
        $this->id_tipo_doc = $id_tipo_doc;
    }

    public function getNom_doc(): ?string
    {
        return $this->nom_doc;
    }

    public function setNom_doc(?string $nom_doc = null): void
    {
        $this->nom_doc = $nom_doc;
    }

    public function getSigla(): string
    {
        return $this->sigla;
    }

    public function setSigla(string $sigla): void
    {
        $this->sigla = $sigla;
    }

    public function getObserv(): ?string
    {
        return $this->observ;
    }

    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    public function getId_coleccion(): ?int
    {
        return $this->id_coleccion;
    }

    public function setId_coleccion(?int $id_coleccion = null): void
    {
        $this->id_coleccion = $id_coleccion;
    }

    /**
     * @deprecated Usar `isBajoLlave(): ?bool` en su lugar.
     */
    public function isBajo_llave(): ?bool
    {
        return $this->isBajoLlave();
    }

    public function isBajoLlave(): ?bool
    {
        return $this->bajo_llave;
    }


    /**
     * @deprecated Usar `setBajoLlave(?bool $bajoLlave = null): void` en su lugar.
     */
    public function setBajo_llave(?bool $bajo_llave = null): void
    {
        $this->setBajoLlave($bajo_llave);
    }

    public function setBajoLlave(?bool $bajoLlave = null): void
    {
        $this->bajo_llave = $bajoLlave;
    }


    public function isVigente(): ?bool
    {
        return $this->vigente;
    }


    public function setVigente(?bool $vigente = null): void
    {
        $this->vigente = $vigente;
    }


    public function isNumerado(): bool
    {
        return $this->numerado;
    }


    public function setNumerado(?bool $numerado): void
    {
        $this->numerado = $numerado?? False;
    }

    // Value Object API (duplicada con legacy)
    public function getIdTipoDocVo(): TipoDocId
    {
        return new TipoDocId($this->id_tipo_doc);
    }

    public function setIdTipoDocVo(?TipoDocId $id = null): void
    {
        if ($id === null) { return; }
        $this->id_tipo_doc = $id->value();
    }

    public function getNomDocVo(): ?TipoDocName
    {
        return $this->nom_doc !== null && $this->nom_doc !== '' ? new TipoDocName($this->nom_doc) : null;
    }

    public function setNomDocVo(?TipoDocName $name = null): void
    {
        $this->nom_doc = $name?->value();
    }

    public function getSiglaVo(): ?TipoDocSigla
    {
        return isset($this->sigla) && $this->sigla !== '' ? new TipoDocSigla($this->sigla) : null;
    }

    public function setSiglaVo(?TipoDocSigla $sigla = null): void
    {
        $this->sigla = $sigla?->value() ?? '';
    }

    public function getObservVo(): ?TipoDocObserv
    {
        return $this->observ !== null && $this->observ !== '' ? new TipoDocObserv($this->observ) : null;
    }

    public function setObservVo(?TipoDocObserv $obs = null): void
    {
        $this->observ = $obs?->value();
    }

    public function getIdColeccionVo(): ?ColeccionId
    {
        return $this->id_coleccion !== null ? new ColeccionId($this->id_coleccion) : null;
    }

    public function setIdColeccionVo(?ColeccionId $id = null): void
    {
        $this->id_coleccion = $id?->value();
    }

    public function getBajoLlaveVo(): ?TipoDocBajoLlave
    {
        return $this->bajo_llave === null ? null : new TipoDocBajoLlave((bool)$this->bajo_llave);
    }

    public function setBajoLlaveVo(?TipoDocBajoLlave $bajoLlave = null): void
    {
        $this->bajo_llave = $bajoLlave?->value();
    }

    public function getVigenteVo(): ?TipoDocVigente
    {
        return $this->vigente === null ? null : new TipoDocVigente((bool)$this->vigente);
    }

    public function setVigenteVo(?TipoDocVigente $vigente = null): void
    {
        $this->vigente = $vigente?->value();
    }

    public function getNumeradoVo(): TipoDocNumerado
    {
        return new TipoDocNumerado((bool)$this->numerado);
    }

    public function setNumeradoVo(TipoDocNumerado $numerado): void
    {
        $this->numerado = $numerado->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_tipo_doc';
    }

    public function getDatosCampos(): array
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

    private function getDatosNom_doc(): DatosCampo
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

    private function getDatosSigla(): DatosCampo
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

    private function getDatosObserv(): DatosCampo
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

    private function getDatosId_coleccion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_coleccion');
        $oDatosCampo->setMetodoGet('getId_coleccion');
        $oDatosCampo->setMetodoSet('setId_coleccion');
        $oDatosCampo->setEtiqueta(_("coleccion"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(ColeccionRepositoryInterface::class);
        $oDatosCampo->setArgument2('getNom_coleccion');
        $oDatosCampo->setArgument3('getArrayColecciones');

        return $oDatosCampo;
    }

    private function getDatosBajo_llave(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('bajo_llave');
        $oDatosCampo->setMetodoGet('isBajo_llave');
        $oDatosCampo->setMetodoSet('setBajo_llave');
        $oDatosCampo->setEtiqueta(_("bajo llave"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosVigente(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('vigente');
        $oDatosCampo->setMetodoGet('isVigente');
        $oDatosCampo->setMetodoSet('setVigente');
        $oDatosCampo->setEtiqueta(_("vigente"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosNumerado(): DatosCampo
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