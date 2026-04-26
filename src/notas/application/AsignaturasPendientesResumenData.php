<?php

namespace src\notas\application;

use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;

/**
 * Resumen: número de alumnos con cada asignatura pendiente, desglosado por
 * tramo (nb, nc1, nc2, n total, ab, ac1, ac2, a total). Sucesor de la lógica
 * embebida en `frontend/notas/controller/asignaturas_pendientes_resumen.php`.
 *
 * @return array{pendientes: array<int, array<string, mixed>>}
 */
final class AsignaturasPendientesResumenData
{
    public static function execute(): array
    {
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $aWhere = [];
        $aOperador = [];
        $aWhere['active'] = 't';
        $aWhere['id_nivel'] = '1100,2500';
        $aOperador['id_nivel'] = 'BETWEEN';
        $aWhere['_ordre'] = 'id_nivel';
        $cAsignaturas = $AsignaturaRepository->getAsignaturas($aWhere, $aOperador);

        $aPendientes = [];
        foreach ($cAsignaturas as $oAsignatura) {
            $id_nivel = $oAsignatura->getId_nivel();
            $nombre_corto = $oAsignatura->getNombre_corto();
            $creditos = $oAsignatura->getCreditos();
            $year = $oAsignatura->getYear();
            $aPendientes[$id_nivel] = [
                'def' => [
                    'nombre' => $nombre_corto,
                    'creditos' => $creditos,
                    'year' => $year,
                ],
                'nb' => 0,
                'nc1' => 0,
                'nc2' => 0,
                'ntotal' => 0,
                'ab' => 0,
                'ac1' => 0,
                'ac2' => 0,
                'atotal' => 0,
            ];
        }

        $a_Asig_isActive = [];
        $a_Asig_nivel = [];
        $cAsignaturasTodas = $AsignaturaRepository->getAsignaturas(['_ordre' => 'id_asignatura']);
        foreach ($cAsignaturasTodas as $oAsignatura) {
            $id_asignatura = $oAsignatura->getId_asignatura();
            $a_Asig_isActive[$id_asignatura] = $oAsignatura->isActive();
            $a_Asig_nivel[$id_asignatura] = $oAsignatura->getId_nivel();
        }

        $aWhere = [];
        $aOperador = [];
        $aWhere['situacion'] = 'A';
        $aWhere['nivel_stgr'] = NivelStgrId::B . ',' . NivelStgrId::C1 . ',' . NivelStgrId::C2;
        $aOperador['nivel_stgr'] = 'IN';
        $aWhere['id_tabla'] = '^[na]';
        $aOperador['id_tabla'] = '~';
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $cPersonas = $PersonaDlRepository->getPersonas($aWhere, $aOperador);

        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $arrayNotasSuperadas = NotaSituacion::getArraySuperadas();
        $a_NivelesStgr = [NivelStgrId::B => 'b', NivelStgrId::C1 => 'c1', NivelStgrId::C2 => 'c2'];

        foreach ($cPersonas as $oPersona) {
            $id_nom = $oPersona->getId_nom();
            $id_tabla = $oPersona->getId_tabla();
            $nivel_stgr = $oPersona->getNivel_stgr();

            $tipo = $id_tabla . $a_NivelesStgr[$nivel_stgr];

            $cNotas = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $id_nom]);
            $aAprobadas = [];
            foreach ($cNotas as $oPersonaNota) {
                $id_asignatura = $oPersonaNota->getId_asignatura();
                $id_nivel = $oPersonaNota->getIdNivelVo()->value();
                $id_situacion = $oPersonaNota->getId_situacion();

                if (($a_Asig_isActive[$id_asignatura] ?? null) !== true) {
                    continue;
                }

                if ($id_asignatura > 3000) {
                    $id_nivel_asig = $id_nivel;
                } else {
                    $id_nivel_asig = $a_Asig_nivel[$id_asignatura];
                }
                if (in_array($id_situacion, $arrayNotasSuperadas)) {
                    $aAprobadas[$id_nivel_asig] = 1;
                }
            }

            foreach ($cAsignaturas as $oAsignatura) {
                $id_nivel = $oAsignatura->getId_nivel();
                if (empty($aAprobadas[$id_nivel])) {
                    $aPendientes[$id_nivel][$tipo]++;
                }
            }
        }

        return ['pendientes' => $aPendientes];
    }
}
