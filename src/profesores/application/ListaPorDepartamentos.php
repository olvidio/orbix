<?php

namespace src\profesores\application;

use src\shared\config\ConfigGlobal;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\entity\ProfesorDirector;
use src\profesores\domain\entity\ProfesorStgr;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

final class ListaPorDepartamentos
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
        private ProfesorTipoRepositoryInterface $profesorTipoRepository,
        private DepartamentoRepositoryInterface $departamentoRepository,
        private ProfesorDirectorRepositoryInterface $profesorDirectorRepository,
        private ProfesorStgrRepositoryInterface $profesorStgrRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
    ) {
    }

    /**
     * @param list<string> $dl
     * @return array<string, mixed>
     */
    public function getData(array $dl, int $filtro): array
    {
        $rstgr = ConfigGlobal::mi_ambito() === 'rstgr';
        if ($rstgr && $filtro !== 1) {
            $region_stgr = ConfigGlobal::mi_dele();

            return [
                'modo' => 'filtro',
                'rstgr' => true,
                'a_checked' => $dl,
                'a_delegaciones' => $this->delegacionRepository->getArrayDlRegionStgr([$region_stgr]),
            ];
        }

        $cProfesorTipo = $this->profesorTipoRepository->getProfesorTipos();
        $cTipoProfesor = [];
        foreach ($cProfesorTipo as $oProfesorTipo) {
            $cTipoProfesor[$oProfesorTipo->getId_tipo_profesor()] = $oProfesorTipo->getTipo_profesor();
        }

        $cDepartamentos = $this->departamentoRepository->getDepartamentos(['_ordre' => 'departamento']);

        $aClaustro = [];
        foreach ($cDepartamentos as $oDepartamento) {
            $id_departamento = $oDepartamento->getId_departamento();
            $departamento = $oDepartamento->getDepartamento();

            $aProfesores = [];
            $aDirs = $this->getPersonasOrdenadas(
                $this->profesorDirectorRepository->getProfesoresDirectores(
                    $this->buildWhere($id_departamento, $dl),
                    $this->buildOperador($dl)
                ),
                $this->personaDlRepository
            );
            $aProfesores['director'] = $aDirs;

            foreach ($cTipoProfesor as $id_tipo => $tipo) {
                $aWhere = $this->buildWhere($id_departamento, $dl);
                $aWhere['id_tipo_profesor'] = $id_tipo;
                $aProfesores[$tipo] = $this->getPersonasOrdenadas(
                    $this->profesorStgrRepository->getProfesoresStgr($aWhere, $this->buildOperador($dl)),
                    $this->personaDlRepository
                );
            }

            $aClaustro[] = [
                'id_departamento' => $id_departamento,
                'departamento' => $departamento,
                'profesores' => $aProfesores,
            ];
        }

        return [
            'modo' => 'lista',
            'rstgr' => $rstgr,
            'aClaustro' => $aClaustro,
        ];
    }

    /**
     * @param list<string> $dl
     * @return array<string, mixed>
     */
    private function buildWhere(int $id_departamento, array $dl): array
    {
        $aWhere = [
            'id_departamento' => $id_departamento,
            'f_cese' => 'NULL',
        ];
        if (!empty($dl)) {
            $aWhere['id_dl'] = implode(',', $dl);
            $aWhere['_ordre'] = 'id_dl';
        }

        return $aWhere;
    }

    /**
     * @param list<string> $dl
     * @return array<string, string>
     */
    private function buildOperador(array $dl): array
    {
        $aOperador = ['f_cese' => 'IS NULL'];
        if (!empty($dl)) {
            $aOperador['id_dl'] = 'IN';
        }

        return $aOperador;
    }

    /**
     * @param list<ProfesorDirector|ProfesorStgr> $rows
     * @return array<string, array<string, string>>
     */
    private function getPersonasOrdenadas(array $rows, PersonaDlRepositoryInterface $personaDlRepository): array
    {
        $aProfes = [];
        foreach ($rows as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $personaDlRepository->findById($id_nom);
            if ($oPersonaDl === null || $oPersonaDl->getSituacion() !== 'A') {
                continue;
            }
            $dl = $oPersonaDl->getDl();
            $ap_orden = $dl . '*' . $oPersonaDl->getApellido1() . $oPersonaDl->getApellido2() . $oPersonaDl->getNom();
            $aProfes[$ap_orden][$dl] = $oPersonaDl->getPrefApellidosNombre() . ' (' . $oPersonaDl->getCentro_o_dl() . ')';
        }
        ksort($aProfes);

        return $aProfes;
    }
}
