<?php

namespace src\actividadcargos\application;

use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\actividades\domain\entity\TiposActividades;
use function src\shared\domain\helpers\is_true;

/**
 * Elimina un `ActividadCargo` y, si `elim_asis === 2` y el tipo de actividad
 * es `s`/`sg`, elimina tambien el `Asistente` y cierra los dossiers 1301/3101.
 *
 * Sustituye al case `eliminar` del antiguo `update_3102.php` dispatcher.
 */
final class ActividadCargoEliminar
{
    public static function execute(array $input): string
    {
        $id_item = (int) ($input['id_item'] ?? 0);
        $elim_asis = (int) ($input['elim_asis'] ?? 0);

        $a_sel = (array) ($input['sel'] ?? []);
        if ($id_item <= 0 && !empty($a_sel)) {
            // formato "id_nom#id_item#elim_asis#id_schema"
            $tok = $a_sel[0];
            strtok($tok, '#');
            $id_item = (int) strtok('#');
            $elim_asis = (int) strtok('#');
        }

        if ($id_item <= 0) {
            return _("falta id_item");
        }

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $oActividadCargo = $ActividadCargoRepository->findById($id_item);
        if ($oActividadCargo === null) {
            return _("no encuentro el cargo");
        }
        $id_activ = $oActividadCargo->getId_activ();
        $id_nom = $oActividadCargo->getId_nom();

        if ($ActividadCargoRepository->Eliminar($oActividadCargo) === false) {
            return _("hay un error, no se ha eliminado");
        }

        self::cerrarDossier('p', $id_nom, 1302);

        if ($elim_asis === 2 && $id_nom !== null && $id_activ > 0) {
            $error = self::eliminarAsistenteSiProcede($id_activ, $id_nom);
            if ($error !== '') {
                return $error;
            }
        }

        return '';
    }

    private static function eliminarAsistenteSiProcede(int $id_activ, int $id_nom): string
    {
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $oActividad = $ActividadRepository->findById($id_activ);
        if ($oActividad === null) {
            return '';
        }
        $oTipoActiv = new TiposActividades($oActividad->getId_tipo_activ());
        $sasistentes = $oTipoActiv->getAsistentesText();
        if ($sasistentes !== 's' && $sasistentes !== 'sg') {
            return '';
        }

        $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
        $AsistenteRepositoryInterface = $AsistenteActividadService->getRepoAsistente($id_nom, $id_activ);
        $AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
        $oAsistente = $AsistenteRepository->findById($id_activ, $id_nom);
        if ($oAsistente === null) {
            return '';
        }
        if (!is_true($oAsistente->perm_modificar())) {
            return '';
        }
        if ($AsistenteRepository->Eliminar($oAsistente) === false) {
            return _("hay un error, no se ha eliminado el asistente");
        }
        self::cerrarDossier('p', $id_nom, 1301);
        return '';
    }

    private static function cerrarDossier(string $tabla, ?int $id_pau, int $id_tipo_dossier): void
    {
        if ($id_pau === null || $id_pau <= 0) {
            return;
        }
        $DossierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
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
