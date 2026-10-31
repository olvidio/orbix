<?php

namespace src\profesores\application;

use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\profesores\domain\criteria\ProfesorDirectorField;
use src\profesores\domain\criteria\ProfesorStgrField;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\criteria\DepartamentoField;
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

        $cProfesorTipo = $this->profesorTipoRepository->matching(Criteria::create());
        $cTipoProfesor = [];
        foreach ($cProfesorTipo as $oProfesorTipo) {
            $cTipoProfesor[$oProfesorTipo->getId_tipo_profesor()] = $oProfesorTipo->getTipo_profesor();
        }

        $cDepartamentos = $this->departamentoRepository->matching(
            Criteria::create()->orderBy(DepartamentoField::NombreDepartamento)
        );

        $aClaustro = [];
        foreach ($cDepartamentos as $oDepartamento) {
            $id_departamento = $oDepartamento->getId_departamento();
            $departamento = $oDepartamento->getDepartamento();

            $aProfesores = [];
            // d_profesor_director no tiene columna id_dl (el filtro por dl daba error SQL): no se aplica.
            $aDirs = $this->getPersonasOrdenadas(
                $this->profesorDirectorRepository->matching(
                    Criteria::create()
                        ->where(Filter::eq(ProfesorDirectorField::IdDepartamento, $id_departamento))
                        ->andWhere(Filter::isNull(ProfesorDirectorField::FCese))
                ),
                $this->personaDlRepository
            );
            $aProfesores['director'] = $aDirs;

            foreach ($cTipoProfesor as $id_tipo => $tipo) {
                // d_profesor_stgr no tiene columna id_dl: el filtro por dl solo aplica a directores.
                $criteria = Criteria::create()
                    ->where(Filter::eq(ProfesorStgrField::IdDepartamento, $id_departamento))
                    ->andWhere(Filter::isNull(ProfesorStgrField::FCese))
                    ->andWhere(Filter::eq(ProfesorStgrField::IdTipoProfesor, $id_tipo));
                $aProfesores[$tipo] = $this->getPersonasOrdenadas(
                    $this->profesorStgrRepository->matching($criteria),
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
