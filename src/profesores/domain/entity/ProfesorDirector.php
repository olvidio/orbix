<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\profesores\domain\value_objects\FechaNombramiento;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\FechaCese;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class ProfesorDirector
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private int $id_departamento;

    private string|null $escrito_nombramiento = null;

    private DateTimeLocal|null $f_nombramiento = null;

    private string|null $escrito_cese = null;

    private DateTimeLocal|null $f_cese = null;

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


    public function getId_departamento(): int
    {
        return $this->id_departamento;
    }


    public function setId_departamento(int $id_departamento): void
    {
        $this->id_departamento = $id_departamento;
    }

    // Métodos VO nuevos
    public function getEscritoNombramientoVo(): ?EscritoNombramiento
    {
        return EscritoNombramiento::fromNullable($this->escrito_nombramiento);
    }

    public function setEscritoNombramientoVo(?EscritoNombramiento $escrito): void
    {
        $this->escrito_nombramiento = $escrito?->value();
    }

    public function getFechaNombramientoVo(): ?FechaNombramiento
    {
        return FechaNombramiento::fromNullable($this->f_nombramiento);
    }

    public function setFechaNombramientoVo(?FechaNombramiento $fecha): void
    {
        $this->f_nombramiento = $fecha?->value();
    }

    public function getEscritoCeseVo(): ?EscritoCese
    {
        return EscritoCese::fromNullable($this->escrito_cese);
    }

    public function setEscritoCeseVo(?EscritoCese $escrito): void
    {
        $this->escrito_cese = $escrito?->value();
    }

    public function getFechaCeseVo(): ?FechaCese
    {
        return FechaCese::fromNullable($this->f_cese);
    }

    public function setFechaCeseVo(?FechaCese $fecha): void
    {
        $this->f_cese = $fecha?->value();
    }

    /**
     * @deprecated Usar getEscritoNombramientoVo()->value()
     */
    public function getEscrito_nombramiento(): ?string
    {
        return $this->escrito_nombramiento;
    }

    /**
     * @deprecated Usar setEscritoNombramientoVo(EscritoNombramiento $vo)
     */
    public function setEscrito_nombramiento(?string $escrito_nombramiento = null): void
    {
        $this->escrito_nombramiento = $escrito_nombramiento;
    }

    /**
     * @deprecated Usar getFechaNombramientoVo()->value()
     */
    public function getF_nombramiento(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_nombramiento ?? new NullDateTimeLocal;
    }

    /**
     * @deprecated Usar setFechaNombramientoVo(FechaNombramiento $vo)
     */
    public function setF_nombramiento(DateTimeLocal|null $f_nombramiento = null): void
    {
        $this->f_nombramiento = $f_nombramiento;
    }

    /**
     * @deprecated Usar getEscritoCeseVo()->value()
     */
    public function getEscrito_cese(): ?string
    {
        return $this->escrito_cese;
    }

    /**
     * @deprecated Usar setEscritoCeseVo(EscritoCese $vo)
     */
    public function setEscrito_cese(?string $escrito_cese = null): void
    {
        $this->escrito_cese = $escrito_cese;
    }

    /**
     * @deprecated Usar getFechaCeseVo()->value()
     */
    public function getF_cese(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->f_cese ?? new NullDateTimeLocal;
    }

    /**
     * @deprecated Usar setFechaCeseVo(FechaCese $vo)
     */
    public function setF_cese(DateTimeLocal|null $f_cese = null): void
    {
        $this->f_cese = $f_cese;
    }


    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    public function getDatosCampos():array
    {
        $oProfesorDirectorSet = new Set();

        $oProfesorDirectorSet->add($this->getDatosId_nom());
        $oProfesorDirectorSet->add($this->getDatosId_departamento());
        $oProfesorDirectorSet->add($this->getDatosEscrito_nombramiento());
        $oProfesorDirectorSet->add($this->getDatosF_nombramiento());
        $oProfesorDirectorSet->add($this->getDatosEscrito_cese());
        $oProfesorDirectorSet->add($this->getDatosF_cese());
        return $oProfesorDirectorSet->getTot();
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
    private function getDatosId_departamento(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_departamento');
        $oDatosCampo->setMetodoGet('getId_departamento');
        $oDatosCampo->setMetodoSet('setId_departamento');
        $oDatosCampo->setEtiqueta(_("departamento"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(DepartamentoRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getDepartamento'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayDepartamentos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    private function getDatosEscrito_nombramiento(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('escrito_nombramiento');
        $oDatosCampo->setMetodoGet('getEscrito_nombramiento');
        $oDatosCampo->setMetodoSet('setEscrito_nombramiento');
        $oDatosCampo->setEtiqueta(_("escrito de nombramiento"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    private function getDatosF_nombramiento(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_nombramiento');
        $oDatosCampo->setMetodoGet('getF_nombramiento');
        $oDatosCampo->setMetodoSet('setF_nombramiento');
        $oDatosCampo->setEtiqueta(_("fecha de nombramiento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    private function getDatosEscrito_cese(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('escrito_cese');
        $oDatosCampo->setMetodoGet('getEscrito_cese');
        $oDatosCampo->setMetodoSet('setEscrito_cese');
        $oDatosCampo->setEtiqueta(_("escrito de cese"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    private function getDatosF_cese(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_cese');
        $oDatosCampo->setMetodoGet('getF_cese');
        $oDatosCampo->setMetodoSet('setF_cese');
        $oDatosCampo->setEtiqueta(_("fecha de cese"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }
}