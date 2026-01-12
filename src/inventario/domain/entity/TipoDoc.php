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

    private TipoDocId $id_tipo_doc;

    private ?TipoDocName $nom_doc = null;

    private TipoDocSigla $sigla;

    private ?TipoDocObserv $observ = null;

    private ?ColeccionId $id_coleccion = null;

    private ?bool $bajo_llave = null;

    private ?bool $vigente = null;

    private ?bool $numerado;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_tipo_doc(): int
    {
        return $this->id_tipo_doc->value();
    }

    public function setId_tipo_doc(int $id_tipo_doc): void
    {
        $this->id_tipo_doc =TipoDocId::fromNullableInt($id_tipo_doc);
    }

    public function getNom_doc(): ?string
    {
        return $this->nom_doc->value() ?? '';
    }

    public function setNom_doc(?string $nom_doc = null): void
    {
        $this->nom_doc = TipoDocName::fromNullableString($nom_doc);
    }

    public function getSigla(): string
    {
        return $this->sigla->value();
    }

    public function setSigla(string $sigla): void
    {
        $this->sigla = TipoDocSigla::fromNullableString($sigla);
    }

    public function getObserv(): ?string
    {
        return $this->observ->value() ?? '';
    }

    public function setObserv(?string $observ = null): void
    {
        $this->observ = TipoDocObserv::fromNullableString($observ);
    }

    public function getId_coleccion(): ?string
    {
        return $this->id_coleccion?->value();
    }

    public function setId_coleccion(?int $id_coleccion = null): void
    {
        $this->id_coleccion = ColeccionId::fromNullableInt($id_coleccion);
    }


    public function isBajo_llave(): ?bool
    {
        return $this->bajo_llave;
    }

    public function setBajo_llave(?bool $bajo_llave = null): void
    {
        $this->bajo_llave = $bajo_llave;
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
        return $this->id_tipo_doc;
    }

    public function setIdTipoDocVo(TipoDocId|int|null $id = null): void
    {
        $this->id_tipo_doc = $id instanceof TipoDocId
            ? $id
            : TipoDocId::fromNullableInt($id);
    }

    public function getNomDocVo(): ?TipoDocName
    {
        return $this->nom_doc;
    }

    public function setNomDocVo(TipoDocName|string|null $name = null): void
    {
        $this->nom_doc = $name instanceof TipoDocName
            ? $name
            : TipoDocName::fromNullableString($name);
    }

    public function getSiglaVo(): ?TipoDocSigla
    {
        return $this->sigla;
    }

    public function setSiglaVo(TipoDocSigla|string|null $sigla = null): void
    {
        $this->sigla = $sigla instanceof TipoDocSigla
            ? $sigla
            : TipoDocSigla::fromNullableString($sigla);
    }

    public function getObservVo(): ?TipoDocObserv
    {
        return $this->observ;
    }

    public function setObservVo(TipoDocObserv|string|null $obs = null): void
    {
        $this->observ = $obs instanceof TipoDocObserv
            ? $obs
            : TipoDocObserv::fromNullableString($obs);
    }

    public function getIdColeccionVo(): ?ColeccionId
    {
        return $this->id_coleccion;
    }

    public function setIdColeccionVo(ColeccionId|int|null $id = null): void
    {
        $this->id_coleccion = $id instanceof ColeccionId
            ? $id
            : ColeccionId::fromNullableInt($id);
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