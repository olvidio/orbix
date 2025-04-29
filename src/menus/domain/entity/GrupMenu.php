<?php

namespace src\menus\domain\entity;

use core\DatosCampo;
use core\Set;

/**
 * Clase que implementa la entidad aux_grupmenu
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class GrupMenu
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Id_grupmenu de GrupMenu
     *
     * @var int
     */
    private int $iid_grupmenu;
    /**
     * Grup_menu de GrupMenu
     *
     * @var string
     */
    private string $sgrup_menu;
    /**
     * Orden de GrupMenu
     *
     * @var int|null
     */
    private int|null $iorden = null;

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

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return GrupMenu
     */
    public function setAllAttributes(array $aDatos): GrupMenu
    {
        if (array_key_exists('id_grupmenu', $aDatos)) {
            $this->setId_grupmenu($aDatos['id_grupmenu']);
        }
        if (array_key_exists('grup_menu', $aDatos)) {
            $this->setGrup_menu($aDatos['grup_menu']);
        }
        if (array_key_exists('orden', $aDatos)) {
            $this->setOrden($aDatos['orden']);
        }
        return $this;
    }

    /**
     *
     * @return int $iid_grupmenu
     */
    public function getId_grupmenu(): int
    {
        return $this->iid_grupmenu;
    }

    /**
     *
     * @param int $iid_grupmenu
     */
    public function setId_grupmenu(int $iid_grupmenu): void
    {
        $this->iid_grupmenu = $iid_grupmenu;
    }

    /**
     *
     * @return string $sgrup_menu
     */
    public function getGrup_menu($dl_r = 'dl'): string
    {
        $sgrupmenu = $this->sgrup_menu;
        if ($dl_r === 'r' || $dl_r === 'rstgr') {
            if (!empty($this->aEquivalencias[$this->sgrup_menu])) {
                $sgrupmenu = $this->aEquivalencias[$this->sgrup_menu];
            }
        }
        return $sgrupmenu;
    }

    /**
     *
     * @param string $sgrup_menu
     */
    public function setGrup_menu(string $sgrup_menu): void
    {
        $this->sgrup_menu = $sgrup_menu;
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

    /* ------------------- PARA el mod_tabla  -------------------------------*/
    public function getPrimary_key()
    {
        return 'id_grupmenu';
    }

    function getDatosCampos()
    {
        $oMetamenuSet = new Set();

        $oMetamenuSet->add($this->getDatosGrupMenu());
        $oMetamenuSet->add($this->getDatosOrden());
        return $oMetamenuSet->getTot();
    }

    function getDatosGrupMenu()
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

    function getDatosOrden()
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