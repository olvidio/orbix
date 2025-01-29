<?php
namespace ubis\model\entity;

use core\ClaseGestor;

/**
 * GestorTelecoUbi
 *
 * Classe per gestionar la llista d'objectes de la clase TelecoUbi
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
abstract class GestorTelecoUbi extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorTelecoUbi
     *
     */
    function __construct()
    {
        /*
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_teleco_ubis');
        */
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus TelecoUbi
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus TelecoUbi
     */
    function getTelecos($aWhere = array(), $aOperators = array())
    {
        $a_Clases[] = array('clase' => 'TelecoCtr', 'get' => 'getTelecos');
        $a_Clases[] = array('clase' => 'TelecoCdc', 'get' => 'getTelecos');

        $namespace = __NAMESPACE__;
        return $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
    }
    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
