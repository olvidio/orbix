<?php

namespace personas\model\entity;
/**
 * GestorPersonaSSSC
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaSSSC
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorPersonaSSSC extends GestorPersonaDl
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
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_sssc');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}