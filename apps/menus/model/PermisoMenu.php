<?php

namespace menus\model;

use permisos\model as permisos;

/**
 * Classe per saber els permisos d'un usuari sobre els menus.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 4/12/2010
 */
class PermisoMenu extends permisos\XPermisos
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * sPermLogin de PermisoMenu
     *
     * @var string llista de valors separats per comes amb els permisos.
     */
    private $sPermLogin;

    public $todos;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     */
    public function __construct()
    {
        $this->iaccion = $_SESSION['iPermMenus'];
        self::omplir();
    }

    public static function omplir()
    {
        $permissions = [];
        $permissions['adl'] = 1;
        $permissions['pr'] = 1;
        $permissions['agd'] = 1 << 1; // 2
        $permissions['aop'] = 1 << 2; //4,
        $permissions['des'] = 1 << 3; //8,
        $permissions['est'] = 1 << 4; //16,
        $permissions['scdl'] = 1 << 5; //32,
        $permissions['scr'] = 1 << 5; //32,
        $permissions['sg'] = 1 << 6; //64,
        $permissions['sm'] = 1 << 7; //128,
        $permissions['soi'] = 1 << 8; //256,
        $permissions['sr'] = 1 << 9; //512,
        $permissions['vcsd'] = 1 << 10; //1024,
        $permissions['vcsr'] = 1 << 10; //1024,
        $permissions['dtor'] = 1 << 11; //2048,
        $permissions['ocs'] = 1 << 12; //4096,
        $permissions['sddl'] = 1 << 13; //8192,
        $permissions['nax'] = 1 << 14; //16384,
        $permissions['calendario'] = 1 << 15; //32768,
        $permissions['ctr'] = 1 << 16; //65536,
        $permissions['jefeZona'] = 1 << 17; //131072,
        $permissions['sacd'] = 1 << 18; //262144,
        $permissions['persona'] = 1 << 19;
        $permissions['casa'] = 1 << 20;
        $permissions['admin_sf'] = 1 << 21;
        $permissions['admin_sv'] = 1 << 25; // uno que se grande, para que sea el último

        self::$permissions = $permissions;
        
        return self::$permissions;
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Retorna true o false si és visible o no.
     *
     * @param integer permís del menú.
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
    /* MÉTODOS PRIVADOS --------------------------------------------------------- */

}
