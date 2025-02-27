<?php

namespace personas\model\entity;

use core\ClaseGestor;
use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use web\DateTimeLocal;
use profesores\model\entity\GestorProfesor;

/**
 * GestorPersonaAll
 *
 * Dado que no parece que vaya a ser necesario asilar completamente los esquemas, con esta
 * clase podré consultar la tabla padre de todas las personas. Es útil cuando no se encuentra
 * la persona en la delegación que se espera.
 *
 */
class GestorPersonaAll extends ClaseGestor
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
            return new PersonaDl($id_nom);
        }

        // sino, los que vienen de otra dl
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$id_nom AND situacion = 'A' ORDER BY f_situacion";
        if ($oDblSt = $this->ejecutar($sql)) {
            foreach ($oDblSt as $aDades) {
                $id_schema_persona = $aDades['id_schema'];
            }
            // Si hay más de uno, me quedo con el que tiene la fecha de cambio situación más reciente.
            // Es posible que no exista como personaOut y hay que crearla
            $oPersonaIN = new PersonaIn(['id_nom' => $id_nom, 'id_schema' => $id_schema_persona]);
            $nom = $oPersonaIN->getNom();
            if (!empty($nom)) {
                return $oPersonaIN;
            }

            // crear una nueva desde el esquema de la persona
            if ($this->nuevaPersonaOut($id_schema_persona)) {
                return new PersonaIn(['id_nom' => $id_nom, 'id_schema' => $id_schema_persona]);
            }
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
        if (empty($oDblSt->rowCount())) {
            return FALSE;
        }

        return $oDblSt;
    }

    private function nuevaPersonaOut($id_schema_persona)
    {
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom=$this->id_nom AND situacion = 'A' AND id_schema = $id_schema_persona";
        $oDblSt = $this->ejecutar($sql);
        if (empty($oDblSt->rowCount())) {
            return FALSE;
        }

        // en un mismo esquema sólo debería haber una
        $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);

        $schema_persona = $this->getSchemaFromId($id_schema_persona);

        $oPersonaOut = new PersonaOut($this->id_nom);
        // cambiar conexión al schema_persona:
        $oConfigDB = new ConfigDB('sv');
        $config = $oConfigDB->getEsquema($schema_persona);
        $oConexion = new DBConnection($config);
        $oDbl = $oConexion->getPDO();
        $oPersonaOut->setoDbl($oDbl);

        $oPersonaOut->setId_cr($aDades['id_cr']);
        $oPersonaOut->setId_tabla('p' . $aDades['id_tabla']);
        $oPersonaOut->setDl($aDades['dl']);
        $oPersonaOut->setSacd($aDades['sacd']);
        $oPersonaOut->setTrato($aDades['trato']);
        $oPersonaOut->setNom($aDades['nom']);
        $oPersonaOut->setNx1($aDades['nx1']);
        $oPersonaOut->setApellido1($aDades['apellido1']);
        $oPersonaOut->setNx2($aDades['nx2']);
        $oPersonaOut->setApellido2($aDades['apellido2']);
        $oPersonaOut->setF_nacimiento($aDades['f_nacimiento'],false);
        $oPersonaOut->setLengua($aDades['lengua']);
        $oPersonaOut->setSituacion($aDades['situacion']);
        $oPersonaOut->setF_situacion($aDades['f_situacion'], false);
        $oPersonaOut->setApel_fam($aDades['apel_fam']);
        $oPersonaOut->setInc($aDades['inc']);
        $oPersonaOut->setF_inc($aDades['f_inc'],false);
        $oPersonaOut->setStgr($aDades['stgr']);
        //$oPersonaOut->setEdad($aDades['edad']);
        $oPersonaOut->setProfesion($aDades['profesion']);
        $oPersonaOut->setEap($aDades['eap']);
        $oPersonaOut->setObserv($aDades['observ']);
        $oPersonaOut->setLugar_nacimiento($aDades['lugar_nacimiento']);
        //$oPersonaOut->setProfesor_stgr($aDades['profesor_stgr']);

        // miro si es profesor
        $gesProfesores = new GestorProfesor();
        $gesProfesores->setoDbl($oDbl);
        $cProfesores = $gesProfesores->getProfesores(array('id_nom' => $this->id_nom, 'f_cese' => ''), array('f_cese' => 'IS NULL'));
        if (count($cProfesores) > 0) {
            $oPersonaOut->setProfesor_stgr('t');
        }
        // calculo la edad
        if (!empty($aDades['f_nacimiento'])) {
            $oF_nacimiento = new DateTimeLocal($aDades['f_nacimiento']);
            if (!empty($oF_nacimiento)) {
                $oF_nacimiento = new DateTimeLocal($aDades['f_nacimiento']);
                $m = (int)$oF_nacimiento->format('m');
                $a = (int)$oF_nacimiento->format('Y');
                $ah = (int)date("Y");
                $mh = (int)date("m");
                $inc_m = 0;
                $mh >= $m ? 0 : $inc_m = 1;
                $edad = $ah - $a - $inc_m;

                $oPersonaOut->setEdad($edad);
            }
        }

        $oPersonaOut->DBGuardar();

        return TRUE;
    }

    private function getSchemaFromId($id_schema_persona)
    {
        $sql = "SELECT * FROM public.db_idschema WHERE id = $id_schema_persona";
        $oDblSt = $this->ejecutar($sql);
        $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);

        return $aDades['schema'];
    }

}
