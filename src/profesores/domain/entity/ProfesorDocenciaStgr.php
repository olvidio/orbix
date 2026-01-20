<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\value_objects\TipoActividadAsignatura;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\notas\domain\value_objects\ActaNumero;
use src\procesos\domain\value_objects\ActividadId;
use src\profesores\domain\value_objects\Acta;
use src\profesores\domain\value_objects\CursoInicio;
use src\profesores\domain\value_objects\ProfesorTipoName;
use src\shared\domain\traits\Hydratable;


class ProfesorDocenciaStgr
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_item;

    private int $id_nom;

    private AsignaturaId $id_asignatura;

    private ?ActividadId $id_activ = null;

    private ?TipoActividadAsignatura $tipo = null;

    private int $curso_inicio;

    private ?ActaNumero $acta = null;

    

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getIdAsignaturaVo(): AsignaturaId
    {
        return $this->id_asignatura;
    }

    public function setIdAsignaturaVo(AsignaturaId|int $valor = null): void
    {
        $this->id_asignatura = $valor instanceof AsignaturaId
            ? $valor
            : AsignaturaId::fromNullableInt($valor);
    }

    /**
     * @deprecated use getIdAsignaturaVo()
     */
    public function getId_asignatura(): int
    {
        return $this->id_asignatura->value();
    }

    /**
     * @deprecated use setIdAsignaturaVo()
     */
    public function setId_asignatura(int $valor = null): void
    {
        $this->id_asignatura = AsignaturaId::fromNullableInt($valor);
    }

    public function getIdActivVo(): ?ActividadId
    {
        return $this->id_activ;
    }

    public function setIdActivVo(ActividadId|int|null $valor = null): void
    {
        $this->id_activ = $valor instanceof ActividadId
            ? $valor
            : ActividadId::fromNullableInt($valor);
    }

    /**
     * @deprecated use getIdActivVo()
     */
    public function getId_activ(): ?int
    {
        return $this->id_activ?->value();
    }

    /**
     * @deprecated use setIdActivVo()
     */
    public function setId_activ(?int $valor = null): void
    {
        $this->id_activ = ActividadId::fromNullableInt($valor);
    }

    public function getTipoVo(): ?TipoActividadAsignatura
    {
        return $this->tipo;
    }

    public function setTipoVo(TipoActividadAsignatura|string|null $valor = null): void
    {
        $this->tipo = $valor instanceof TipoActividadAsignatura
            ? $valor
            : TipoActividadAsignatura::fromNullableString($valor);
    }

    /**
     * @deprecated use getTipoActividadAsignaturaVo()
     */
    public function getTipo(): ?string
    {
        return $this->tipo?->value();
    }

    /**
     * @deprecated use setTipoActividadAsignaturaVo()
     */
    public function setTipo(?string $valor = null): void
    {
        $this->tipo = TipoActividadAsignatura::fromNullableString($valor);
    }

    public function getActaVo(): ?ActaNumero
    {
        return $this->acta;
    }

    public function setActaVo(ActaNumero|string|null $valor = null): void
    {
        $this->acta = $valor instanceof ActaNumero
            ? $valor
            : ActaNumero::fromNullableString($valor);
    }

    /**
     * @deprecated use getActaVo()
     */
    public function getActa(): ?int
    {
        return $this->acta?->value();
    }

    /**
     * @deprecated use setActaVo()
     */
    public function setActa(?string $valor = null): void
    {
        $this->acta = ActaNumero::fromNullableString($valor);
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

    public function getCurso_inicio(): int
    {
        return $this->curso_inicio;
    }

    public function setCurso_inicio(int $valor): void
    {
        $this->curso_inicio = $valor;
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