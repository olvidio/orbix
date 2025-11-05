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
class GestorCtrDlxDireccion extends GestorCtrxDireccion
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
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('u_cross_ctr_dl_dir');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* MÉTODOS PROTECTED --------------------------------------------------------*/
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
