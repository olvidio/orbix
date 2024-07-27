<?php
namespace notas\model\entity;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;

/**
 * Fitxer amb la Classe que accedeix a la taula e_notas_otra_region_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/07/2024
 */

/**
 * Clase que implementa la entidad e_notas_otra_region_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/07/2024
 */
class PersonaNotaOtraRegionStgr extends PersonaNota
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_nom,iid_nivel
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($esquema_region_stgr, $a_id = '')
    {
        $db = (ConfigGlobal::mi_sfsv() === 1 )? 'sv' : 'sf';
        // se debe conectar con la region del stgr padre
        $oConfigDB = new ConfigDB($db); //de la database sv/sf
        $config = $oConfigDB->getEsquema($esquema_region_stgr);
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();

        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id;
                if (($nom_id === 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id;
                if (($nom_id === 'id_nivel') && $val_id !== '') $this->iid_nivel = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_otra_region_stgr');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/
}
