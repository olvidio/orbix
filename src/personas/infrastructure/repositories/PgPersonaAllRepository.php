<?php

namespace src\personas\infrastructure\repositories;

use core\ClaseRepository;
use core\ConfigGlobal;
use src\personas\domain\contracts\PersonaAllRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;

/**
 * GestorPersonaAll
 *
 * Dado que no parece que vaya a ser necesario asilar completamente los esquemas, con esta
 * clase podré consultar la tabla padre de todas las personas. Es útil cuando no se encuentra
 * la persona en la delegación que se espera.
 *
 */
class PgPersonaAllRepository extends ClaseRepository implements PersonaAllRepositoryInterface
{
    private int $id_nom;

    function __construct()
    {
        /*
            $oConfigDB = new core\ConfigDB('importar'); //de la database sv
    $config = $oConfigDB->getEsquema('publicv');
    $oConexion = new core\DBConnection($config);
    $oDevelPC = $oConexion->getPDO();

        $this->setoDbl($oDevelPC);
*/

        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('global.personas');
    }

    public function getPersonaByIdNom($id_nom)
    {
        $this->id_nom = $id_nom;

        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $mi_id_schema = ConfigGlobal::mi_id_schema();
        // buscar los 'A' de mi schema
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion = 'A' AND id_schema = $mi_id_schema ";
        if ($this->ejecutar($sql) !== FALSE) {
            $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
            return $PersonaDlRepository->findById($id_nom);
        }

        // sino, los que vienen de otra dl
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion = 'A' ORDER BY f_situacion";
        if ($oDblSt = $this->ejecutar($sql)) {
            foreach ($oDblSt as $aDades) {
                $id_schema_persona = $aDades['id_schema'];
            }
            // Si hay más de uno, me quedo con el que tiene la fecha de cambio situación más reciente.
            // Lo marco como publicado
            $sql = "UPDATE $nom_tabla SET es_publico = 't' WHERE id_nom=$id_nom AND situacion = 'A' AND id_schema = $id_schema_persona";
            if ($oDblSt = $this->ejecutar($sql)) {

            }

        }

        // que esté en la dl, pero no en situación = 'A'
        // buscar los distintos de 'A' de mi schema
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion != 'A' AND id_schema = $mi_id_schema ";
        if ($this->ejecutar($sql) !== FALSE) {
            $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
            return $PersonaDlRepository->findById($id_nom);
        }
        return sprintf(_("no encuentro a nadie con id: %s"), $id_nom);
    }

    private function ejecutar($sql)
    {
        $oDbl = $this->getoDbl();
        if (($oDblSt = $oDbl->query($sql)) === false) {
            $sClauError = 'PersonaAll.select';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (empty($oDblSt->rowCount())) {
            return FALSE;
        }

        return $oDblSt;
    }

}
