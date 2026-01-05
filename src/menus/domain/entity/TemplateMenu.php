<?php

namespace src\menus\domain\entity;

use core\DatosCampo;
use core\Set;
use src\menus\domain\value_objects\TemplateMenuName;
use src\shared\domain\traits\Hydratable;


class TemplateMenu
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_template_menu;

    private string|null $nombre = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_template_menu(): int
    {
        return $this->id_template_menu;
    }


    public function setId_template_menu(int $id_template_menu): void
    {
        $this->id_template_menu = $id_template_menu;
    }


    public function getNombre(): ?string
    {
        return $this->nombre;
    }


    public function setNombre(string|TemplateMenuName|null $nombre = null): void
    {
        $this->nombre = $nombre instanceof TemplateMenuName ? $nombre->value() : $nombre;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_template_menu';
    }

    public function getDatosCampos(): array
    {
        $oSet = new Set();

        $oSet->add($this->getDatosNombre());
        return $oSet->getTot();
    }

    private function getDatosNombre(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('nombre');
        $oDatosCampo->setMetodoGet('getNombre');
        $oDatosCampo->setMetodoSet('setNombre');
        $oDatosCampo->setEtiqueta(_("nombre plantilla menú"));
        $oDatosCampo->setTipo('texto');
        return $oDatosCampo;
    }
}