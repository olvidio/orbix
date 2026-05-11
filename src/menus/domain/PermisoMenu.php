<?php

namespace src\menus\domain;

use src\permisos\domain\XPermisos;

/**
 * Para saber los permisos de un usuario sobre los menus.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 4/12/2010
 */
class PermisoMenu extends XPermisos
{

    public $todos;

    public function __construct()
    {
        $this->iaccion = $_SESSION['iPermMenus']?? 1;
        $this->omplir();
    }

    public function omplir(): array
    {
        $this->permissions = PermisoMenuBits::map();

        return $this->permissions;
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * devuelve true o false si és visible o no.
     *
     * @param integer $perm_menu permiso del menú.
     * @return boolean
     */
    function visible($perm_menu)
    {
        if ($this->have_perm_bit($perm_menu)) {
            return true;
        } else {
            return false;
        }
    }

}
