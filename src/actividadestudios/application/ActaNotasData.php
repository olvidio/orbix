<?php

namespace src\actividadestudios\application;

use frontend\shared\config\OrbixRuntime;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\notas\domain\contracts\ActaRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\entity\Persona;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use function frontend\shared\helpers\is_true;

/**
 * @return array{
 *   msg_err: string,
 *   permiso: int,
 *   nom_activ: string,
 *   matriculados: int,
 *   matriculas_rows: list<array{nom: string, id_nom: int, nota_num: mixed, nota_max: mixed, preceptor: bool, acta: mixed}>,
 *   notas: string,
 *   despl_actas_opciones: array<int|string, string>,
 *   acta_principal: string,
 *   acta_notas_a_actas: list<string>
 * }
 */
final class ActaNotasData
{
    public static function execute(int $idActiv, int $idAsignatura): array
    {
        $msgErr = '';
        $miDele = OrbixRuntime::miDelef();

        $actividadAsignaturaRepository = $GLOBALS['container']->get(ActividadAsignaturaRepositoryInterface::class);
        $cActivAsignaturas = $actividadAsignaturaRepository->getActividadAsignaturas([
            'id_activ' => $idActiv,
            'id_asignatura' => $idAsignatura,
        ]);
        $oActividadAsignatura = $cActivAsignaturas[0];
        $idSchema = $oActividadAsignatura->getId_schema();
        $dbSchemaRepository = $GLOBALS['container']->get(DbSchemaRepositoryInterface::class);
        $cDbSchemas = $dbSchemaRepository->getDbSchemas(['id' => $idSchema]);
        $aReg = explode('-', $cDbSchemas[0]->getSchema());
        $dlMatricula = substr($aReg[1], 0, -1);
        $permiso = ($miDele === $dlMatricula) ? 3 : 1;

        $actividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $actividadAllRepository->findById($idActiv);
        $nomActiv = $oActividad->getNom_activ();

        $matriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        $cMatriculados = $matriculaRepository->getMatriculas([
            'id_asignatura' => $idAsignatura,
            'id_activ' => $idActiv,
        ]);
        $matriculados = count($cMatriculados);
        $aPersonasMatriculadas = [];
        if ($matriculados > 0) {
            foreach ($cMatriculados as $oMatricula) {
                $idNom = $oMatricula->getId_nom();
                $oPersona = Persona::findPersonaEnGlobal($idNom);
                if ($oPersona === null) {
                    $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom";
                    continue;
                }
                $nom = $oPersona->getPrefApellidosNombre();
                $aPersonasMatriculadas[$nom] = $oMatricula;
            }
            uksort($aPersonasMatriculadas, 'src\shared\domain\helpers\strsinacentocmp');
        }

        $actaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
        $cActas = $actaRepository->getActas([
            'id_activ' => $idActiv,
            'id_asignatura' => $idAsignatura,
            '_ordre' => 'f_acta',
        ]);
        $actaPrincipal = '';
        $nomActa = '';
        $desplActasOpciones = [];
        $notas = 'nuevo';
        $aActasList = [];
        if (is_array($cActas) && !empty($cActas)) {
            $desplActasOpciones = [0 => '', NotaSituacion::CURSADA => Nota::getStatusTxt(NotaSituacion::CURSADA)];
            foreach ($cActas as $oActa) {
                $nomActa = $oActa->getActa();
                $desplActasOpciones[$nomActa] = $oActa->getActa();
                $aActasList[] = $nomActa;
            }
            $notas = 'acta';
            if (count($cActas) === 1) {
                $actaPrincipal = $nomActa;
            }
        } else {
            $desplActasOpciones = ['primero guardar acta'];
        }

        $matriculasRows = [];
        foreach ($aPersonasMatriculadas as $nom => $oMatricula) {
            $matriculasRows[] = [
                'nom' => $nom,
                'id_nom' => $oMatricula->getId_nom(),
                'nota_num' => $oMatricula->getNota_num(),
                'nota_max' => $oMatricula->getNota_max(),
                'preceptor' => is_true($oMatricula->isPreceptor()),
                'acta' => $oMatricula->getActa(),
            ];
        }

        return [
            'msg_err' => $msgErr,
            'permiso' => $permiso,
            'nom_activ' => $nomActiv,
            'matriculados' => $matriculados,
            'matriculas_rows' => $matriculasRows,
            'notas' => $notas,
            'despl_actas_opciones' => $desplActasOpciones,
            'acta_principal' => $actaPrincipal,
            'acta_notas_a_actas' => $aActasList,
        ];
    }
}
