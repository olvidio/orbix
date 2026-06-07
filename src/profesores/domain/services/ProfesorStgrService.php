<?php

namespace src\profesores\domain\services;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaPubRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;

use function src\shared\domain\helpers\usort_profesores_por_apellidos;

class ProfesorStgrService
{
    public function __construct(
        private ProfesorStgrRepositoryInterface $profesorStgrRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private PersonaPubRepositoryInterface $personaPubRepository,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function getArrayProfesoresPub(): array
    {
        $cPersonasPub = $this->personaPubRepository->getPersonas(['profesor_stgr' => 't']);

        $aProfesores = [];
        foreach ($cPersonasPub as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $situacion = $oPersona->getSituacionVo()->value();
            if ($situacion !== 'A') {
                continue;
            }
            $aProfesores[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $oPersona->getPrefApellidosNombre(),
                'ap1' => $oPersona->getApellido1Vo()->value(),
                'ap2' => $oPersona->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersona->getNomVo()?->value() ?? '',
            ];
        }
        usort_profesores_por_apellidos($aProfesores);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $aOpciones[$aClave['id_nom']] = $aClave['ap_nom'];
        }

        return $aOpciones;
    }

    /**
     * @return array<int, array{id_nom: int, ap_nom: string, dl: string}>
     */
    public function getArrayProfesoresConDl(): array
    {
        $gesProfesores = $this->profesorStgrRepository->getProfesoresStgr(['f_cese' => ''], ['f_cese' => 'IS NULL']);
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
            $situacion = $oPersonaDl->getSituacionVo()->value();
            if ($situacion !== 'A') {
                continue;
            }
            $dl = $oPersonaDl->getDlVo()?->value() ?? '';
            $ap_nom = $oPersonaDl->getPrefApellidosNombre();
            $aProfesores[] = ['id_nom' => $id_nom, 'ap_nom' => $ap_nom, 'dl' => $dl];
            $aAp1[] = $oPersonaDl->getApellido1Vo()->value();
            $aAp2[] = $oPersonaDl->getApellido2Vo()?->value() ?? '';
            $aNom[] = $oPersonaDl->getNomVo()?->value() ?? '';
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
        $multisort_args[] = &$aProfesores;
        call_user_func_array('array_multisort', $multisort_args);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $aOpciones[$aClave['id_nom']] = $aClave;
        }

        return $aOpciones;
    }

    /**
     * @return array<int, string>
     */
    public function getArrayProfesoresDl(): array
    {
        $gesProfesores = $this->profesorStgrRepository->getProfesoresStgr(['f_cese' => ''], ['f_cese' => 'IS NULL']);
        $aFilas = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $this->personaDlRepository->findById($id_nom);
            if ($oPersonaDl === null) {
                continue;
            }
            $situacion = $oPersonaDl->getSituacionVo()->value();
            if ($situacion !== 'A') {
                continue;
            }
            $aFilas[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $oPersonaDl->getPrefApellidosNombre(),
                'ap1' => $oPersonaDl->getApellido1Vo()->value(),
                'ap2' => $oPersonaDl->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersonaDl->getNomVo()?->value() ?? '',
            ];
        }
        usort_profesores_por_apellidos($aFilas);

        $aProfesores = [];
        foreach ($aFilas as $fila) {
            $aProfesores[$fila['id_nom']] = $fila['ap_nom'];
        }

        return $aProfesores;
    }
}
