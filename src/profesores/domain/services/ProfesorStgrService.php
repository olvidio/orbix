<?php

namespace src\profesores\domain\services;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;

class ProfesorStgrService
{
    private ProfesorStgrRepositoryInterface $profesorStgrRepository;
    private PersonaDlRepositoryInterface $personaDlRepository;

    public function __construct(
        ProfesorStgrRepositoryInterface $profesorStgrRepository,
        PersonaDlRepositoryInterface $personaDlRepository
    ) {
        $this->profesorStgrRepository = $profesorStgrRepository;
        $this->personaDlRepository = $personaDlRepository;
    }

    public function getArrayProfesoresPub(): array
    {
        $PersonaPubRepository = $GLOBALS['container']->get(PersonaPubRepositoryInterface::class);
        $cPersonasPub = $PersonaPubRepository->getPersonas(array('profesor_stgr' => 't'));

        $aProfesores = [];
        $aAp1 = [];
        $aAp2 = [];
        $aNom = [];
        foreach ($cPersonasPub as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            // comprobar situación
            $situacion = $oPersona->getSituacion();
            if ($situacion !== 'A') {
                continue;
            }
            $ap_nom = $oPersona->getPrefApellidosNombre();
            $aProfesores[] = array('id_nom' => $id_nom, 'ap_nom' => $ap_nom);
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
        $multisort_args[] = &$aProfesores;   // finally add the source array, by reference
        call_user_func_array("array_multisort", $multisort_args);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $clave = $aClave['id_nom'];
            $val = $aClave['ap_nom'];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    public function getArrayProfesoresConDl(): array
    {
        $gesProfesores = $this->profesorStgrRepository->getProfesoresStgr(array('f_cese' => ''), array('f_cese' => 'IS NULL'));
        $aProfesores = [];
        $aAp1 = [];
        $aAp2 = [];
        $aNom = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $this->personaDlRepository->findById($id_nom);
            if ($oPersonaDl === null) {
                continue;
            }
            // comprobar situación
            $situacion = $oPersonaDl->getSituacion();
            if ($situacion !== 'A') {
                continue;
            }
            $ap_nom = $oPersonaDl->getPrefApellidosNombre();
            $dl = $oPersonaDl->getDl();
            $aProfesores[] = array('id_nom' => $id_nom, 'ap_nom' => $ap_nom, 'dl' => $dl);
            $aAp1[] = $oPersonaDl->getApellido1();
            $aAp2[] = $oPersonaDl->getApellido2();
            $aNom[] = $oPersonaDl->getNom();
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
        $multisort_args[] = &$aProfesores;   // finally add the source array, by reference
        call_user_func_array("array_multisort", $multisort_args);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $clave = $aClave['id_nom'];
            //$val=$aClave['ap_nom'];
            //$dl=$aClave['dl'];
            $aOpciones[$clave] = $aClave;
        }
        return $aOpciones;
    }

    public function getArrayProfesoresDl(): array
    {
        $gesProfesores = $this->profesorStgrRepository->getProfesoresStgr(array('f_cese' => ''), array('f_cese' => 'IS NULL'));
        $aProfesores = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $this->personaDlRepository->findById($id_nom);
            if ($oPersonaDl === null) {
                continue;
            }
            // comprobar situación
            $situacion = $oPersonaDl?->getSituacion();
            if ($situacion !== 'A') {
                continue;
            }
            $ap_nom = $oPersonaDl->getPrefApellidosNombre();
            $aProfesores[$id_nom] = $ap_nom;
        }
        uasort($aProfesores, 'core\strsinacentocmp');

        return $aProfesores;
    }
}
