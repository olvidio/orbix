<?php

namespace personas\model\entity;
/**
 * GestorPersonaSacd
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaSacd
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2019
 */
class GestorPersonaSacd extends GestorPersonaGlobal
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
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('cp_sacd');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}