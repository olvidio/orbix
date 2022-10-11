<?php

namespace cartaspresentacion\model\entity;

/**
 * GestorCartaPresentacion
 *
 * Classe per gestionar la llista d'objectes de la clase CartaPresentacion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/3/2019
 */
class GestorCartaPresentacionDl extends GestorCartaPresentacion
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('du_presentacion_dl');
    }
}
