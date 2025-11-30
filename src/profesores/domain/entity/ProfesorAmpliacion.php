<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\profesores\domain\value_objects\FechaNombramiento;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\FechaCese;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Clase que implementa la entidad d_profesor_ampliacion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class ProfesorAmpliacion
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de ProfesorAmpliacion
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_nom de ProfesorAmpliacion
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Id_asignatura de ProfesorAmpliacion
     *
     * @var int
     */
    private int $iid_asignatura;
    /**
     * Escrito_nombramiento de ProfesorAmpliacion
     *
     * @var string|null
     */
    private string|null $sescrito_nombramiento = null;
    /**
     * F_nombramiento de ProfesorAmpliacion
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_nombramiento = null;
    /**
     * Escrito_cese de ProfesorAmpliacion
     *
     * @var string|null
     */
    private string|null $sescrito_cese = null;
    /**
     * F_cese de ProfesorAmpliacion
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_cese = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorAmpliacion
     */
    public function setAllAttributes(array $aDatos): ProfesorAmpliacion
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
        if (array_key_exists('escrito_nombramiento', $aDatos)) {
            $this->setEscritoNombramientoVo(EscritoNombramiento::fromNullable($aDatos['escrito_nombramiento']));
        }
        if (array_key_exists('f_nombramiento', $aDatos)) {
            $this->setFechaNombramientoVo(FechaNombramiento::fromNullable($aDatos['f_nombramiento']));
        }
        if (array_key_exists('escrito_cese', $aDatos)) {
            $this->setEscritoCeseVo(EscritoCese::fromNullable($aDatos['escrito_cese']));
        }
        if (array_key_exists('f_cese', $aDatos)) {
            $this->setFechaCeseVo(FechaCese::fromNullable($aDatos['f_cese']));
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

    // Métodos VO nuevos
    public function getEscritoNombramientoVo(): ?EscritoNombramiento
    {
        return EscritoNombramiento::fromNullable($this->sescrito_nombramiento);
    }

    public function setEscritoNombramientoVo(?EscritoNombramiento $escrito): void
    {
        $this->sescrito_nombramiento = $escrito?->value();
    }

    public function getFechaNombramientoVo(): ?FechaNombramiento
    {
        return FechaNombramiento::fromNullable($this->df_nombramiento);
    }

    public function setFechaNombramientoVo(?FechaNombramiento $fecha): void
    {
        $this->df_nombramiento = $fecha?->value();
    }

    public function getEscritoCeseVo(): ?EscritoCese
    {
        return EscritoCese::fromNullable($this->sescrito_cese);
    }

    public function setEscritoCeseVo(?EscritoCese $escrito): void
    {
        $this->sescrito_cese = $escrito?->value();
    }

    public function getFechaCeseVo(): ?FechaCese
    {
        return FechaCese::fromNullable($this->df_cese);
    }

    public function setFechaCeseVo(?FechaCese $fecha): void
    {
        $this->df_cese = $fecha?->value();
    }

    /**
     * @deprecated Usar getEscritoNombramientoVo()->value()
     */
    public function getEscrito_nombramiento(): ?string
    {
        return $this->sescrito_nombramiento;
    }

    /**
     * @deprecated Usar setEscritoNombramientoVo(EscritoNombramiento $vo)
     */
    public function setEscrito_nombramiento(?string $escrito_nombramiento = null): void
    {
        $this->sescrito_nombramiento = $escrito_nombramiento;
    }

    /**
     * @deprecated Usar getFechaNombramientoVo()->value()
     */
    public function getF_nombramiento(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_nombramiento ?? new NullDateTimeLocal;
    }

    /**
     * @deprecated Usar setFechaNombramientoVo(FechaNombramiento $vo)
     */
    public function setF_nombramiento(DateTimeLocal|null $df_nombramiento = null): void
    {
        $this->df_nombramiento = $df_nombramiento;
    }

    /**
     * @deprecated Usar getEscritoCeseVo()->value()
     */
    public function getEscrito_cese(): ?string
    {
        return $this->sescrito_cese;
    }

    /**
     * @deprecated Usar setEscritoCeseVo(EscritoCese $vo)
     */
    public function setEscrito_cese(?string $escrito_cese = null): void
    {
        $this->sescrito_cese = $escrito_cese;
    }

    /**
     * @deprecated Usar getFechaCeseVo()->value()
     */
    public function getF_cese(): DateTimeLocal|NullDateTimeLocal|null
    {
        return $this->df_cese ?? new NullDateTimeLocal;
    }

    /**
     * @deprecated Usar setFechaCeseVo(FechaCese $vo)
     */
    public function setF_cese(DateTimeLocal|null $df_cese = null): void
    {
        $this->df_cese = $df_cese;
    }


    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_item';
    }

    function getDatosCampos(): array
    {
        $oProfesorAmpliacionSet = new Set();

        $oProfesorAmpliacionSet->add($this->getDatosId_asignatura());
        $oProfesorAmpliacionSet->add($this->getDatosEscrito_nombramiento());
        $oProfesorAmpliacionSet->add($this->getDatosF_nombramiento());
        $oProfesorAmpliacionSet->add($this->getDatosEscrito_cese());
        $oProfesorAmpliacionSet->add($this->getDatosF_cese());
        return $oProfesorAmpliacionSet->getTot();
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

    function getDatosEscrito_nombramiento(): DatosCampo
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

    function getDatosF_nombramiento(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('f_nombramiento');
        $oDatosCampo->setMetodoGet('getF_nombramiento');
        $oDatosCampo->setMetodoSet('setF_nombramiento');
        $oDatosCampo->setEtiqueta(_("fecha de nombramiento"));
        $oDatosCampo->setTipo('fecha');
        return $oDatosCampo;
    }

    function getDatosEscrito_cese(): DatosCampo
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

    function getDatosF_cese(): DatosCampo
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