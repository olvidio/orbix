<?php

namespace src\zonassacd\domain\entity;
use core\DatosCampo;
use core\Set;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;

/**
 * Clase que implementa la entidad zonas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/12/2025
 */
class Zona
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_zona de Zona
     *
     * @var int
     */
    private int $iid_zona;
    /**
     * Nombre_zona de Zona
     *
     * @var string
     */
    private string $snombre_zona;
    /**
     * Orden de Zona
     *
     * @var int|null
     */
    private int|null $iorden = null;
    /**
     * Id_grupo de Zona
     *
     * @var int|null
     */
    private int|null $iid_grupo = null;
    /**
     * Id_nom de Zona
     *
     * @var int|null
     */
    private int|null $iid_nom = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return Zona
     */
    public function setAllAttributes(array $aDatos): Zona
    {
        if (array_key_exists('id_zona', $aDatos)) {
            $this->setId_zona($aDatos['id_zona']);
        }
        if (array_key_exists('nombre_zona', $aDatos)) {
            $this->setNombre_zona($aDatos['nombre_zona']);
        }
        if (array_key_exists('orden', $aDatos)) {
            $this->setOrden($aDatos['orden']);
        }
        if (array_key_exists('id_grupo', $aDatos)) {
            $this->setId_grupo($aDatos['id_grupo']);
        }
        if (array_key_exists('id_nom', $aDatos)) {
            $this->setId_nom($aDatos['id_nom']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_zona
     */
    public function getId_zona(): int
    {
        return $this->iid_zona;
    }

    /**
     *
     * @param int $iid_zona
     */
    public function setId_zona(int $iid_zona): void
    {
        $this->iid_zona = $iid_zona;
    }

    /**
     *
     * @return string $snombre_zona
     */
    public function getNombre_zona(): string
    {
        return $this->snombre_zona;
    }

    /**
     *
     * @param string $snombre_zona
     */
    public function setNombre_zona(string $snombre_zona): void
    {
        $this->snombre_zona = $snombre_zona;
    }

    /**
     *
     * @return int|null $iorden
     */
    public function getOrden(): ?int
    {
        return $this->iorden;
    }

    /**
     *
     * @param int|null $iorden
     */
    public function setOrden(?int $iorden = null): void
    {
        $this->iorden = $iorden;
    }

    /**
     *
     * @return int|null $iid_grupo
     */
    public function getId_grupo(): ?int
    {
        return $this->iid_grupo;
    }

    /**
     *
     * @param int|null $iid_grupo
     */
    public function setId_grupo(?int $iid_grupo = null): void
    {
        $this->iid_grupo = $iid_grupo;
    }

    /**
     *
     * @return int|null $iid_nom
     */
    public function getId_nom(): ?int
    {
        return $this->iid_nom;
    }

    /**
     *
     * @param int|null $iid_nom
     */
    public function setId_nom(?int $iid_nom = null): void
    {
        $this->iid_nom = $iid_nom;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_zona';
    }
 function getDatosCampos(): array
    {
        $oProfesorSet = new Set();
        $oProfesorSet->add($this->getDatosNombre_zona());
        $oProfesorSet->add($this->getDatosOrden());
        $oProfesorSet->add($this->getDatosId_grupo());
        $oProfesorSet->add($this->getDatosId_nom());
        return $oProfesorSet->getTot();
    }

    /**
     * Recupera les propietats de l'atribut snombre_zona de Zona
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosNombre_zona()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre_zona');
        $oDatosCampo->setMetodoGet('getNombre_zona');
        $oDatosCampo->setMetodoSet('setNombre_zona');
        $oDatosCampo->setEtiqueta(_("nombre zona"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('30');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iorden de Zona
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosOrden()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('orden');
        $oDatosCampo->setMetodoGet('getOrden');
        $oDatosCampo->setMetodoSet('setOrden');
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument('5');
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_grupo de Zona
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_grupo()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_grupo');
        $oDatosCampo->setMetodoGet('getId_grupo');
        $oDatosCampo->setMetodoSet('setId_grupo');
        $oDatosCampo->setEtiqueta(_("grupo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(ZonaGrupoRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNombre_grupo'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayZonaGrupos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    /**
     * Recupera les propietats de l'atribut iid_nom de Zona
     * en una clase del tipus DatosCampo
     *
     * @return DatosCampo
     */
    function getDatosId_nom()
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_nom');
        $oDatosCampo->setMetodoGet('getId_nom');
        $oDatosCampo->setMetodoSet('setId_nom');
        $oDatosCampo->setEtiqueta(_("jefe zona"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(PersonaDlRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getPrefApellidosNombre'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArraySacd'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }
}