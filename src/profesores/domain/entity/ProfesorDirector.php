<?php

namespace src\profesores\domain\entity;

use core\DatosCampo;
use core\Set;
use src\profesores\domain\value_objects\EscritoNombramiento;
use src\profesores\domain\value_objects\FechaNombramiento;
use src\profesores\domain\value_objects\EscritoCese;
use src\profesores\domain\value_objects\FechaCese;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use web\DateTimeLocal;
use web\NullDateTimeLocal;

/**
 * Clase que implementa la entidad d_profesor_director
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class ProfesorDirector
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_item de ProfesorDirector
     *
     * @var int
     */
    private int $iid_item;
    /**
     * Id_nom de ProfesorDirector
     *
     * @var int
     */
    private int $iid_nom;
    /**
     * Id_departamento de ProfesorDirector
     *
     * @var int
     */
    private int $iid_departamento;
    /**
     * Escrito_nombramiento de ProfesorDirector
     *
     * @var string|null
     */
    private string|null $sescrito_nombramiento = null;
    /**
     * F_nombramiento de ProfesorDirector
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_nombramiento = null;
    /**
     * Escrito_cese de ProfesorDirector
     *
     * @var string|null
     */
    private string|null $sescrito_cese = null;
    /**
     * F_cese de ProfesorDirector
     *
     * @var DateTimeLocal|null
     */
    private DateTimeLocal|null $df_cese = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ProfesorDirector
     */
    public function setAllAttributes(array $aDatos): ProfesorDirector
    {
        if (array_key_exists('id_item', $aDatos)) {
            $this->setId_item($aDatos['id_item']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        if (array_key_exists('id_departamento', $aDatos)) {
            $this->setId_departamento($aDatos['id_departamento']);
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
     * @return int $iid_departamento
     */
    public function getId_departamento(): int
    {
        return $this->iid_departamento;
    }

    /**
     *
     * @param int $iid_departamento
     */
    public function setId_departamento(int $iid_departamento): void
    {
        $this->iid_departamento = $iid_departamento;
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

    function getDatosCampos():array
    {
        $oProfesorDirectorSet = new Set();

        $oProfesorDirectorSet->add($this->getDatosId_departamento());
        $oProfesorDirectorSet->add($this->getDatosEscrito_nombramiento());
        $oProfesorDirectorSet->add($this->getDatosF_nombramiento());
        $oProfesorDirectorSet->add($this->getDatosEscrito_cese());
        $oProfesorDirectorSet->add($this->getDatosF_cese());
        return $oProfesorDirectorSet->getTot();
    }

    function getDatosId_departamento(): DatosCampo
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