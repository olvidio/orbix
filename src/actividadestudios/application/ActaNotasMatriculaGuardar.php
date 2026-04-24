<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\MatriculaRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;

/**
 * Guarda las notas de cada matricula (borrador del acta de notas). Se invoca
 * desde la pantalla `acta_notas` cuando el usuario pulsa "Grabar".
 *
 * Sustituye a la rama `que=1` del legacy
 * `apps/actividadestudios/controller/acta_notas_update.php`.
 *
 * Devuelve string vacio en exito o un mensaje de error. Respuesta JSON la
 * publica el controlador HTTP.
 */
final class ActaNotasMatriculaGuardar
{
    public static function execute(array $input): string
    {
        $Qid_asignatura = (int) ($input['id_asignatura'] ?? 0);
        $Qid_activ = (int) ($input['id_activ'] ?? 0);
        $nota_corte = $_SESSION['oConfig']->getNotaCorte();

        $Qform_preceptor = (array) ($input['form_preceptor'] ?? []);
        $Qid_nom = (array) ($input['id_nom'] ?? []);
        $Qnota_num = (array) ($input['nota_num'] ?? []);
        $Qnota_max = (array) ($input['nota_max'] ?? []);
        $Qacta = (array) ($input['acta_nota'] ?? []);

        $num_alumnos = count($Qid_nom);
        $MatriculaRepository = $GLOBALS['container']->get(MatriculaRepositoryInterface::class);

        for ($n = 0; $n < $num_alumnos; $n++) {
            $preceptor = (!empty($Qform_preceptor[$n]) && $Qform_preceptor[$n] === 'p');

            $oMatricula = $MatriculaRepository->findById($Qid_activ, $Qid_asignatura, $Qid_nom[$n]);
            $oMatricula->setPreceptor($preceptor);
            $nn = str_replace(',', '.', (string) $Qnota_num[$n]);
            if (!empty($Qnota_num[$n]) && (float) $Qnota_num[$n] / (float) $Qnota_max[$n] > 1) {
                return _('Hay una nota mayor que el máximo') . "\n";
            }
            $oMatricula->setNota_num($nn);
            $oMatricula->setNota_max($Qnota_max[$n]);
            $oMatricula->setActa($Qacta[$n]);

            if ($preceptor === false) {
                if ((int) $Qacta[$n] === 2) {
                    $oMatricula->setId_situacion(2);
                    if ((float) $Qnota_num[$n] > 1) {
                        $oMatricula->setId_situacion(12);
                    }
                } elseif ((float) $Qnota_num[$n] > 1) {
                    if ((float) $Qnota_num[$n] / (float) $Qnota_max[$n] < $nota_corte) {
                        $oMatricula->setId_situacion(12);
                    } else {
                        $oMatricula->setId_situacion(10);
                    }
                }
            } else {
                if ((int) $Qacta[$n] === NotaSituacion::CURSADA) {
                    return _('no se puede definir cursada con preceptor') . "\n";
                }
                if (empty($Qnota_num[$n])) {
                    $oMatricula->setId_situacion(0);
                } else {
                    $oMatricula->setId_situacion(10);
                }
            }
            if ($MatriculaRepository->Guardar($oMatricula) === false) {
                return _('hay un error, no se ha guardado') . "\n" . $oMatricula->getErrorTxt();
            }
        }
        return '';
    }
}
