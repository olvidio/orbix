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


    /**
     * Constructor de la classe.
     *
     * @return $gestor
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /* METODES PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
