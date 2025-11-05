<?php

namespace dossiers\model;

use core\ConfigGlobal;
use permisos\model\XPermisos;

/**
 * Classe per saber els permisos d'un usuari sobre els menus.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 4/12/2010
 */
class PermisoDossier extends XPermisos
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
    function __construct()
    {
        $this->iaccion = $_SESSION['iPermMenus'];
        $this->omplir();
    }

    private function omplir()
    {
        if (ConfigGlobal::mi_sfsv() == 1) $permissions['adl'] = 1;
        if (ConfigGlobal::mi_sfsv() == 2) $permissions['pr'] = 1;

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

       $this->permissions= $permissions;
    }

}