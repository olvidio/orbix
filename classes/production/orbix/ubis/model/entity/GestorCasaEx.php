<?php
namespace ubis\model\entity;

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
        $oDbl_Select = $GLOBALS['oDBRC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('u_cdc_ex');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
