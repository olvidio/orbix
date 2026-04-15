<?php

namespace src\profesores\application;

use core\ConfigGlobal;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

class ListaPorDepartamentos
{
    public static function getData(array $dl, int $filtro): array
    {
        $rstgr = ConfigGlobal::mi_ambito() === 'rstgr';
        if ($rstgr && $filtro !== 1) {
            $region_stgr = ConfigGlobal::mi_dele();
            $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
            $a_delegacionesStgr = $repoDelegacion->getArrayDlRegionStgr([$region_stgr]);

            return [
                'modo' => 'filtro',
                'rstgr' => true,
                'a_checked' => $dl,
                'a_delegaciones' => $a_delegacionesStgr,
            ];
        }

        $ProfesorTipoRepository = $GLOBALS['container']->get(ProfesorTipoRepositoryInterface::class);
        $cProfesorTipo = $ProfesorTipoRepository->getProfesorTipos();
        $cTipoProfesor = [];
        foreach ($cProfesorTipo as $oProfesorTipo) {
            $cTipoProfesor[$oProfesorTipo->getId_tipo_profesor()] = $oProfesorTipo->getTipo_profesor();
        }

        $DepartamentoRepository = $GLOBALS['container']->get(DepartamentoRepositoryInterface::class);
        $cDepartamentos = $DepartamentoRepository->getDepartamentos(['_ordre' => 'departamento']);
        $ProfesorDirectorRepository = $GLOBALS['container']->get(ProfesorDirectorRepositoryInterface::class);
        $ProfesorRepository = $GLOBALS['container']->get(ProfesorStgrRepositoryInterface::class);
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);

        $aClaustro = [];
        foreach ($cDepartamentos as $oDepartamento) {
            $id_departamento = $oDepartamento->getId_departamento();
            $departamento = $oDepartamento->getDepartamento();

            $aProfesores = [];
            $aDirs = self::getPersonasOrdenadas(
                $ProfesorDirectorRepository->getProfesoresDirectores(
                    self::buildWhere($id_departamento, $dl),
                    self::buildOperador($dl)
                ),
                $PersonaDlRepository
            );
            $aProfesores['director'] = $aDirs;

            foreach ($cTipoProfesor as $id_tipo => $tipo) {
                $aWhere = self::buildWhere($id_departamento, $dl);
                $aWhere['id_tipo_profesor'] = $id_tipo;
                $aProfesores[$tipo] = self::getPersonasOrdenadas(
                    $ProfesorRepository->getProfesoresStgr($aWhere, self::buildOperador($dl)),
                    $PersonaDlRepository
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

    private static function buildWhere(int $id_departamento, array $dl): array
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

    private static function buildOperador(array $dl): array
    {
        $aOperador = ['f_cese' => 'IS NULL'];
        if (!empty($dl)) {
            $aOperador['id_dl'] = 'IN';
        }
        return $aOperador;
    }

    private static function getPersonasOrdenadas(iterable $rows, PersonaDlRepositoryInterface $PersonaDlRepository): array
    {
        $aProfes = [];
        foreach ($rows as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $PersonaDlRepository->findById($id_nom);
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
