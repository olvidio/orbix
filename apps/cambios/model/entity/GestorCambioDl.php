<?php

namespace cambios\model\entity;
/**
 * GestorCambio
 *
 * Classe per gestionar la llista d'objectes de la clase Cambio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class GestorCambioDl extends GestorCambio
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
