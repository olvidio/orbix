<?php
namespace personas\model\entity;

/**
 * GestorPersonaDl
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorPersonaDl extends GestorPersonaGlobal
{
    /* ATRIBUTOS ----------------------------------------------------------------- */


    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('personas_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus PersonaDl
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus PersonaDl
     */
    function getPersonasDl($aWhere = array(), $aOperators = array())
    {
        return parent::getPersonasObj('personas\\model\entity\\PersonaDl', $aWhere, $aOperators);
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
