<?php
namespace actividades\model\entity;

/**
 * GestorActividadDl
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorActividadDl extends GestorActividadAll
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorActividadDl
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_actividades_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

}

?>
