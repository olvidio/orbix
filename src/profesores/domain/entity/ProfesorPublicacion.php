<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\ColeccionName;
use src\profesores\domain\value_objects\EditorialName;
use src\profesores\domain\value_objects\LugarPublicacionName;
use src\profesores\domain\value_objects\ObservacionText;
use src\profesores\domain\value_objects\PublicacionTitulo;
use src\profesores\domain\value_objects\ReferenciaText;
use src\profesores\domain\value_objects\TipoPublicacionName;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;


class ProfesorPublicacion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private ?TipoPublicacionName $tipo_publicacion = null;

    private PublicacionTitulo $titulo;

    private ?EditorialName $editorial = null;

    private ?ColeccionName $coleccion = null;

   private ?DateTimeLocal $f_publicacion = null;

    private ?bool $pendiente = null;

    private ?ReferenciaText $referencia = null;

    private ?LugarPublicacionName $lugar = null;

    private ?ObservacionText $observ = null;

    

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getTipoPublicacionVo(): ?TipoPublicacionName
    {
        return $this->tipo_publicacion;
    }

    public function setTipoPublicacionVo(TipoPublicacionName|string|null $valor = null): void
    {
        $this->tipo_publicacion = $valor instanceof TipoPublicacionName
            ? $valor
            : TipoPublicacionName::fromNullableString($valor);
    }

    /**
     * @deprecated use getTipoPublicacionVo()
     */
    public function getTipo_publicacion(): ?string
    {
        return $this->tipo_publicacion?->value();
    }

    /**
     * @deprecated use setTipoPublicacionVo()
     */
    public function setTipo_publicacion(?string $valor = null): void
    {
        $this->tipo_publicacion = TipoPublicacionName::fromNullableString($valor);
    }

    public function getTituloVo(): PublicacionTitulo
    {
        return $this->titulo;
    }

    public function setTituloVo(PublicacionTitulo|string|null $valor = null): void
    {
        $this->titulo = $valor instanceof PublicacionTitulo
            ? $valor
            : PublicacionTitulo::fromNullableString($valor);
    }

    /**
     * @deprecated use getTituloVo()
     */
    public function getTitulo(): string
    {
        return $this->titulo->value();
    }

    /**
     * @deprecated use setTituloVo()
     */
    public function setTitulo(?string $valor = null): void
    {
        $this->titulo = PublicacionTitulo::fromNullableString($valor);
    }

    public function getEditorialVo(): ?EditorialName
    {
        return $this->editorial;
    }

    public function setEditorialVo(EditorialName|string|null $valor = null): void
    {
        $this->editorial = $valor instanceof EditorialName
            ? $valor
            : EditorialName::fromNullableString($valor);
    }

    /**
     * @deprecated use getEditorialVo()
     */
    public function getEditorial(): ?string
    {
        return $this->editorial?->value();
    }

    /**
     * @deprecated use setEditorialVo()
     */
    public function setEditorial(?string $valor = null): void
    {
        $this->editorial = EditorialName::fromNullableString($valor);
    }

    public function getColeccionVo(): ?ColeccionName
    {
        return $this->coleccion;
    }

    public function setColeccionVo(ColeccionName|string|null $valor = null): void
    {
        $this->coleccion = $valor instanceof ColeccionName
            ? $valor
            : ColeccionName::fromNullableString($valor);
    }

    /**
     * @deprecated use getColeccionVo()
     */
    public function getColeccion(): ?string
    {
        return $this->coleccion?->value();
    }

    /**
     * @deprecated use setColeccionVo()
     */
    public function setColeccion(?string $valor = null): void
    {
        $this->coleccion = ColeccionName::fromNullableString($valor);
    }

    public function getReferenciaVo(): ?ReferenciaText
    {
        return $this->referencia;
    }

    public function setReferenciaVo(ReferenciaText|string|null $valor = null): void
    {
        $this->referencia = $valor instanceof ReferenciaText
            ? $valor
            : ReferenciaText::fromNullableString($valor);
    }

    /**
     * @deprecated use getReferenciaVo()
     */
    public function getReferencia(): ?string
    {
        return $this->referencia?->value();
    }

    /**
     * @deprecated use setReferenciaVo()
     */
    public function setReferencia(?string $valor = null): void
    {
        $this->referencia = ReferenciaText::fromNullableString($valor);
    }

    public function getLugarVo(): ?LugarPublicacionName
    {
        return $this->lugar;
    }

    public function setLugarVo(LugarPublicacionName|string|null $valor = null): void
    {
        $this->lugar = $valor instanceof LugarPublicacionName
            ? $valor
            : LugarPublicacionName::fromNullableString($valor);
    }

    /**
     * @deprecated use getLugarVo()
     */
    public function getLugar(): ?string
    {
        return $this->lugar?->value();
    }

    /**
     * @deprecated use setLugarVo()
     */
    public function setLugar(?string $valor = null): void
    {
        $this->lugar = LugarPublicacionName::fromNullableString($valor);
    }

    public function getObservVo(): ?ObservacionText
    {
        return $this->observ;
    }

    public function setObservVo(ObservacionText|string|null $valor = null): void
    {
        $this->observ = $valor instanceof ObservacionText
            ? $valor
            : ObservacionText::fromNullableString($valor);
    }

    /**
     * @deprecated use getObservVo()
     */
    public function getObserv(): ?string
    {
        return $this->observ?->value();
    }

    /**
     * @deprecated use setObservVo()
     */
    public function setObserv(?string $valor = null): void
    {
        $this->observ = ObservacionText::fromNullableString($valor);
    }

    public function getId_item(): int
    {
        return $this->id_item;
    }

    public function setId_item(int $valor): void
    {
        $this->id_item = $valor;
    }

    public function getId_nom(): int
    {
        return $this->id_nom;
    }

    public function setId_nom(int $valor): void
    {
        $this->id_nom = $valor;
    }

    public function getF_publicacion(): ?DateTimeLocal
    {
        return $this->f_publicacion;
    }

    public function setF_publicacion(?DateTimeLocal $valor): void
    {
        $this->f_publicacion = $valor;
    }

    public function isPendiente(): ?bool
    {
        return $this->pendiente;
    }

    public function setPendiente(?bool $valor): void
    {
        $this->pendiente = $valor;
    }

/* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

  public function getDatosCampos(): array
    {
        $oProfesorPublicacionSet = new Set();

        $oProfesorPublicacionSet->add($this->getDatosId_nom());
        $oProfesorPublicacionSet->add($this->getDatosTipo_publicacion());
        $oProfesorPublicacionSet->add($this->getDatosTitulo());
        $oProfesorPublicacionSet->add($this->getDatosEditorial());
        $oProfesorPublicacionSet->add($this->getDatosColeccion());
        $oProfesorPublicacionSet->add($this->getDatosF_publicacion());
        $oProfesorPublicacionSet->add($this->getDatosPendiente());
        $oProfesorPublicacionSet->add($this->getDatosReferencia());
        $oProfesorPublicacionSet->add($this->getDatosLugar());
        $oProfesorPublicacionSet->add($this->getDatosObserv());
        return $oProfesorPublicacionSet->getTot();
    }

    private function getDatosId_nom(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nom');
        $oDatosCampo->setMetodoGet('getId_nom');
        $oDatosCampo->setMetodoSet('setId_nom');
        $oDatosCampo->setEtiqueta(_("id_nom"));
        $oDatosCampo->setTipo('hidden');

        return $oDatosCampo;
    }

    private function getDatosTipo_publicacion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo_publicacion');
        $oDatosCampo->setMetodoGet('getTipo_publicacion');
        $oDatosCampo->setMetodoSet('setTipo_publicacion');
        $oDatosCampo->setEtiqueta(_("tipo de publicación"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(15);
        return $oDatosCampo;
    }

    private function getDatosTitulo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('titulo');
        $oDatosCampo->setMetodoGet('getTitulo');
        $oDatosCampo->setMetodoSet('setTitulo');
        $oDatosCampo->setEtiqueta(_("título"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(100);
        return $oDatosCampo;
    }

    private function getDatosEditorial(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('editorial');
        $oDatosCampo->setMetodoGet('getEditorial');
        $oDatosCampo->setMetodoSet('setEditorial');
        $oDatosCampo->setEtiqueta(_("editorial"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    private function getDatosColeccion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('coleccion');
        $oDatosCampo->setMetodoGet('getColeccion');
        $oDatosCampo->setMetodoSet('setColeccion');
        $oDatosCampo->setEtiqueta(_("colección"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    private function getDatosF_publicacion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_publicacion');
        $oDatosCampo->setMetodoGet('getF_publicacion');
        $oDatosCampo->setMetodoSet('setF_publicacion');
        $oDatosCampo->setEtiqueta(_("fecha de la publicación"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosPendiente(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('pendiente');
        $oDatosCampo->setMetodoGet('isPendiente');
        $oDatosCampo->setMetodoSet('setPendiente');
        $oDatosCampo->setEtiqueta(_("pendiente"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    private function getDatosReferencia(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('referencia');
        $oDatosCampo->setMetodoGet('getReferencia');
        $oDatosCampo->setMetodoSet('setReferencia');
        $oDatosCampo->setEtiqueta(_("referencia"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

    private function getDatosLugar(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('lugar');
        $oDatosCampo->setMetodoGet('getLugar');
        $oDatosCampo->setMetodoSet('setLugar');
        $oDatosCampo->setEtiqueta(_("lugar"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(100);
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
        $oDatosCampo->setArgument(100);
        return $oDatosCampo;
    }
}
