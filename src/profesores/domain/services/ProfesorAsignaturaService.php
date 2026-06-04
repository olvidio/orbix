<?php

namespace src\profesores\domain\services;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;

use function src\shared\domain\helpers\usort_profesores_por_apellidos;

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
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            // Esto es un poco feo, PersonaDl es una entidad del modelo antiguo (fuera de src)
            // pero es lo que hay en el repositorio original.
            $oPersonaDl = $this->personaDlRepository->findById($id_nom);
            if ($oPersonaDl === null) {
                continue;
            }
            // comprobar situación
            $situacion = $oPersonaDl->getSituacionVo()->value();
            if ($situacion !== 'A') {
                continue;
            }
            $aProfesores[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $oPersonaDl->getPrefApellidosNombre(),
                'ap1' => $oPersonaDl->getApellido1Vo()->value(),
                'ap2' => $oPersonaDl->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersonaDl->getNomVo()->value(),
            ];
        }
        usort_profesores_por_apellidos($aProfesores);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $aOpciones[$aClave['id_nom']] = $aClave['ap_nom'];
        }

        return $aOpciones;
    }
}
