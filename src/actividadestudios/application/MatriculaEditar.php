<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Edita una matricula existente (id_asignatura, id_nivel, id_situacion,
 * preceptor, id_preceptor).
 *
 * Sustituye al case `editar` de `update_3103.php`.
 */
final class MatriculaEditar
{
    public function __construct(
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $Qid_activ = input_int($input, 'id_activ');
        $Qid_nom = input_int($input, 'id_pau');
        if ($Qid_nom <= 0) {
            $Qid_nom = input_int($input, 'id_nom');
        }
        $Qid_asignatura = input_int($input, 'id_asignatura');
        $Qid_nivel = input_int($input, 'id_nivel');
        $Qid_situacion = input_int($input, 'id_situacion');
        $Qpreceptor = input_string($input, 'preceptor');
        $Qid_preceptor = input_int($input, 'id_preceptor');

        if ($Qid_activ <= 0 || $Qid_nom <= 0 || $Qid_asignatura <= 0) {
            return _("faltan claves de la matricula");
        }

        $oMatricula = $this->matriculaDlRepository->findById($Qid_activ, $Qid_asignatura, $Qid_nom);
        if ($oMatricula === null) {
            return _("no encuentro la matricula");
        }

        $oMatricula->setId_asignatura($Qid_asignatura);
        $oMatricula->setId_nivel($Qid_nivel);
        $oMatricula->setId_situacion($Qid_situacion);
        $oMatricula->setPreceptor(is_true($Qpreceptor));
        $oMatricula->setId_preceptor($Qid_preceptor);

        if ($this->matriculaDlRepository->Guardar($oMatricula) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
