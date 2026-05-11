<?php

namespace src\dossiers\application;

use src\dossiers\domain\PermisoDossierBits;
use src\permisos\domain\XPermisos;

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

    private function omplir(): void
    {
        $this->permissions = PermisoDossierBits::labeledMap();
    }

}