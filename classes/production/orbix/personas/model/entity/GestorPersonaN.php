<?php

namespace personas\model\entity;
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
class GestorPersonaN extends GestorPersonaDl
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_numerarios');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
