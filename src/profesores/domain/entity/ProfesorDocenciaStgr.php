<?php

namespace src\profesores\domain\entity;

use actividadestudios\model\entity\ActividadAsignatura;
use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\profesores\domain\value_objects\Acta;
use src\profesores\domain\value_objects\CursoInicio;

/**
 * Clase que implementa la entidad d_docencia_stgr
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class ProfesorDocenciaStgr
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de ProfesorDocenciaStgr
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_nom de ProfesorDocenciaStgr
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Id_asignatura de ProfesorDocenciaStgr
     *
     * @var int
     */
    private int $iid_asignatura;
    /**
     * Id_activ de ProfesorDocenciaStgr
     *
     * @var int|null
     */
    private int|null $iid_activ = null;
    /**
     * Tipo de ProfesorDocenciaStgr
     *
     * @var string|null
     */
    private string|null $stipo = null;
    /**
     * Curso_inicio de ProfesorDocenciaStgr
     *
     * @var int
     */
    private int $icurso_inicio;
    /**
     * Acta de ProfesorDocenciaStgr
     *
     * @var string|null
     */
    private string|null $sacta = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public static function getTiposActividad(): array
    {
        return [
            ActividadAsignatura::TIPO_CA => _("ca/cv"),
            ActividadAsignatura::TIPO_INV => _("sem. invierno"),
            ActividadAsignatura::TIPO_PRECEPTOR => _("preceptor")
        ];
    }


    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorDocenciaStgr
     */
    public function setAllAttributes(array $aDatos): ProfesorDocenciaStgr
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('id_asignatura', $aDatos)) {
            $this->setId_asignatura($aDatos['id_asignatura']);
        }
        if (array_key_exists('id_activ', $aDatos)) {
            $this->setId_activ($aDatos['id_activ']);
        }
        if (array_key_exists('tipo', $aDatos)) {
            $this->setTipo($aDatos['tipo']);
        }
        if (array_key_exists('curso_inicio', $aDatos)) {
            $this->setCurso_inicio($aDatos['curso_inicio']);
        }
        if (array_key_exists('acta', $aDatos)) {
            $this->setActa($aDatos['acta']);
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
     *
     * @return int $iid_asignatura
     */
    public function getId_asignatura(): int
    {
        return $this->iid_asignatura;
    }

    /**
     *
     * @param int $iid_asignatura
     */
    public function setId_asignatura(int $iid_asignatura): void
    {
        $this->iid_asignatura = $iid_asignatura;
    }

    /**
     *
     * @return int|null $iid_activ
     */
    public function getId_activ(): ?int
    {
        return $this->iid_activ;
    }

    /**
     *
     * @param int|null $iid_activ
     */
    public function setId_activ(?int $iid_activ = null): void
    {
        $this->iid_activ = $iid_activ;
    }

    /**
     *
     * @return string|null $stipo
     */
    public function getTipo(): ?string
    {
        return $this->stipo;
    }

    /**
     *
     * @param string|null $stipo
     */
    public function setTipo(?string $stipo = null): void
    {
        $this->stipo = $stipo;
    }

    /**
     * @return int $icurso_inicio
     * @deprecated Usar getCursoInicioVo()->value()
     */
    public function getCurso_inicio(): int
    {
        return $this->icurso_inicio;
    }

    /**
     * @param int $icurso_inicio
     * @deprecated Usar setCursoInicioVo(CursoInicio $vo)
     */
    public function setCurso_inicio(int $icurso_inicio): void
    {
        $this->icurso_inicio = $icurso_inicio;
    }

    public function getCursoInicioVo(): CursoInicio
    {
        return new CursoInicio($this->icurso_inicio);
    }

    public function setCursoInicioVo(CursoInicio $curso): void
    {
        $this->icurso_inicio = $curso->value();
    }

    /**
     * @return string|null $sacta
     * @deprecated Usar getActaVo()->value()
     */
    public function getActa(): ?string
    {
        return $this->sacta;
    }

    /**
     * @param string|null $sacta
     * @deprecated Usar setActaVo(Acta $vo)
     */
    public function setActa(?string $sacta = null): void
    {
        $this->sacta = $sacta;
    }

    public function getActaVo(): ?Acta
    {
        return Acta::fromNullable($this->sacta);
    }

    public function setActaVo(?Acta $acta): void
    {
        $this->sacta = $acta?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    function getDatosCampos(): array
    {
        $oProfesorDocenciaStgrSet = new Set();

        $oProfesorDocenciaStgrSet->add($this->getDatosId_nom());
        $oProfesorDocenciaStgrSet->add($this->getDatosId_asignatura());
        $oProfesorDocenciaStgrSet->add($this->getDatosId_activ());
        $oProfesorDocenciaStgrSet->add($this->getDatosTipo());
        $oProfesorDocenciaStgrSet->add($this->getDatosCurso_inicio());
        $oProfesorDocenciaStgrSet->add($this->getDatosActa());
        return $oProfesorDocenciaStgrSet->getTot();
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

    function getDatosId_asignatura(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_asignatura');
        $oDatosCampo->setMetodoGet('getId_asignatura');
        $oDatosCampo->setMetodoSet('setId_asignatura');
        $oDatosCampo->setEtiqueta(_("asignatura"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(AsignaturaRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombre_corto'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayAsignaturas'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    function getDatosId_activ(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_activ');
        $oDatosCampo->setMetodoGet('getId_activ');
        $oDatosCampo->setMetodoSet('setId_activ');
        $oDatosCampo->setEtiqueta(_("actividad"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument('actividades\model\entity\ActividadAll'); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNom_activ'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayActividadesEstudios'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    function getDatosTipo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo');
        $oDatosCampo->setMetodoGet('getTipo');
        $oDatosCampo->setMetodoSet('setTipo');
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista(self::getTiposActividad());
        return $oDatosCampo;
    }

    function getDatosCurso_inicio(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('curso_inicio');
        $oDatosCampo->setMetodoGet('getCurso_inicio');
        $oDatosCampo->setMetodoSet('setCurso_inicio');
        $oDatosCampo->setEtiqueta(_("año inicio curso"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(5);
        return $oDatosCampo;
    }

    function getDatosActa(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('acta');
        $oDatosCampo->setMetodoGet('getActa');
        $oDatosCampo->setMetodoSet('setActa');
        $oDatosCampo->setEtiqueta(_("acta"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

}