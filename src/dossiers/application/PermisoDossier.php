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

    /** @var mixed */
    public mixed $todos = null;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     */
    function __construct()
    {
        $this->iaccion = is_numeric($_SESSION['iPermMenus'] ?? null)
            ? (int) $_SESSION['iPermMenus']
            : 0;
        $this->omplir();
    }

    private function omplir(): void
    {
        $this->permissions = PermisoDossierBits::labeledMap();
    }

}