<?php
namespace notas\model\entity;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;

/**
 * GestorPersonaNotaDl
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaNotaDl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorPersonaNotaOtraRegionStgr extends GestorPersonaNota
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct($esquema_region_stgr, $a_id = '')
    {
        // se debe conectar con la region del stgr padre
        $oConfigDB = new ConfigDB('comun'); //de la database comun
        $oConfigDB->setDataBase('sv');
        $config = $oConfigDB->getEsquema($esquema_region_stgr);
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_otra_region_stgr');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* MÉTODOS PROTECTED --------------------------------------------------------*/
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
