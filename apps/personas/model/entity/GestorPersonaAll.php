<?php

namespace personas\model\entity;

use core;

/**
 * GestorPersonaAll
 *
 * Dado que no parece que vaya a ser necesario asilar completamente los esquemas, con esta
 * clase podré consultar la tabla padre de todas las personas. Es útil cuando no se encuentra
 * la persona en la delegación que se espera.
 *
 */
class GestorPersonaAll extends core\ClaseGestor
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

        $mi_id_schema = core\ConfigGlobal::mi_id_schema();
        // buscar los 'A' de mi schema
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion = 'A' AND id_schema = $mi_id_schema ";
        if ($this->ejecutar($sql) !== FALSE) {
            return new PersonaDl($id_nom);
        }

        // sino, los que vienen de otra dl
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion = 'A' ORDER BY f_situacion";
        if ($oDblSt = $this->ejecutar($sql)) {
            foreach ($oDblSt as $aDades) {
                $d_schema_persona = $aDades['id_schema'];
            }
            return new PersonaIn($id_nom);
        }

        // que esté en la dl, pero no en situación = 'A'
        // buscar los 'A' de mi schema
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion != 'A' AND id_schema = $mi_id_schema ";
        if ($this->ejecutar($sql) !== FALSE) {
            return new PersonaDl($id_nom);
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
        if (empty($oDblSt)) {
            return FALSE;
        }

        return $oDblSt;
    }

    private function generarEntidad($tipo)
    {
        switch ($tipo) {
            case 'Dl':
                $oPersona = new PersonaDl($this->id_nom);
                break;
            case 'Ex':
                $oPersona = new PersonaEx($this->id_nom);
                break;
            case 'In':
                $oPersona = new PersonaIn($this->id_nom);
                break;
        }

        $oPersona->DBCarregar();

        return $oPersona;
    }

}
