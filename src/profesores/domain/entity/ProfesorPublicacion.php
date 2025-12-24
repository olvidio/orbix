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
use web\DateTimeLocal;
use web\NullDateTimeLocal;
use function core\is_true;

/**
 * Clase que implementa la entidad d_publicaciones
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class ProfesorPublicacion
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de ProfesorPublicacion
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_nom de ProfesorPublicacion
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Tipo_publicacion de ProfesorPublicacion
     *
     * @var string|null
     */
    private string|null $stipo_publicacion = null;
    /**
     * Titulo de ProfesorPublicacion
     *
     * @var string
     */
    private string $stitulo;
    /**
     * Editorial de ProfesorPublicacion
     *
     * @var string|null
     */
    private string|null $seditorial = null;
    /**
     * Coleccion de ProfesorPublicacion
     *
     * @var string|null
     */
    private string|null $scoleccion = null;
    /**
     * F_publicacion de ProfesorPublicacion
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_publicacion = null;
    /**
     * Pendiente de ProfesorPublicacion
     *
     * @var bool|null
     */
    private bool|null $bpendiente = null;
    /**
     * Referencia de ProfesorPublicacion
     *
     * @var string|null
     */
    private string|null $sreferencia = null;
    /**
     * Lugar de ProfesorPublicacion
     *
     * @var string|null
     */
    private string|null $slugar = null;
    /**
     * Observ de ProfesorPublicacion
     *
     * @var string|null
     */
    private string|null $sobserv = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorPublicacion
     */
    public function setAllAttributes(array $aDatos): ProfesorPublicacion
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('tipo_publicacion', $aDatos)) {
            $this->setTipoPublicacionVo(TipoPublicacionName::fromNullable($aDatos['tipo_publicacion']));
        }
        if (array_key_exists('titulo', $aDatos)) {
            $this->setTituloVo(PublicacionTitulo::fromNullable($aDatos['titulo']));
        }
        if (array_key_exists('editorial', $aDatos)) {
            $this->setEditorialVo(EditorialName::fromNullable($aDatos['editorial']));
        }
        if (array_key_exists('coleccion', $aDatos)) {
            $this->setColeccionVo(ColeccionName::fromNullable($aDatos['coleccion']));
        }
        if (array_key_exists('f_publicacion', $aDatos)) {
            $this->setFechaPublicacionVo(FechaPublicacion::fromNullable($aDatos['f_publicacion']));
        }
        if (array_key_exists('pendiente', $aDatos)) {
            $this->setPendiente(is_true($aDatos['pendiente']));
        }
        if (array_key_exists('referencia', $aDatos)) {
            $this->setReferenciaVo(ReferenciaText::fromNullable($aDatos['referencia']));
        }
        if (array_key_exists('lugar', $aDatos)) {
            $this->setLugarVo(LugarPublicacionName::fromNullable($aDatos['lugar']));
        }
        if (array_key_exists('observ', $aDatos)) {
            $this->setObservVo(ObservacionText::fromNullable($aDatos['observ']));
        }
        return $this;
    }

    /**
     *
     * @return int $iid_item
     */
    public function getId_item(): int
    {
        return $this->iid_item;
    }

    /**
     *
     * @param int $iid_item
     */
    public function setId_item(int $iid_item): void
    {
        $this->iid_item = $iid_item;
    }

    /**
     *
     * @return int $iid_nom
     */
    public function getId_nom(): int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int $iid_nom
     */
    public function setId_nom(int $iid_nom): void
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * @return string|null $stipo_publicacion
     * @deprecated Usar getTipoPublicacionVo()->value()
     */
    public function getTipo_publicacion(): ?string
    {
        return $this->stipo_publicacion;
    }

    /**
     * @param string|null $stipo_publicacion
     * @deprecated Usar setTipoPublicacionVo(TipoPublicacionName $vo)
     */
    public function setTipo_publicacion(?string $stipo_publicacion = null): void
    {
        $this->stipo_publicacion = $stipo_publicacion;
    }

    public function getTipoPublicacionVo(): ?TipoPublicacionName
    {
        return TipoPublicacionName::fromNullable($this->stipo_publicacion);
    }

    public function setTipoPublicacionVo(?TipoPublicacionName $tipo): void
    {
        $this->stipo_publicacion = $tipo?->value();
    }

    /**
     * @return string $stitulo
     * @deprecated Usar getTituloVo()->value()
     */
    public function getTitulo(): string
    {
        return $this->stitulo;
    }

    /**
     * @param string $stitulo
     * @deprecated Usar setTituloVo(PublicacionTitulo $vo)
     */
    public function setTitulo(string $stitulo): void
    {
        $this->stitulo = $stitulo;
    }

    public function getTituloVo(): PublicacionTitulo
    {
        return new PublicacionTitulo($this->stitulo);
    }

    public function setTituloVo(?PublicacionTitulo $titulo): void
    {
        if ($titulo !== null) {
            $this->stitulo = $titulo->value();
        }
    }

    /**
     * @return string|null $seditorial
     * @deprecated Usar getEditorialVo()->value()
     */
    public function getEditorial(): ?string
    {
        return $this->seditorial;
    }

    /**
     * @param string|null $seditorial
     * @deprecated Usar setEditorialVo(EditorialName $vo)
     */
    public function setEditorial(?string $seditorial = null): void
    {
        $this->seditorial = $seditorial;
    }

    public function getEditorialVo(): ?EditorialName
    {
        return EditorialName::fromNullable($this->seditorial);
    }

    public function setEditorialVo(?EditorialName $editorial): void
    {
        $this->seditorial = $editorial?->value();
    }

    /**
     * @return string|null $scoleccion
     * @deprecated Usar getColeccionVo()->value()
     */
    public function getColeccion(): ?string
    {
        return $this->scoleccion;
    }

    /**
     * @param string|null $scoleccion
     * @deprecated Usar setColeccionVo(ColeccionName $vo)
     */
    public function setColeccion(?string $scoleccion = null): void
    {
        $this->scoleccion = $scoleccion;
    }

    public function getColeccionVo(): ?ColeccionName
    {
        return ColeccionName::fromNullable($this->scoleccion);
    }

    public function setColeccionVo(?ColeccionName $coleccion): void
    {
        $this->scoleccion = $coleccion?->value();
    }

    /**
     * @return DateTimeLocal|NullDateTimeLocal|null $df_publicacion
     * @deprecated Usar getFechaPublicacionVo()->value()
     */
    public function getF_publicacion(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_publicacion ?? new NullDateTimeLocal;
    }

    /**
     * @param DateTimeLocal|null $df_publicacion
     * @deprecated Usar setFechaPublicacionVo(FechaPublicacion $vo)
     */
    public function setF_publicacion(DateTimeLocal|null $df_publicacion = null): void
    {
        $this->df_publicacion = $df_publicacion;
    }

    public function getFechaPublicacionVo(): ?FechaPublicacion
    {
        return FechaPublicacion::fromNullable($this->df_publicacion);
    }

    public function setFechaPublicacionVo(?FechaPublicacion $fecha): void
    {
        $this->df_publicacion = $fecha?->value();
    }

    /**
     *
     * @return bool|null $bpendiente
     */
    public function isPendiente(): ?bool
    {
        return $this->bpendiente;
    }

    /**
     *
     * @param bool|null $bpendiente
     */
    public function setPendiente(?bool $bpendiente = null): void
    {
        $this->bpendiente = $bpendiente;
    }

    /**
     * @return string|null $sreferencia
     * @deprecated Usar getReferenciaVo()->value()
     */
    public function getReferencia(): ?string
    {
        return $this->sreferencia;
    }

    /**
     * @param string|null $sreferencia
     * @deprecated Usar setReferenciaVo(ReferenciaText $vo)
     */
    public function setReferencia(?string $sreferencia = null): void
    {
        $this->sreferencia = $sreferencia;
    }

    public function getReferenciaVo(): ?ReferenciaText
    {
        return ReferenciaText::fromNullable($this->sreferencia);
    }

    public function setReferenciaVo(?ReferenciaText $referencia): void
    {
        $this->sreferencia = $referencia?->value();
    }

    /**
     * @return string|null $slugar
     * @deprecated Usar getLugarVo()->value()
     */
    public function getLugar(): ?string
    {
        return $this->slugar;
    }

    /**
     * @param string|null $slugar
     * @deprecated Usar setLugarVo(LugarPublicacionName $vo)
     */
    public function setLugar(?string $slugar = null): void
    {
        $this->slugar = $slugar;
    }

    public function getLugarVo(): ?LugarPublicacionName
    {
        return LugarPublicacionName::fromNullable($this->slugar);
    }

    public function setLugarVo(?LugarPublicacionName $lugar): void
    {
        $this->slugar = $lugar?->value();
    }

    /**
     * @return string|null $sobserv
     * @deprecated Usar getObservVo()->value()
     */
    public function getObserv(): ?string
    {
        return $this->sobserv;
    }

    /**
     * @param string|null $sobserv
     * @deprecated Usar setObservVo(ObservacionText $vo)
     */
    public function setObserv(?string $sobserv = null): void
    {
        $this->sobserv = $sobserv;
    }

    public function getObservVo(): ?ObservacionText
    {
        return ObservacionText::fromNullable($this->sobserv);
    }

    public function setObservVo(?ObservacionText $observ): void
    {
        $this->sobserv = $observ?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    function getDatosCampos(): array
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

    function getDatosId_nom(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nom');
        $oDatosCampo->setMetodoGet('getId_nom');
        $oDatosCampo->setMetodoSet('setId_nom');
        $oDatosCampo->setEtiqueta(_("id_nom"));
        $oDatosCampo->setTipo('hidden');

        return $oDatosCampo;
    }

    function getDatosTipo_publicacion(): DatosCampo
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

    function getDatosTitulo(): DatosCampo
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

    function getDatosEditorial(): DatosCampo
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

    function getDatosColeccion(): DatosCampo
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

    function getDatosF_publicacion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_publicacion');
        $oDatosCampo->setMetodoGet('getF_publicacion');
        $oDatosCampo->setMetodoSet('setF_publicacion');
        $oDatosCampo->setEtiqueta(_("fecha de la publicación"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosPendiente(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('pendiente');
        $oDatosCampo->setMetodoGet('isPendiente');
        $oDatosCampo->setMetodoSet('setPendiente');
        $oDatosCampo->setEtiqueta(_("pendiente"));
        $oDatosCampo->setTipo('check');
        return $oDatosCampo;
    }

    function getDatosReferencia(): DatosCampo
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

    function getDatosLugar(): DatosCampo
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

    function getDatosObserv(): DatosCampo
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
