<?php

namespace src\zonassacd\domain\entity;

use core\DatosCampo;
use core\Set;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\traits\Hydratable;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\value_objects\NombreZona;


class Zona
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_zona;

    private NombreZona $nombre_zona;

    private int|null $orden = null;

    private int|null $id_grupo = null;

    private int|null $id_nom = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_zona(): int
    {
        return $this->id_zona;
    }

    public function setId_zona(int $id_zona): void
    {
        $this->id_zona = $id_zona;
    }

    public function getNombreZonaVo(): NombreZona
    {
        return $this->nombre_zona;
    }

    public function setNombreZonaVo(NombreZona|string $oNombreZona): void
    {
        $this->nombre_zona = $oNombreZona instanceof NombreZona? $oNombreZona : new NombreZona($oNombreZona);
    }

    /**
     * @deprecated use getNombreZonaVo()
     */
    public function getNombre_zona(): string
    {
        return $this->nombre_zona->value();
    }

    /**
     * @deprecated use setNombreZonaVo()
     */
    public function setNombre_zona(string $nombre_zona): void
    {
        $this->nombre_zona = new NombreZona($nombre_zona);
    }


    public function getOrden(): ?int
    {
        return $this->orden;
    }


    public function setOrden(?int $orden = null): void
    {
        $this->orden = $orden;
    }


    public function getId_grupo(): ?int
    {
        return $this->id_grupo;
    }


    public function setId_grupo(?int $id_grupo = null): void
    {
        $this->id_grupo = $id_grupo;
    }


    public function getId_nom(): ?int
    {
        return $this->id_nom;
    }


    public function setId_nom(?int $id_nom = null): void
    {
        $this->id_nom = $id_nom;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key(): string
    {
        return 'id_zona';
    }

    public function getDatosCampos(): array
    {
        $oProfesorSet = new Set();
        $oProfesorSet->add($this->getDatosNombre_zona());
        $oProfesorSet->add($this->getDatosOrden());
        $oProfesorSet->add($this->getDatosId_grupo());
        $oProfesorSet->add($this->getDatosId_nom());
        return $oProfesorSet->getTot();
    }

    /**
     * Recupera las propiedades del atributo nombre_zona de Zona
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosNombre_zona(): DatosCampo
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
     * Recupera las propiedades del atributo orden de Zona
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosOrden(): DatosCampo
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
     * Recupera las propiedades del atributo id_grupo de Zona
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_grupo(): DatosCampo
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
     * Recupera las propiedades del atributo id_nom de Zona
     * en una clase del tipo DatosCampo
     *
     * @return DatosCampo
     */
    private function getDatosId_nom(): DatosCampo
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