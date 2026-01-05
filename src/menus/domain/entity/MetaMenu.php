<?php

namespace src\menus\domain\entity;

use core\DatosCampo;
use core\Set;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\menus\domain\value_objects\MetaMenuUrl;
use src\menus\domain\value_objects\MetaMenuParametros;
use src\menus\domain\value_objects\MetaMenuDescripcion;
use src\shared\domain\traits\Hydratable;

class MetaMenu
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_metamenu;

    private int|null $id_mod = null;

    private string|null $url = null;

    private string|null $parametros = null;

    private string|null $descripcion = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_metamenu(): int
    {
        return $this->id_metamenu;
    }


    public function setId_metamenu(int $id_metamenu): void
    {
        $this->id_metamenu = $id_metamenu;
    }


    public function getId_mod(): ?int
    {
        return $this->id_mod;
    }


    public function setId_mod(?int $id_mod = null): void
    {
        $this->id_mod = $id_mod;
    }


    public function getUrl(): ?string
    {
        return $this->url;
    }


    public function setUrl(string|MetaMenuUrl|null $url = null): void
    {
        $this->url = $url instanceof MetaMenuUrl ? $url->value() : $url;
    }


    public function getParametros(): ?string
    {
        return $this->parametros;
    }


    public function setParametros(string|MetaMenuParametros|null $parametros = null): void
    {
        $this->parametros = $parametros instanceof MetaMenuParametros ? $parametros->value() : $parametros;
    }


    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }


    public function setDescripcion(string|MetaMenuDescripcion|null $descripcion = null): void
    {
        $this->descripcion = $descripcion instanceof MetaMenuDescripcion ? $descripcion->value() : $descripcion;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_metamenu';
    }

    public function getDatosCampos(): array
    {
        $oMetamenuSet = new Set();

        $oMetamenuSet->add($this->getDatosModulo());
        $oMetamenuSet->add($this->getDatosUrl());
        $oMetamenuSet->add($this->getDatosParametros());
        $oMetamenuSet->add($this->getDatosDescripcion());
        return $oMetamenuSet->getTot();
    }

    private function getDatosModulo(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('id_mod');
        $oDatosCampo->setMetodoGet('getId_mod');
        $oDatosCampo->setMetodoSet('setId_mod');
        $oDatosCampo->setEtiqueta(_("modulo"));
        $oDatosCampo->setTipo('opciones');
        $oDatosCampo->setArgument(ModuloRepositoryInterface::class); // nombre del objeto relacionado
        $oDatosCampo->setArgument2('getNom'); // método para obtener el valor a mostrar del objeto relacionado.
        $oDatosCampo->setArgument3('getArrayModulos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
        return $oDatosCampo;
    }

    private function getDatosUrl(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('url');
        $oDatosCampo->setMetodoGet('getUrl');
        $oDatosCampo->setMetodoSet('setUrl');
        $oDatosCampo->setEtiqueta(_("url"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(60);
        return $oDatosCampo;
    }

    private function getDatosParametros(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('parametros');
        $oDatosCampo->setMetodoGet('getParametros');
        $oDatosCampo->setMetodoSet('setParametros');
        $oDatosCampo->setEtiqueta(_("parametros"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    private function getDatosDescripcion(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('descripcion');
        $oDatosCampo->setMetodoGet('getDescripcion');
        $oDatosCampo->setMetodoSet('setDescripcion');
        $oDatosCampo->setEtiqueta(_("descripción"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(50);
        return $oDatosCampo;
    }

}