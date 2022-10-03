<?php

namespace personas\model\entity;
/**
 * GestorPersonaIn
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaIn
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorPersonaIn extends GestorPersonaPub
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
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_de_paso');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus PersonaIn
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus PersonaIn
     */
    function getPersonasIn($aWhere = array(), $aOperators = array())
    {
        return parent::getPersonasObj($aWhere, $aOperators, 'personas\\model\entity\\PersonaIn');
    }

    /* METODES PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
