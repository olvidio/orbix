<?php
namespace ubis\model\entity;

/**
 * GestorDireccion
 *
 * Classe per gestionar la llista d'objectes de la clase Direccion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorDireccionCdcDl extends GestorDireccionCdc
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorDireccion
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('u_dir_cdc_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
