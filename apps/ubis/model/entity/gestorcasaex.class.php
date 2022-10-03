<?php
namespace ubis\model\entity;

use core;

/**
 * GestorCasaEx
 *
 * Classe per gestionar la llista d'objectes de la clase CasaEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */
class GestorCasaEx extends GestorCasa
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorCasaEx
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBRC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('u_cdc_ex');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* METODES PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}

?>
