<?php

namespace actividadestudios\model\entity;
/**
 * GestorActividadAsignatura
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadAsignaturaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */
class GestorActividadAsignaturaDl extends GestorActividadAsignatura
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_asignaturas_activ_dl');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* MÉTODOS PROTECTED --------------------------------------------------------*/
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
