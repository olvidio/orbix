<?php
namespace ubis\model\entity;

/**
 * GestorCasaDl
 *
 * Classe per gestionar la llista d'objectes de la clase CasaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */
class GestorCasaDl extends GestorCasa
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorCasaDl
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('u_cdc_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
