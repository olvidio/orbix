<?php

namespace src\profesores\domain;


use src\asistentes\domain\contracts\AsistentePubRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\profesores\domain\services\ProfesorStgrService;
use frontend\shared\web\Desplegable;
use function src\shared\domain\helpers\is_true;
use function src\shared\domain\helpers\usort_profesores_por_apellidos;

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
class ProfesorActividad
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
    public function getArrayProfesoresActividad(array $aId_activ = []): array
    {
        // Profesores de la Dl
        $ProfesorStgrService = $GLOBALS['container']->get(ProfesorStgrService::class);
        $aProfesoresDl = $ProfesorStgrService->getArrayProfesoresDl();
        // asistentes de otras dl que son profesores
        // asistentes de paso que son profesores
        $AsistentesPubRepository = $GLOBALS['container']->get(AsistentePubRepositoryInterface::class);
        $aProfesoresEx = [];
        $msg_err = '';
        foreach ($AsistentesPubRepository->getListaAsistentesDistintos($aId_activ) as $id_nom) {
            $oPersona = Persona::findPersonaEnGlobal($id_nom);
            if ($oPersona === null) {
                $msg_err .= "<br>No encuentro a nadie con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
                continue;
            }
            $obj_persona = get_class($oPersona);
            $obj_persona = str_replace("src\\personas\\domain\\entity\\", '', $obj_persona);
            if ($obj_persona === 'PersonaDl') {
                continue;
            }
            // solo puede ser PersonaEx o PersonaIN;
            $profesor_stgr = $oPersona->isProfesor_stgr();
            if (!is_true($profesor_stgr)) {
                continue;
            }

            $ap_nom = $oPersona->getPrefApellidosNombre();
            //$ctr_dl=$oPersona->getCentro_o_dl();

            $aProfesoresEx[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $ap_nom,
                'ap1' => $oPersona->getApellido1Vo()->value(),
                'ap2' => $oPersona->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersona->getNomVo()->value(),
            ];
        }
        usort_profesores_por_apellidos($aProfesoresEx);

        $aOpciones = [];
        foreach ($aProfesoresEx as $aClave) {
            $aOpciones[$aClave['id_nom']] = $aClave['ap_nom'];
        }

        $AllOpciones = $aOpciones + array("----------") + $aProfesoresDl;

        if (!empty($msg_err)) {
            echo $msg_err;
        }
        return $AllOpciones;
    }
}
