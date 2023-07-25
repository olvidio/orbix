<?php
namespace ubis\model\entity;

use core;
use web;

/**
 * GestorUbi
 *
 * Classe per gestionar la llista d'objectes de la clase Ubi
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorUbi extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorUbi
     *
     */
    function __construct()
    {
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles ubis
     *
     * @param string optional $sCondicion Condició de búsqueda (amb el WHERE).
     * @return array Una Llista
     */
    function getListaUbis($sCondicion = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$sCondicion
				ORDER BY nombre_ubi";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorUbi.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new web\Desplegable('', $oDblSt, '', true);
    }


    /**
     * retorna l'array d'objectes de tipus Ubi
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Ubi
     */
    function getUbis($aWhere = array(), $aOperators = array())
    {
        $a_Clases[] = array('clase' => 'Casa', 'get' => 'getCasas');
        $a_Clases[] = array('clase' => 'Centro', 'get' => 'getCentros');

        $namespace = __NAMESPACE__;
        return $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
