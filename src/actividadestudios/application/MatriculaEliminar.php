<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;

/**
 * Elimina una o varias matriculas y reajusta los dossiers 1303 / 3103 y
 * las asignaturas impartidas (`ActividadAsignatura`).
 *
 * Sustituye al case `eliminar` del antiguo `update_3103.php` dispatcher.
 *
 * Entrada esperada:
 * - `pau`: 'p' (persona) | 'a' (actividad).
 * - Si viene desde checkbox: `sel[]` con `id_activ#id_asignatura#id_nom` (pau=p)
 *   o `id_nom#id_asignatura#id_activ` (pau=a).
 * - Si viene desde formulario: `id_activ`, `id_nom` (o `id_pau` como fallback),
 *   `id_asignatura`.
 */
final class MatriculaEliminar
{
    public static function execute(array $input): string
    {
        $pau = (string) ($input['pau'] ?? '');
        $a_sel = (array) ($input['sel'] ?? []);
        $Qid_activ = (int) ($input['id_activ'] ?? 0);
        $Qid_nom = (int) ($input['id_nom'] ?? ($input['id_pau'] ?? 0));
        $Qid_asignatura = (int) ($input['id_asignatura'] ?? 0);

        $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
        $MatriculaDlRepository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);
        $DossierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);

        $msg_err = '';

        if ($pau === 'p') {
            foreach ($a_sel as $sel) {
                $id_activ = (int) strtok($sel, '#');
                $id_asignatura = (int) strtok('#');
                $id_nom = (int) strtok('#');
                if (!empty($Qid_activ)) {
                    $id_activ = $Qid_activ;
                }
                if (empty($id_nom) && !empty($Qid_nom)) {
                    $id_nom = $Qid_nom;
                }

                $oMatricula = $MatriculaDlRepository->findById($id_activ, $id_asignatura, $id_nom);
                if ($oMatricula === null) {
                    continue;
                }
                if ($MatriculaDlRepository->Eliminar($oMatricula) === false) {
                    $msg_err = _("hay un error, no se ha borrado");
                    continue;
                }

                // Cerrar dossier 1303 para esta persona si no tiene mas matriculas.
                self::cerrarDossier($DossierRepository, 'p', $id_nom, 1303);

                // Si es la unica asignatura de esta actividad y no queda nadie
                // matriculado, borrar la asignatura impartida.
                $cActividadAsignaturas = $ActividadAsignaturaDlRepository->getActividadAsignaturas(
                    ['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]
                );
                if (count($cActividadAsignaturas) === 1) {
                    $cMatriculas = $MatriculaDlRepository->getMatriculas(
                        ['id_activ' => $id_activ, 'id_asignatura' => $id_asignatura]
                    );
                    if (count($cMatriculas) === 0) {
                        $oActividadAsignatura = $cActividadAsignaturas[0];
                        $ActividadAsignaturaDlRepository->Eliminar($oActividadAsignatura);
                    }
                }
            }

            return $msg_err;
        }

        if ($pau === 'a') {
            // En legacy solo procesaba un `sel[0]` con el orden id_nom#id_asignatura#id_activ.
            if (!empty($a_sel)) {
                $id_nom = (int) strtok($a_sel[0], '#');
                $id_asignatura = (int) strtok('#');
                $id_activ = (int) strtok('#');
                if (empty($id_activ) && !empty($Qid_activ)) {
                    $id_activ = $Qid_activ;
                }
            } else {
                $id_activ = $Qid_activ;
                $id_nom = $Qid_nom;
                $id_asignatura = $Qid_asignatura;
            }

            $oMatricula = $MatriculaDlRepository->findById($id_activ, $id_asignatura, $id_nom);
            if ($oMatricula === null) {
                return _("no encuentro la matricula");
            }
            if ($MatriculaDlRepository->Eliminar($oMatricula) === false) {
                return _("hay un error, no se ha borrado");
            }
            self::cerrarDossier($DossierRepository, 'a', $id_activ, 3103);
            return '';
        }

        return $msg_err;
    }

    private static function cerrarDossier($DossierRepository, string $tabla, int $id_pau, int $id_tipo_dossier): void
    {
        if ($id_pau <= 0) {
            return;
        }
        $oDossier = $DossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => $tabla,
            'id_pau' => $id_pau,
            'id_tipo_dossier' => $id_tipo_dossier,
        ]));
        if ($oDossier === null) {
            return;
        }
        $oDossier->cerrar();
        $DossierRepository->Guardar($oDossier);
    }
}
