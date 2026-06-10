<?php

namespace src\actividadestudios\application;

use src\actividades\domain\value_objects\NivelStgrId;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\notas\application\AsignaturasPendientes;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\value_objects\CursoStgr;
use src\personas\domain\entity\Persona;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * @return array{
 *   nom_activ: string,
 *   aAsignaturas_alumnos: list<array{nom_asignatura: string, id_asignatura: int, posibles_alumnos: int, aNombresAlumnos: list<string>}>,
 *   a_alumnos_fin_c: list<array{apellidos_nombre: string, asignaturas: mixed}>
 * }
 */
final class PosiblesAsignaturasCaData
{
    public function __construct(
        private AsistenteRepositoryInterface $asistenteRepository,
        private PersonaNotaRepositoryInterface $personaNotaRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{
     *   nom_activ: string,
     *   aAsignaturas_alumnos: list<array{nom_asignatura: string, id_asignatura: int, posibles_alumnos: int, aNombresAlumnos: list<string>}>,
     *   a_alumnos_fin_c: list<array{apellidos_nombre: string, asignaturas: mixed}>
     * }
     */
    public function execute(array $input): array
    {
        $idActiv = input_int($input, 'id_activ');
        $nomActivFromSel = input_string($input, 'nom_activ');

        $aTiposStgr = NivelStgrId::getArrayNivelStgrOn();
        $aAlumnosFinC = [];
        $aAlumnos = [];
        $pendientes = new AsignaturasPendientes($this->asignaturaRepository);
        foreach ($this->asistenteRepository->getAsistentes(['id_activ' => $idActiv]) as $oAsistente) {
            $idNom = $oAsistente->getId_nom();
            $oPersona = Persona::findPersonaEnGlobal($idNom);
            if ($oPersona === null) {
                continue;
            }
            $stgr = $oPersona->getNivel_stgr() ?? '';
            if ($stgr === '' || !array_key_exists($stgr, $aTiposStgr)) {
                continue;
            }
            $apNom = $oPersona->getPrefApellidosNombre();
            $aNomAsignaturasFaltan = $pendientes->asignaturasQueFaltanPersona($idNom, CursoStgr::CUADRIENIO);
            if (count($aNomAsignaturasFaltan) < 5) {
                $aAlumnosFinC[] = ['apellidos_nombre' => $apNom, 'asignaturas' => $aNomAsignaturasFaltan];
            }
            $aWhere = [];
            $aOperador = [];
            $aWhere['id_nom'] = $idNom;
            $aWhere['id_nivel'] = '1100,2500';
            $aOperador['id_nivel'] = 'BETWEEN';
            $cNotas = $this->personaNotaRepository->getPersonaNotas($aWhere, $aOperador);
            $aAprobadas = [];
            foreach ($cNotas as $oPersonaNota) {
                $idAsignatura = $oPersonaNota->getId_asignatura();
                $idSituacion = $oPersonaNota->getId_situacion();
                $aAprobadas[$idAsignatura] = $idSituacion;
            }
            $datos = ['id_nom' => $idNom, 'oPersona' => $oPersona, 'aprobadas' => $aAprobadas];
            $aAlumnos[] = ['apellidos_nombre' => $apNom, 'datos' => $datos];
        }
        sort($aAlumnos);

        $aWhereAsig = ['id_tipo' => 8, '_ordre' => 'id_nivel'];
        $aOperadorAsig = ['id_tipo' => '!='];
        $cAsignaturas = $this->asignaturaRepository->getAsignaturas($aWhereAsig, $aOperadorAsig);

        $aAsignaturasAlumnos = [];
        foreach ($cAsignaturas as $oAsignatura) {
            $nomAsignatura = $oAsignatura->getNombre_asignatura();
            $idAsignatura = $oAsignatura->getId_asignatura();
            $posiblesAlumnos = 0;
            $aNombresAlumnos = [];
            foreach ($aAlumnos as $aAlumno) {
                $datos = $aAlumno['datos'];
                $idNom = $datos['id_nom'];
                $aprobadas = $datos['aprobadas'];
                if (!array_key_exists($idAsignatura, $aprobadas)) {
                    $posiblesAlumnos++;
                    $aNombresAlumnos[] = $datos['oPersona']->getPrefApellidosNombre();
                }
            }
            if ($posiblesAlumnos === 0) {
                continue;
            }
            $aAsignaturasAlumnos[] = [
                'nom_asignatura' => $nomAsignatura,
                'id_asignatura' => $idAsignatura,
                'posibles_alumnos' => $posiblesAlumnos,
                'aNombresAlumnos' => $aNombresAlumnos,
            ];
        }

        return [
            'nom_activ' => $nomActivFromSel,
            'aAsignaturas_alumnos' => $aAsignaturasAlumnos,
            'a_alumnos_fin_c' => $aAlumnosFinC,
        ];
    }
}
