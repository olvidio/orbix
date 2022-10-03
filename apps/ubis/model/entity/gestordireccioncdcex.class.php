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
class GestorDireccionCdcEx extends GestorDireccionCdc
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
        $oDbl = $GLOBALS['oDBRC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('u_dir_cdc_ex');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* METODES PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}

?>
