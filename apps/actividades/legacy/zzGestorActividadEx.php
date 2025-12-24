<?php
namespace actividades\legacy;

use actividades\model\entity\GestorActividadAll;

/**
 * GestorActividadEx
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorActividadEx extends GestorActividadAll
{
    /* ATRIBUTOS ----------------------------------------------------------------- */


    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorActividadEx
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBRC'];
        $oDbl_Select = $GLOBALS['oDBRC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_actividades_ex');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

}

?>
