<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;

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
        $Qid_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
        $Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
        if ($Qid_nom <= 0) {
            $Qid_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        }
        $Qid_asignatura = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_asignatura');
        $Qid_nivel = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nivel');
        $Qid_situacion = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_situacion');
        $Qpreceptor = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'preceptor');
        $Qid_preceptor = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_preceptor');

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
        $oMatricula->setPreceptor(\src\shared\domain\helpers\FuncTablasSupport::isTrue($Qpreceptor));
        $oMatricula->setId_preceptor($Qid_preceptor);

        if ($this->matriculaDlRepository->Guardar($oMatricula) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }
}
