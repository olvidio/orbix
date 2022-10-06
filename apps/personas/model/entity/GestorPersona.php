<?php

namespace personas\model\entity;

use core;

/**
 * GestorPersona
 *
 * Classe per gestionar la llista d'objectes de la clase Persona
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorPersona extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    private $aClases = [];

    /* CONSTRUCTOR -------------------------------------------------------------- */


    /**
     * Constructor de la classe.
     *
     * @return $gestor
     *
     */
    function __construct()
    {
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * Define el array con los tipos de persona que hay que incluir en las búsquedas.
     *       $a_Clases[] = array('clase'=>'PersonaOut','get'=>'getPersonasOut');
     *       $a_Clases[] = array('clase'=>'PersonaEx','get'=>'getPersonasEx');
     *
     * @param array $aClases
     */
    public function setClases($aClases)
    {
        $this->aClases = $aClases;
    }

    /**
     * retorna l'array d'objectes de tipus Persona
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Persona
     */
    public function getPersonas($aWhere = array(), $aOperators = array())
    {
        $namespace = __NAMESPACE__;

        if (empty ($this->aClases)) {
            $a_Clases = [];
            /* Buscar en los tres tipos de asistente: Dl, IN y Out. */
            $a_Clases[] = array('clase' => 'PersonaDl', 'get' => 'getPersonasDl');
            $a_Clases[] = array('clase' => 'PersonaIn', 'get' => 'getPersonasIn');
            $a_Clases[] = array('clase' => 'PersonaOut', 'get' => 'getPersonasOut');
            $a_Clases[] = array('clase' => 'PersonaEx', 'get' => 'getPersonasEx');
        } else {
            $a_Clases = $this->aClases;
        }

        return $this->getConjunt($a_Clases, $namespace, $aWhere, $aOperators);
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
