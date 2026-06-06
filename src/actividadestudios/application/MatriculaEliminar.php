<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Elimina una o varias matriculas y reajusta los dossiers 1303 / 3103 y
 * las asignaturas impartidas (`ActividadAsignatura`).
 *
 * Sustituye al case `eliminar` del antiguo `update_3103.php` dispatcher.
 */
final class MatriculaEliminar
{
    public function __construct(
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
        private MatriculaDlRepositoryInterface $matriculaDlRepository,
        private DossierRepositoryInterface $dossierRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $pau = input_string($input, 'pau');
        $a_sel = (array) ($input['sel'] ?? []);
        $Qid_activ = input_int($input, 'id_activ');
        $Qid_nom = input_int($input, 'id_nom');
        if ($Qid_nom <= 0) {
            $Qid_nom = input_int($input, 'id_pau');
        }
        $Qid_asignatura = input_int($input, 'id_asignatura');

        $msg_err = '';

        if ($pau === 'p') {
            foreach ($a_sel as $sel) {
                $id_activ = (int) strtok(self::selAsString($sel), '#');
                $id_asignatura = (int) strtok('#');
                $id_nom = (int) strtok('#');
                if ($Qid_activ > 0) {
                    $id_activ = $Qid_activ;
                }
                if ($id_nom <= 0 && $Qid_nom > 0) {
                    $id_nom = $Qid_nom;
                }

                $oMatricula = $this->matriculaDlRepository->findById($id_activ, $id_asignatura, $id_nom);
                if ($oMatricula === null) {
                    continue;
                }
                if ($this->matriculaDlRepository->Eliminar($oMatricula) === false) {
                    $msg_err = _("hay un error, no se ha borrado");
                    continue;
                }

                $this->cerrarDossier('p', $id_nom, 1303);

                $cActividadAsignaturas = $this->actividadAsignaturaDlRepository->getActividadAsignaturas(
                    ['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]
                );
                if (count($cActividadAsignaturas) === 1) {
                    $cMatriculas = $this->matriculaDlRepository->getMatriculas(
                        ['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]
                    );
                    if (count($cMatriculas) === 0) {
                        $oActividadAsignatura = $cActividadAsignaturas[0];
                        $this->actividadAsignaturaDlRepository->Eliminar($oActividadAsignatura);
                    }
                }
            }

            return $msg_err;
        }

        if ($pau === 'a') {
            if (!empty($a_sel)) {
                $id_nom = (int) strtok(self::selAsString($a_sel[0]), '#');
                $id_asignatura = (int) strtok('#');
                $id_activ = (int) strtok('#');
                if ($id_activ <= 0 && $Qid_activ > 0) {
                    $id_activ = $Qid_activ;
                }
            } else {
                $id_activ = $Qid_activ;
                $id_nom = $Qid_nom;
                $id_asignatura = $Qid_asignatura;
            }

            $oMatricula = $this->matriculaDlRepository->findById($id_activ, $id_asignatura, $id_nom);
            if ($oMatricula === null) {
                return _("no encuentro la matricula");
            }
            if ($this->matriculaDlRepository->Eliminar($oMatricula) === false) {
                return _("hay un error, no se ha borrado");
            }
            $this->cerrarDossier('a', $id_activ, 3103);
            return '';
        }

        return $msg_err;
    }

    private function cerrarDossier(string $tabla, int $id_pau, int $id_tipo_dossier): void
    {
        if ($id_pau <= 0) {
            return;
        }
        $oDossier = $this->dossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => $tabla,
            'id_pau' => $id_pau,
            'id_tipo_dossier' => $id_tipo_dossier,
        ]));
        if ($oDossier === null) {
            return;
        }
        $oDossier->cerrar();
        $this->dossierRepository->Guardar($oDossier);
    }

    private static function selAsString(mixed $sel): string
    {
        return is_scalar($sel) ? (string) $sel : '';
    }
}
