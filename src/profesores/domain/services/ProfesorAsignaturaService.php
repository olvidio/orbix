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

    /**
     * @return array{departamento: array<int, string>, ampliacion: array<int, string>}
     */
    public function getArrayProfesoresAsignatura(AsignaturaId $id_asignatura): array
    {
        $oAsignatura = $this->asignaturaRepository->findById($id_asignatura->value());
        if ($oAsignatura === null) {
            throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura->value()));
        }
        $id_sector = $oAsignatura->getId_sector();
        $id_departamento = null;
        if ($id_sector !== null) {
            $id_departamento = $this->sectorRepository->findById($id_sector)?->getIdDepartamentoVo()?->value();
        }

        return [
            'departamento' => $this->getArrayProfesoresDepartamento($id_departamento),
            'ampliacion' => $this->profesorAmpliacionRepository->getArrayProfesoresAsignatura($id_asignatura->value()),
        ];
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayTodosProfesoresAsignatura(AsignaturaId $id_asignatura): array
    {
        $oAsignatura = $this->asignaturaRepository->findById($id_asignatura->value());
        if ($oAsignatura === null) {
            throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura->value()));
        }
        $id_sector = $oAsignatura->getId_sector();
        $id_departamento = null;
        if ($id_sector !== null) {
            $id_departamento = $this->sectorRepository->findById($id_sector)?->getIdDepartamentoVo()?->value();
        }

        return $this->getArrayProfesoresDepartamento($id_departamento)
            + ['----------']
            + $this->profesorAmpliacionRepository->getArrayProfesoresAsignatura($id_asignatura->value());
    }

    /**
     * @return array<int, string>
     */
    private function getArrayProfesoresDepartamento(?int $id_departamento): array
    {
        if ($id_departamento === null) {
            return [];
        }

        $gesProfesores = $this->profesorStgrRepository->getProfesoresStgr(
            ['id_departamento' => $id_departamento, 'f_cese' => ''],
            ['f_cese' => 'IS NULL']
        );
        $aProfesores = [];
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
            $aProfesores[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $oPersonaDl->getPrefApellidosNombre(),
                'ap1' => $oPersonaDl->getApellido1Vo()->value(),
                'ap2' => $oPersonaDl->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersonaDl->getNomVo()?->value() ?? '',
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
