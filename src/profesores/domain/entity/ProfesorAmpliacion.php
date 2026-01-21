<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\shared\domain\traits\Hydratable;
use src\shared\domain\value_objects\DateTimeLocal;


class ProfesorAmpliacion
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */

    private int $id_item;

    private int $id_nom;

    private AsignaturaId $id_asignatura;

    private ?EscritoNombramiento $escrito_nombramiento = null;

    private ?DateTimeLocal $f_nombramiento = null;

    private ?EscritoCese $escrito_cese = null;

    private ?DateTimeLocal $f_cese = null;


    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

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

    public function getIdAsignaturaVo(): AsignaturaId
    {
        return $this->id_asignatura;
    }

    public function setIdAsignaturaVo(AsignaturaId|int|null $valor = null): void
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
    public function setId_asignatura(?int $valor = null): void
    {
        $this->id_asignatura = AsignaturaId::fromNullableInt($valor);
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
        $oProfesorAmpliacionSet = new Set();

        $oProfesorAmpliacionSet->add($this->getDatosId_nom());
        $oProfesorAmpliacionSet->add($this->getDatosId_asignatura());
        $oProfesorAmpliacionSet->add($this->getDatosEscrito_nombramiento());
        $oProfesorAmpliacionSet->add($this->getDatosF_nombramiento());
        $oProfesorAmpliacionSet->add($this->getDatosEscrito_cese());
        $oProfesorAmpliacionSet->add($this->getDatosF_cese());
        return $oProfesorAmpliacionSet->getTot();
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