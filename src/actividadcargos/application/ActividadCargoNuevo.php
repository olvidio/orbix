<?php

namespace src\actividadcargos\application;

use core\ConfigGlobal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividadcargos\domain\entity\ActividadCargo;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use src\personas\domain\entity\Persona;
use function core\is_true;

/**
 * Alta de un `ActividadCargo`. Si llega `asis=true` ademas da de alta
 * al `Asistente` asociado (flujo tipico `nuevo` desde el form 3102/1302).
 *
 * Replica la logica del case `nuevo` del antiguo `update_3102.php`:
 * abrir dossiers `1302`/`3102` (cargos) y, cuando proceda, `1301`/`3101`
 * (asistentes).
 */
final class ActividadCargoNuevo
{
    public static function execute(array $input): string
    {
        $id_activ = (int) ($input['id_activ'] ?? 0);
        $id_nom = (int) ($input['id_nom'] ?? 0);
        $id_cargo = (int) ($input['id_cargo'] ?? 0);
        $observ = (string) ($input['observ'] ?? '');
        $puede_agd = (string) ($input['puede_agd'] ?? '');
        $asis = (string) ($input['asis'] ?? '');

        if ($id_activ <= 0 || $id_nom <= 0 || $id_cargo <= 0) {
            return _("faltan parametros id_activ / id_nom / id_cargo");
        }

        $ActividadCargoRepository = $GLOBALS['container']->get(ActividadCargoRepositoryInterface::class);
        $oActividadCargo = new ActividadCargo();
        $oActividadCargo->setId_item($ActividadCargoRepository->getNewId());
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
            return is_string($error) && $error !== '' ? $error : _("hay un error, no se ha guardado");
        }

        self::abrirDossier('p', $id_nom, 1302);
        self::abrirDossier('a', $id_activ, 3102);

        if (!empty($asis)) {
            $error = self::crearAsistente($id_activ, $id_nom);
            if ($error !== '') {
                return $error;
            }
            self::abrirDossier('p', $id_nom, 1301);
            self::abrirDossier('a', $id_activ, 3101);
        }

        return '';
    }

    private static function crearAsistente(int $id_activ, int $id_nom): string
    {
        $oPersona = Persona::findPersonaEnGlobal($id_nom);
        if (!is_object($oPersona)) {
            return sprintf(_("no encuentro a nadie con id_nom: %d"), $id_nom);
        }

        $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
        $AsistenteRepositoryInterface = $AsistenteActividadService->getRepoAsistente($id_nom, $id_activ);
        $AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
        $oAsistente = $AsistenteRepository->findById($id_activ, $id_nom);
        if ($oAsistente === null) {
            $oAsistente = new Asistente();
            $oAsistente->setId_activ($id_activ);
            $oAsistente->setId_nom($id_nom);
            $oAsistente->setEst_ok(false);
            $oAsistente->setCfi(false);
        }
        $oAsistente->setPropio('t');
        $oAsistente->setFalta('f');
        $oAsistente->setDl_responsable(ConfigGlobal::mi_delef());

        if ($AsistenteRepository->Guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado el asistente");
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
