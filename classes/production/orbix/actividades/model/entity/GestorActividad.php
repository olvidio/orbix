<?php

namespace actividades\model\entity;
/**
 * GestorActividad
 *
 * Classe per gestionar la llista d'objectes de la clase Actividad
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorActividad extends GestorActividadAll
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorActividad
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];

        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('av_actividades');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

}