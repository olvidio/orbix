<?php

namespace src\menus\domain\entity;

use core\DatosCampo;
use core\Set;
use src\menus\domain\value_objects\GrupMenuName;
use src\shared\domain\traits\Hydratable;


class GrupMenu
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private int $id_grupmenu;

    private GrupMenuName $grup_menu;

    private ?int $orden = null;

    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */

    /**
     * Equivalencias de nomenclatura entre la dl => cr
     *
     * @var array
     */
    private $aEquivalencias = [
        'dre' => 'der',
        'vest' => 'dle',
        'scdl' => 'scr',
        'vcd' => 'vcr',
        'vcsd' => 'vcsr',
    ];
    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function getId_grupmenu(): int
    {
        return $this->id_grupmenu;
    }


    public function setId_grupmenu(int $id_grupmenu): void
    {
        $this->id_grupmenu = $id_grupmenu;
    }


    public function getGrup_menu($dl_r = 'dl'): string
    {
        $sgrupmenu = $this->grup_menu->value();
        if ($dl_r === 'r' || $dl_r === 'rstgr') {
            if (!empty($this->aEquivalencias[$this->grup_menu->value()])) {
                $sgrupmenu = $this->aEquivalencias[$this->grup_menu->value()];
            }
        }
        return $sgrupmenu;
    }

    public function setGrup_menu(string $grup_menu): void
    {
        $this->grup_menu = GrupMenuName::fromNullableString($grup_menu);
    }

    public function getGrupMenuVo(): GrupMenuName
    {
        return $this->grup_menu;
    }

    public function setGrupMenuVo(GrupMenuName|string $texto): void
    {
        $this->grup_menu = $texto instanceof GrupMenuName
            ? $texto
            : GrupMenuName::fromNullableString($texto);
    }


    public function getOrden(): ?int
    {
        return $this->orden;
    }


    public function setOrden(?int $orden = null): void
    {
        $this->orden = $orden;
    }

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_grupmenu';
    }

    public function getDatosCampos(): array
    {
        $oMetamenuSet = new Set();

        $oMetamenuSet->add($this->getDatosGrupMenu());
        $oMetamenuSet->add($this->getDatosOrden());
        return $oMetamenuSet->getTot();
    }

    private function getDatosGrupMenu(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('grup_menu');
        $oDatosCampo->setMetodoGet('getGrup_menu');
        $oDatosCampo->setMetodoSet('setGrup_menu');
        $oDatosCampo->setEtiqueta(_("Grup Menu"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(30);
        return $oDatosCampo;
    }

    private function getDatosOrden(): DatosCampo
    {
        $oDatosCampo = new DatosCampo();
        $oDatosCampo->setNom_camp('orden');
        $oDatosCampo->setMetodoGet('getOrden');
        $oDatosCampo->setMetodoSet('setOrden');
        $oDatosCampo->setEtiqueta(_("orden"));
        $oDatosCampo->setTipo('texto');
        $oDatosCampo->setArgument(10);
        return $oDatosCampo;
    }
}