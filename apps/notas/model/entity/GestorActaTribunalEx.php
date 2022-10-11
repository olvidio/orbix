<?php
namespace notas\model\entity;

use core;

/**
 * GestorActaTribunalEx
 *
 * Classe per gestionar la llista d'objectes de la clase ActaTribunalEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorActaTribunalEx extends GestorActaTribunal
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas_tribunal_ex');
    }


}

?>
