<?php
namespace ubis\model\entity;

/**
 * GestorDireccionCtrEx
 *
 * Classe per gestionar la llista d'objectes de la clase DireccionCtrEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorDireccionCtrEx extends GestorDireccionCtr
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorDireccionCtrEx
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('u_dir_ctr_ex');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
