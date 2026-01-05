<?php

namespace src\profesores\domain\services;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;

class ProfesorAsignaturaService
{
    public function __construct(
        private readonly AsignaturaRepositoryInterface $asignaturaRepository,
        private readonly SectorRepositoryInterface $sectorRepository,
        private readonly ProfesorStgrRepositoryInterface $profesorStgrRepository,
        private readonly ProfesorAmpliacionRepositoryInterface $profesorAmpliacionRepository,
        private readonly PersonaDlRepositoryInterface $personaDlRepository
    ) {
    }

    public function getArrayProfesoresAsignatura(AsignaturaId $id_asignatura): array
    {
        $oAsignatura = $this->asignaturaRepository->findById($id_asignatura->value());
        if ($oAsignatura === null) {
            throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura->value()));
        }
        $id_sector = $oAsignatura->getId_sector();
        $id_departamento = $this->sectorRepository->findById($id_sector)?->getIdDepartamentoVo()?->value();
        
        // Profesores departamento
        $aProfesoresDepartamento = $this->getArrayProfesoresDepartamento($id_departamento);
        // profesor ampliación
        $aProfesoresAmpliacion = $this->profesorAmpliacionRepository->getArrayProfesoresAsignatura($id_asignatura->value());

        $Opciones['departamento'] = $aProfesoresDepartamento;
        $Opciones['ampliacion'] = $aProfesoresAmpliacion;

        return $Opciones;
    }

    public function getArrayTodosProfesoresAsignatura(AsignaturaId $id_asignatura): array
    {
        $oAsignatura = $this->asignaturaRepository->findById($id_asignatura->value());
        if ($oAsignatura === null) {
            throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura->value()));
        }
        $id_sector = $oAsignatura->getId_sector();
        $id_departamento = $this->sectorRepository->findById($id_sector)?->getIdDepartamentoVo()?->value();
        
        // Profesores departamento
        $aProfesoresDepartamento = $this->getArrayProfesoresDepartamento($id_departamento);
        // profesor ampliación
        $aProfesoresAmpliacion = $this->profesorAmpliacionRepository->getArrayProfesoresAsignatura($id_asignatura->value());

        return $aProfesoresDepartamento + array("----------") + $aProfesoresAmpliacion;
    }

    private function getArrayProfesoresDepartamento($id_departamento): array
    {
        $gesProfesores = $this->profesorStgrRepository->getProfesoresStgr(array('id_departamento' => $id_departamento, 'f_cese' => ''), array('f_cese' => 'IS NULL'));
        $aProfesores = [];
        $aAp1 = [];
        $aAp2 = [];
        $aNom = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            // Esto es un poco feo, PersonaDl es una entidad del modelo antiguo (fuera de src)
            // pero es lo que hay en el repositorio original.
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
            $aProfesores[] = array('id_nom' => $id_nom, 'ap_nom' => $ap_nom);
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
            $val = $aClave['ap_nom'];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }
}
