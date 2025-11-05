<?php

namespace ubis\model\entity;
/**
 * GestorCentro
 *
 * Classe per gestionar la llista d'objectes de la clase Centro
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 2/01/2020
 */
class GestorCentroEllos extends GestorCentro
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorCentro
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('cu_centros_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}