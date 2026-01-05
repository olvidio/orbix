<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\ColeccionName;
use src\profesores\domain\value_objects\EditorialName;
use src\profesores\domain\value_objects\FechaPublicacion;
use src\profesores\domain\value_objects\LugarPublicacionName;
use src\profesores\domain\value_objects\ObservacionText;
use src\profesores\domain\value_objects\PublicacionTitulo;
use src\profesores\domain\value_objects\ReferenciaText;
use src\profesores\domain\value_objects\TipoPublicacionName;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;


class ProfesorPublicacion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private string|null $tipo_publicacion = null;

    private string $titulo;

    private string|null $editorial = null;

    private string|null $coleccion = null;

    private DateTimeLocal|null $f_publicacion = null;

    private bool|null $pendiente = null;

    private string|null $referencia = null;

    private string|null $lugar = null;

    private string|null $observ = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_item(): int
    {
        return $this->id_item;
    }


    public function setId_item(int $id_item): void
    {
        $this->id_item = $id_item;
    }


    public function getId_nom(): int
    {
        return $this->id_nom;
    }


    public function setId_nom(int $id_nom): void
    {
        $this->id_nom = $id_nom;
    }

    /**
     * @deprecated Usar getTipoPublicacionVo()->value()
     */
    public function getTipo_publicacion(): ?string
    {
        return $this->tipo_publicacion;
    }

    /**
     * @deprecated Usar setTipoPublicacionVo(TipoPublicacionName $vo)
     */
    public function setTipo_publicacion(?string $tipo_publicacion = null): void
    {
        $this->tipo_publicacion = $tipo_publicacion;
    }

    public function getTipoPublicacionVo(): ?TipoPublicacionName
    {
        return TipoPublicacionName::fromNullable($this->tipo_publicacion);
    }

    public function setTipoPublicacionVo(?TipoPublicacionName $tipo): void
    {
        $this->tipo_publicacion = $tipo?->value();
    }

    /**
     * @deprecated Usar getTituloVo()->value()
     */
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    /**
     * @deprecated Usar setTituloVo(PublicacionTitulo $vo)
     */
    public function setTitulo(string $titulo): void
    {
        $this->titulo = $titulo;
    }

    public function getTituloVo(): PublicacionTitulo
    {
        return new PublicacionTitulo($this->titulo);
    }

    public function setTituloVo(?PublicacionTitulo $titulo): void
    {
        if ($titulo !== null) {
            $this->titulo = $titulo->value();
        }
    }

    /**
     * @deprecated Usar getEditorialVo()->value()
     */
    public function getEditorial(): ?string
    {
        return $this->editorial;
    }

    /**
     * @deprecated Usar setEditorialVo(EditorialName $vo)
     */
    public function setEditorial(?string $editorial = null): void
    {
        $this->editorial = $editorial;
    }

    public function getEditorialVo(): ?EditorialName
    {
        return EditorialName::fromNullable($this->editorial);
    }

    public function setEditorialVo(?EditorialName $editorial): void
    {
        $this->editorial = $editorial?->value();
    }

    /**
     * @deprecated Usar getColeccionVo()->value()
     */
    public function getColeccion(): ?string
    {
        return $this->coleccion;
    }

    /**
     * @deprecated Usar setColeccionVo(ColeccionName $vo)
     */
    public function setColeccion(?string $coleccion = null): void
    {
        $this->coleccion = $coleccion;
    }

    public function getColeccionVo(): ?ColeccionName
    {
        return ColeccionName::fromNullable($this->coleccion);
    }

    public function setColeccionVo(?ColeccionName $coleccion): void
    {
        $this->coleccion = $coleccion?->value();
    }

    /**
     * @deprecated Usar getFechaPublicacionVo()->value()
     */
    public function getF_publicacion(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_publicacion ?? new NullDateTimeLocal;
    }

    /**
     * @deprecated Usar setFechaPublicacionVo(FechaPublicacion $vo)
     */
    public function setF_publicacion(DateTimeLocal|null $f_publicacion = null): void
    {
        $this->f_publicacion = $f_publicacion;
    }

    public function getFechaPublicacionVo(): ?FechaPublicacion
    {
        return FechaPublicacion::fromNullable($this->f_publicacion);
    }

    public function setFechaPublicacionVo(?FechaPublicacion $fecha): void
    {
        $this->f_publicacion = $fecha?->value();
    }


    public function isPendiente(): ?bool
    {
        return $this->pendiente;
    }


    public function setPendiente(?bool $pendiente = null): void
    {
        $this->pendiente = $pendiente;
    }

    /**
     * @deprecated Usar getReferenciaVo()->value()
     */
    public function getReferencia(): ?string
    {
        return $this->referencia;
    }

    /**
     * @deprecated Usar setReferenciaVo(ReferenciaText $vo)
     */
    public function setReferencia(?string $referencia = null): void
    {
        $this->referencia = $referencia;
    }

    public function getReferenciaVo(): ?ReferenciaText
    {
        return ReferenciaText::fromNullable($this->referencia);
    }

    public function setReferenciaVo(?ReferenciaText $referencia): void
    {
        $this->referencia = $referencia?->value();
    }

    /**
     * @deprecated Usar getLugarVo()->value()
     */
    public function getLugar(): ?string
    {
        return $this->lugar;
    }

    /**
     * @deprecated Usar setLugarVo(LugarPublicacionName $vo)
     */
    public function setLugar(?string $lugar = null): void
    {
        $this->lugar = $lugar;
    }

    public function getLugarVo(): ?LugarPublicacionName
    {
        return LugarPublicacionName::fromNullable($this->lugar);
    }

    public function setLugarVo(?LugarPublicacionName $lugar): void
    {
        $this->lugar = $lugar?->value();
    }

    /**
     * @deprecated Usar getObservVo()->value()
     */
    public function getObserv(): ?string
    {
        return $this->observ;
    }

    /**
     * @deprecated Usar setObservVo(ObservacionText $vo)
     */
    public function setObserv(?string $observ = null): void
    {
        $this->observ = $observ;
    }

    public function getObservVo(): ?ObservacionText
    {
        return ObservacionText::fromNullable($this->observ);
    }

    public function setObservVo(?ObservacionText $observ): void
    {
        $this->observ = $observ?->value();
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
