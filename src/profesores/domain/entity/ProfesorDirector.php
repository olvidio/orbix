<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\value_objects\DepartamentoId;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\profesores\domain\value_objects\FechaCese;
use src\profesores\domain\value_objects\FechaNombramiento;
use src\shared\domain\traits\Hydratable;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

class ProfesorDirector
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private DepartamentoId $id_departamento;

    private ?EscritoNombramiento $escrito_nombramiento = null;

    private ?DateTimeLocal $f_nombramiento = null;

    private ?EscritoCese $escrito_cese = null;

    private ?DateTimeLocal $f_cese = null;

    

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getIdDepartamentoVo(): DepartamentoId
    {
        return $this->id_departamento;
    }

    public function setIdDepartamentoVo(DepartamentoId|int $valor = null): void
    {
        $this->id_departamento = $valor instanceof DepartamentoId
            ? $valor
            : DepartamentoId::fromNullable($valor);
    }

    /**
     * @deprecated use getIdDepartamentoVo()
     */
    public function getId_departamento(): int
    {
        return $this->id_departamento->value();
    }

    /**
     * @deprecated use setIdDepartamentoVo()
     */
    public function setId_departamento(int $valor = null): void
    {
        $this->id_departamento = DepartamentoId::fromNullable($valor);
    }

    public function getEscritoNombramientoVo(): ?EscritoNombramiento
    {
        return $this->escrito_nombramiento;
    }

    public function setEscritoNombramientoVo(EscritoNombramiento|string|null $valor = null): void
    {
        $this->escrito_nombramiento = $valor instanceof EscritoNombramiento
            ? $valor
            : EscritoNombramiento::fromNullableString($valor);
    }

    /**
     * @deprecated use getEscritoNombramientoVo()
     */
    public function getEscrito_nombramiento(): ?string
    {
        return $this->escrito_nombramiento?->value();
    }

    /**
     * @deprecated use setEscritoNombramientoVo()
     */
    public function setEscrito_nombramiento(?string $valor = null): void
    {
        $this->escrito_nombramiento = EscritoNombramiento::fromNullableString($valor);
    }

    public function getEscritoCeseVo(): ?EscritoCese
    {
        return $this->escrito_cese;
    }

    public function setEscritoCeseVo(EscritoCese|string|null $valor = null): void
    {
        $this->escrito_cese = $valor instanceof EscritoCese
            ? $valor
            : EscritoCese::fromNullableString($valor);
    }

    /**
     * @deprecated use getEscritoCeseVo()
     */
    public function getEscrito_cese(): ?string
    {
        return $this->escrito_cese?->value();
    }

    /**
     * @deprecated use setEscritoCeseVo()
     */
    public function setEscrito_cese(?string $valor = null): void
    {
        $this->escrito_cese = EscritoCese::fromNullableString($valor);
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

    public function getF_nombramiento(): ?DateTimeLocal
    {
        return $this->f_nombramiento;
    }

    public function setF_nombramiento(?DateTimeLocal $valor): void
    {
        $this->f_nombramiento = $valor;
    }

    public function getF_cese(): ?DateTimeLocal
    {
        return $this->f_cese;
    }

    public function setF_cese(?DateTimeLocal $valor): void
    {
        $this->f_cese = $valor;
    }

/* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    public function getDatosCampos(): array
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