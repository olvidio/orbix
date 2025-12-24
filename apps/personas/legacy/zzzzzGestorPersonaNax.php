<?php

namespace personas\legacy;
use personas\model\entity\GestorPersonaDl;

/**
 * GestorPersonaN
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaN
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorPersonaNax extends GestorPersonaDl
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_nax');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
