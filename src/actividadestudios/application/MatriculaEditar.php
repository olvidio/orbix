<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use function src\shared\domain\helpers\is_true;

/**
 * Edita una matricula existente (id_asignatura, id_nivel, id_situacion,
 * preceptor, id_preceptor).
 *
 * Sustituye al case `editar` de `update_3103.php`.
 */
final class MatriculaEditar
{
    public static function execute(array $input): string
    {
        $Qid_activ = (int) ($input['id_activ'] ?? 0);
        $Qid_nom = (int) ($input['id_pau'] ?? 0);
        if (empty($Qid_nom)) {
            $Qid_nom = (int) ($input['id_nom'] ?? 0);
        }
        $Qid_asignatura = (int) ($input['id_asignatura'] ?? 0);
        $Qid_nivel = (int) ($input['id_nivel'] ?? 0);
        $Qid_situacion = (int) ($input['id_situacion'] ?? 0);
        $Qpreceptor = (string) ($input['preceptor'] ?? '');
        $Qid_preceptor = (int) ($input['id_preceptor'] ?? 0);

        if (empty($Qid_activ) || empty($Qid_nom) || empty($Qid_asignatura)) {
            return _("faltan claves de la matricula");
        }

        $MatriculaDlRepository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
        $oMatricula = $MatriculaDlRepository->findById($Qid_activ, $Qid_asignatura, $Qid_nom);
        if ($oMatricula === null) {
            return _("no encuentro la matricula");
        }

        $oMatricula->setId_asignatura($Qid_asignatura);
        $oMatricula->setId_nivel($Qid_nivel);
        $oMatricula->setId_situacion($Qid_situacion);
        $oMatricula->setPreceptor(is_true($Qpreceptor));
        $oMatricula->setId_preceptor($Qid_preceptor);

        if ($MatriculaDlRepository->Guardar($oMatricula) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
