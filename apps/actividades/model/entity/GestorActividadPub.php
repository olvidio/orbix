<?php
namespace actividades\model\entity;

/**
 * GestorActividadPub
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadPub
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorActividadPub extends GestorActividadAll
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorActividadPub
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('av_actividades_pub');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
}

?>
