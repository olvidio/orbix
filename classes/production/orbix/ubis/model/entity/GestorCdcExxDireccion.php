<?php
namespace ubis\model\entity;


/**
 * GestorUbixDireccion
 *
 * Classe per gestionar la llista d'objectes de la clase UbixDireccion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */
class GestorCdcExxDireccion extends GestorCdcxDireccion
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorCdcxDireccion
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBRC'];
        $oDbl_Select = $GLOBALS['oDBRC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('u_cross_cdc_ex_dir');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* MÉTODOS PROTECTED --------------------------------------------------------*/
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
