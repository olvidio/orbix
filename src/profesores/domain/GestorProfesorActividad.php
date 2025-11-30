<?php
namespace src\profesores\domain;


use asistentes\model\entity\GestorAsistenteIn;
use core\ClaseGestor;
use personas\model\entity\Persona;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use web\Desplegable;
use function core\is_true;

/**
 * GestorProfesor
 *
 * Classe per gestionar la llista d'objectes de la clase Profesor
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorProfesorActividad extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles professors de la dl
     * más los asistentes a actividades de otras dl o de paso que sean profesores
     *
     * @param array id_activ de las actividades seleccionadas (solo para profesores de paso).
     * @return array|Desplegable
     */
    function getListaProfesoresActividad($aId_activ = array())
    {

        // Profesores de la Dl
        $ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
        $aProfesoresDl = $ProfesorRepository->getListaProfesoresDl();
        // asistentes de otras dl que son profesores
        // asistentes de paso que son profesores
        $gesAsistentesIn = new GestorAsistenteIn();
        $aProfesoresEx = [];
        $aAp1 = [];
        $aAp2 = [];
        $aNom = [];
        $msg_err = '';
        foreach ($gesAsistentesIn->getListaAsistentesDistintos($aId_activ) as $id_nom) {
            $oPersona = Persona::NewPersona($id_nom);
            if (!is_object($oPersona)) {
                $msg_err .= "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                continue;
            }
            $obj_persona = get_class($oPersona);
            $obj_persona = str_replace("personas\\model\\entity\\", '', $obj_persona);
            if ($obj_persona === 'PersonaDl') { continue; }
            // solo puede ser PersonaEx o PersonaIN;
            $profesor_stgr = $oPersona->getProfesor_stgr();
            if (!is_true($profesor_stgr)) { continue; }

            $ap_nom = $oPersona->getPrefApellidosNombre();
            //$ctr_dl=$oPersona->getCentro_o_dl();

            $aProfesoresEx[] = array('id_nom' => $id_nom, 'ap_nom' => $ap_nom);
            $aAp1[] = $oPersona->getApellido1();
            $aAp2[] = $oPersona->getApellido2();
            $aNom[] = $oPersona->getNom();
        }
        $multisort_args = [];
        $multisort_args[] = $aAp1;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aAp2;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aNom;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = &$aProfesoresEx;   // finally add the source array, by reference
        call_user_func_array("array_multisort", $multisort_args);
        $aOpciones = [];
        foreach ($aProfesoresEx as $aClave) {
            $clave = $aClave['id_nom'];
            $val = $aClave['ap_nom'];
            $aOpciones[$clave] = $val;
        }

        $AllOpciones = $aOpciones + array("----------") + $aProfesoresDl;

        if (!empty($msg_err)) {
            echo $msg_err;
        }
        return new Desplegable('', $AllOpciones, '', true);
    }


    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
