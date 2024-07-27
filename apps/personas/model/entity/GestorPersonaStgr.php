<?php

namespace personas\model\entity;

use ubis\model\entity\GestorDelegacion;

/**
 * GestorPersonaEx
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorPersonaStgr extends GestorPersonaGlobal
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    function __construct()
    {
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_de_paso_ex');
    }

    public function getPerosnasOtrosStgr(string $Qapellidos)
    {
        // Buscar dl y r dependientes de la actual región del stgr:
        $schema = $_SESSION['session_auth']['esquema'];
        $a_reg = explode('-', $schema);
        $RegionStgr = $a_reg[0];
        $gesDl = new GestorDelegacion();
        $a_dl_de_la_region_stgr = $gesDl->getArrayDlRegionStgr([$RegionStgr]);
        $str_dl = "'" . implode("', '", $a_dl_de_la_region_stgr) . "'";

        // Buscar en depaso

        // Buscar en el resto de dl (global menos mis dl)


    }
}
