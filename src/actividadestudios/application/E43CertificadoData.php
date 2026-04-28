<?php

namespace src\actividadestudios\application;

use frontend\shared\config\OrbixRuntime;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\ubis\domain\entity\Ubi;

/**
 * Datos certificado E43 (pantalla e imprimible).
 *
 * @return array{
 *   msg_err: string,
 *   nom: string,
 *   txt_nacimiento: string,
 *   dl_origen: string,
 *   dl_destino: string,
 *   txt_actividad: string,
 *   matriculas: int,
 *   aAsignaturasMatriculadas: list<array{nom_asignatura: mixed, nota: string, f_acta: string, acta: string}>
 * }
 */
final class E43CertificadoData
{
    public static function execute(int $idNom, int $idActiv, bool $appendBlankFooter = false): array
    {
        $msgErr = '';
        $oPersona = Persona::findPersonaEnGlobal($idNom);
        if ($oPersona === null) {
            $msgErr .= "<br>No encuentro a nadie con id_nom: $idNom en  " . __FILE__ . ': line ' . __LINE__;
            return [
                'msg_err' => $msgErr,
                'nom' => '',
                'txt_nacimiento' => '',
                'dl_origen' => OrbixRuntime::miDelef(),
                'dl_destino' => '',
                'txt_actividad' => '',
                'matriculas' => 0,
                'aAsignaturasMatriculadas' => [],
            ];
        }

        $nom = $oPersona->getNombreApellidos();
        $lugarNacimiento = $oPersona->getLugar_nacimiento();
        $fNacimiento = $oPersona->getF_nacimiento()?->getFromLocal();
        $txtNacimiento = "$lugarNacimiento ($fNacimiento)";

        $dlOrigen = OrbixRuntime::miDelef();
        $dlDestino = $oPersona->getDl();

        $actividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $oActividad = $actividadAllRepository->findById($idActiv);
        $idUbi = $oActividad->getId_ubi();
        $fIni = $oActividad->getF_ini()?->getFromLocal();
        $fFin = $oActividad->getF_fin()?->getFromLocal();
        $oUbi = Ubi::NewUbi($idUbi);
        $lugar = $oUbi->getNombre_ubi();
        $txtActividad = "$lugar, $fIni-$fFin";

        $matriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);
        $cMatriculas = $matriculaRepository->getMatriculas(['id_nom' => $idNom, 'id_activ' => $idActiv]);
        $matriculas = count($cMatriculas);
        $aAsignaturasMatriculadas = [];
        if ($matriculas > 0) {
            $asignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
            $personaNotaRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
            foreach ($cMatriculas as $oMatricula) {
                $idAsignatura = $oMatricula->getId_asignatura();
                $oAsignatura = $asignaturaRepository->findById($idAsignatura);
                $nombreCorto = $oAsignatura->getNombre_corto();
                $cNotas = $personaNotaRepository->getPersonaNotas(['id_nom' => $idNom, 'id_asignatura' => $idAsignatura]);
                if ($cNotas !== false && count($cNotas) > 0) {
                    $oNota = $cNotas[0];
                    $nota = $oNota->getNota_txt();
                    $acta = $oNota->getActa();
                    $fActa = $oNota->getF_acta()?->getFromLocal();
                } else {
                    $nota = '';
                    $acta = '';
                    $fActa = '';
                }
                $aAsignaturasMatriculadas[] = [
                    'nom_asignatura' => $nombreCorto,
                    'nota' => $nota,
                    'f_acta' => $fActa,
                    'acta' => $acta,
                ];
            }
        } else {
            $msgErr .= _('no hay ninguna matrícula de esta persona');
        }

        if ($appendBlankFooter) {
            $aAsignaturasMatriculadas[] = [
                'nom_asignatura' => ' ',
                'nota' => '',
                'f_acta' => '',
                'acta' => '',
            ];
        }

        return [
            'msg_err' => $msgErr,
            'nom' => $nom,
            'txt_nacimiento' => $txtNacimiento,
            'dl_origen' => $dlOrigen,
            'dl_destino' => $dlDestino,
            'txt_actividad' => $txtActividad,
            'matriculas' => $matriculas,
            'aAsignaturasMatriculadas' => $aAsignaturasMatriculadas,
        ];
    }
}
