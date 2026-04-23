<?php

namespace src\actividadcargos\application;

use core\ConfigGlobal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use function core\is_true;

/**
 * Edicion de un `ActividadCargo` existente (o alta si `id_item` viene vacio,
 * preservando la semantica del legacy `update_3102` caso `editar`).
 *
 * Si `asis` cambia respecto al estado actual del `Asistente`, lo crea o lo
 * elimina, y actualiza los dossiers 1301/3101 correspondientes.
 *
 * El parametro `asis_presente` debe valer `'1'` cuando el form incluye el
 * input `asis` (visible solo en `nuevo` sin id_nom_real). Sustituye el
 * antiguo `isset($_POST['asis'])` del dispatcher.
 */
final class ActividadCargoEditar
{
    public static function execute(array $input): string
    {
        $id_item = (int) ($input['id_item'] ?? 0);
        $id_activ = (int) ($input['id_activ'] ?? 0);
        $id_nom = (int) ($input['id_nom'] ?? 0);
        $id_cargo = (int) ($input['id_cargo'] ?? 0);
        $observ = (string) ($input['observ'] ?? '');
        $puede_agd = (string) ($input['puede_agd'] ?? '');
        $asis = (string) ($input['asis'] ?? '');
        $asis_presente = !empty($input['asis_presente']);

        if ($id_activ <= 0 || $id_nom <= 0 || $id_cargo <= 0) {
            return _("faltan parametros id_activ / id_nom / id_cargo");
        }

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        if ($id_item <= 0) {
            $oActividadCargo = new ActividadCargo();
            $oActividadCargo->setId_item($ActividadCargoRepository->getNewId());
        } else {
            $oActividadCargo = $ActividadCargoRepository->findById($id_item);
            if ($oActividadCargo === null) {
                return _("no encuentro el cargo");
            }
        }

        $oActividadCargo->setId_activ($id_activ);
        $oActividadCargo->setId_cargo($id_cargo);
        $oActividadCargo->setId_nom($id_nom);
        $oActividadCargo->setObserv($observ !== '' ? $observ : null);
        $oActividadCargo->setPuede_agd(is_true($puede_agd));

        if ($ActividadCargoRepository->Guardar($oActividadCargo) === false) {
            $error = end($_SESSION['errores']);
            if (is_string($error) && strpos($error, 'duplicate key') !== false) {
                return _("ya existe este cargo para esta actividad");
            }
            return _("hay un error, no se ha guardado");
        }

        return self::sincronizarAsistente($id_activ, $id_nom, $asis, $asis_presente);
    }

    private static function sincronizarAsistente(int $id_activ, int $id_nom, string $asis, bool $asis_presente): string
    {
        $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
        $AsistenteRepositoryInterface = $AsistenteActividadService->getRepoAsistente($id_nom, $id_activ);
        $AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
        $oAsistente = $AsistenteRepository->findById($id_activ, $id_nom);

        if ($oAsistente === null) {
            if (empty($asis)) {
                return '';
            }
            $oAsistente = new Asistente();
            $oAsistente->setId_activ($id_activ);
            $oAsistente->setId_nom($id_nom);
            $oAsistente->setPropio(true);
            $oAsistente->setFalta(false);
            $oAsistente->setDl_responsable(ConfigGlobal::mi_delef());
            if ($AsistenteRepository->Guardar($oAsistente) === false) {
                return _("hay un error, no se ha guardado el asistente");
            }
            self::abrirDossier('p', $id_nom, 1301);
            self::abrirDossier('a', $id_activ, 3101);
            return '';
        }

        if ($asis_presente && empty($asis)) {
            if ($AsistenteRepository->Eliminar($oAsistente) === false) {
                return _("hay un error, no se ha eliminado el asistente");
            }
            self::abrirDossier('p', $id_nom, 1301);
            self::abrirDossier('a', $id_activ, 3101);
        }

        return '';
    }

    private static function abrirDossier(string $tabla, int $id_pau, int $id_tipo_dossier): void
    {
        $DossierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $pk = DossierPk::fromArray([
            'tabla' => $tabla,
            'id_pau' => $id_pau,
            'id_tipo_dossier' => $id_tipo_dossier,
        ]);
        $oDossier = $DossierRepository->findByPk($pk);
        if ($oDossier === null) {
            $oDossier = $DossierRepository->crearDossier($pk);
        }
        $oDossier->abrir();
        $DossierRepository->Guardar($oDossier);
    }
}
