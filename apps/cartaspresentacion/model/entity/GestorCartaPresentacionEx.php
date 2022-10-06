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
class GestorCartaPresentacionEx extends GestorCartaPresentacion
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    /**
     * Constructor de la classe.
     *
     * @return $gestor
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('du_presentacion_ex');
    }
}
