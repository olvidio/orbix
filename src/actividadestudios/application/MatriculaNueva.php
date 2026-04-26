<?php

namespace src\actividadestudios\application;

use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\entity\Matricula;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use function src\shared\domain\helpers\is_true;

/**
 * Crea una matricula (asignatura de una persona en una actividad) y
 * ajusta los dossiers 1303 (persona) y 3103 (actividad) + la asignatura
 * impartida (`ActividadAsignatura`).
 *
 * Sustituye al case `nuevo` de `update_3103.php`.
 */
final class MatriculaNueva
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

        if (empty($Qid_activ) || empty($Qid_nom)) {
            return _("falta id_activ o id_nom");
        }

        // Si no es opcional, calcula el id_asignatura a partir del id_nivel.
        if ($Qid_asignatura === 1) {
            $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
            $cAsignaturas = $AsignaturaRepository->getAsignaturas(['id_nivel' => $Qid_nivel]);
            if (empty($cAsignaturas)) {
                return _("no encuentro asignatura para ese nivel");
            }
            $Qid_asignatura = $cAsignaturas[0]->getId_asignatura();
        }

        $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
        $MatriculaDlRepository = $GLOBALS['container']->get(MatriculaDlRepositoryInterface::class);

        $oMatricula = $MatriculaDlRepository->findById($Qid_activ, $Qid_asignatura, $Qid_nom);
        if ($oMatricula === null) {
            $oMatricula = new Matricula();
            $oMatricula->setId_activ($Qid_activ);
            $oMatricula->setId_asignatura($Qid_asignatura);
            $oMatricula->setId_nom($Qid_nom);
        }
        $oMatricula->setId_nivel($Qid_nivel);
        $oMatricula->setId_situacion($Qid_situacion);
        $oMatricula->setPreceptor(empty($Qpreceptor) ? 'f' : 't');
        $oMatricula->setId_preceptor($Qid_preceptor);
        if ($MatriculaDlRepository->Guardar($oMatricula) === false) {
            return _("hay un error, no se ha guardado");
        }

        $DossierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        // Abrir dossier 1303 de la persona.
        $oDossier = $DossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1303,
        ]));
        if ($oDossier === null) {
            $oDossier = $DossierRepository->crearDossier(DossierPk::fromArray([
                'tabla' => 'p', 'id_pau' => $Qid_nom, 'id_tipo_dossier' => 1303,
            ]));
        }
        $oDossier->abrir();
        $DossierRepository->Guardar($oDossier);

        // Cerrar el dossier 3103 de la actividad si estaba abierto (al menos
        // hay ya una matricula, el legacy hacia cerrar — se mantiene por
        // compatibilidad con la semantica previa).
        $oDossierActiv = $DossierRepository->findByPk(DossierPk::fromArray([
            'tabla' => 'a', 'id_pau' => $Qid_activ, 'id_tipo_dossier' => 3103,
        ]));
        if ($oDossierActiv !== null) {
            $oDossierActiv->cerrar();
            $DossierRepository->Guardar($oDossierActiv);
        }

        // Añadir esta asignatura a las impartidas en el ca si no existe.
        $cActividadAsignaturas = $ActividadAsignaturaDlRepository->getActividadAsignaturas(
            ['id_activ' => $Qid_activ, 'id_asignatura' => $Qid_asignatura]
        );
        if (count($cActividadAsignaturas) === 0) {
            $oActividadAsignatura = new ActividadAsignatura();
            $oActividadAsignatura->setId_activ($Qid_activ);
            $oActividadAsignatura->setId_asignatura($Qid_asignatura);
            if (is_true($Qpreceptor)) {
                $oActividadAsignatura->setId_profesor($Qid_preceptor);
                $tipo = 'p';
            } else {
                $tipo = '';
            }
            $oActividadAsignatura->setTipo($tipo);
            $ActividadAsignaturaDlRepository->Guardar($oActividadAsignatura);
        }

        return '';
    }
}
