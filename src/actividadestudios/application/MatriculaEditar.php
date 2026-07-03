<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

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
        $Qid_activ = FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_nom = FuncTablasSupport::inputInt($input, 'id_pau');
        if ($Qid_nom <= 0) {
            $Qid_nom = FuncTablasSupport::inputInt($input, 'id_nom');
        }
        $Qid_asignatura = FuncTablasSupport::inputInt($input, 'id_asignatura');
        $Qid_nivel = FuncTablasSupport::inputInt($input, 'id_nivel');
        $Qid_situacion = FuncTablasSupport::inputInt($input, 'id_situacion');
        $Qpreceptor = FuncTablasSupport::inputString($input, 'preceptor');
        $Qid_preceptor = FuncTablasSupport::inputInt($input, 'id_preceptor');

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
        $oMatricula->setPreceptor(FuncTablasSupport::isTrue($Qpreceptor));
        $oMatricula->setId_preceptor($Qid_preceptor);

        if ($this->matriculaDlRepository->Guardar($oMatricula) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
