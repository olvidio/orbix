<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\profesores\domain\value_objects\Acta;
use src\profesores\domain\value_objects\CursoInicio;
use src\shared\domain\traits\Hydratable;


class ProfesorDocenciaStgr
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_nom;

    private int $id_asignatura;

    private int|null $id_activ = null;

    private string|null $tipo = null;

    private int $curso_inicio;

    private string|null $acta = null;

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


    public function getId_asignatura(): int
    {
        return $this->id_asignatura;
    }


    public function setId_asignatura(int $id_asignatura): void
    {
        $this->id_asignatura = $id_asignatura;
    }


    public function getId_activ(): ?int
    {
        return $this->id_activ;
    }


    public function setId_activ(?int $id_activ = null): void
    {
        $this->id_activ = $id_activ;
    }


    public function getTipo(): ?string
    {
        return $this->tipo;
    }


    public function setTipo(?string $tipo = null): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @deprecated Usar getCursoInicioVo()->value()
     */
    public function getCurso_inicio(): int
    {
        return $this->curso_inicio;
    }

    /**
     * @deprecated Usar setCursoInicioVo(CursoInicio $vo)
     */
    public function setCurso_inicio(int $curso_inicio): void
    {
        $this->curso_inicio = $curso_inicio;
    }

    public function getCursoInicioVo(): CursoInicio
    {
        return new CursoInicio($this->curso_inicio);
    }

    public function setCursoInicioVo(CursoInicio $curso): void
    {
        $this->curso_inicio = $curso->value();
    }

    /**
     * @deprecated Usar getActaVo()->value()
     */
    public function getActa(): ?string
    {
        return $this->acta;
    }

    /**
     * @deprecated Usar setActaVo(Acta $vo)
     */
    public function setActa(?string $acta = null): void
    {
        $this->acta = $acta;
    }

    public function getActaVo(): ?Acta
    {
        return Acta::fromNullable($this->acta);
    }

    public function setActaVo(?Acta $acta): void
    {
        $this->acta = $acta?->value();
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

  public function getDatosCampos(): array
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

    private function getDatosId_asignatura(): DatosCampo
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

    private function getDatosId_activ(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_activ');
        $oDatosCampo->setMetodoGet('getId_activ');
        $oDatosCampo->setMetodoSet('setId_activ');
        $oDatosCampo->setEtiqueta(_("actividad"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(ActividadAllRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNom_activ'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayActividadesEstudios'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    private function getDatosTipo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('tipo');
        $oDatosCampo->setMetodoGet('getTipo');
        $oDatosCampo->setMetodoSet('setTipo');
        $oDatosCampo->setEtiqueta(_("tipo"));
        $oDatosCampo->setTipo('array');
        $oDatosCampo->setLista(TipoActividadAsignatura::getTiposActividad());
        return $oDatosCampo;
    }

    private function getDatosCurso_inicio(): DatosCampo
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

    private function getDatosActa(): DatosCampo
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