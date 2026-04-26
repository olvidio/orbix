<?php

namespace src\asistentes\application;

use src\shared\config\ConfigGlobal;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;
use function src\shared\domain\helpers\is_true;

/**
 * Crea, edita o mueve un `Asistente`.
 *
 * Sustituye a los cases `nuevo`, `editar` y `mover` del antiguo
 * `apps/asistentes/controller/update_3101.php`:
 *
 * - `mod === 'nuevo'`: abre el dossier 1301 y guarda el asistente.
 * - `mod === 'editar'`: guarda el asistente existente (valida `perm_modificar`).
 * - `mod === 'mover'`: elimina el asistente origen (`id_activ_old`) y guarda
 *   el nuevo en `id_activ`.
 */
final class AsistenteGuardar
{
    public static function execute(array $input): string
    {
        $mod = (string) ($input['mod'] ?? '');
        if (!in_array($mod, ['nuevo', 'editar', 'mover'], true)) {
            return sprintf(_("mod no soportado: %s"), $mod);
        }

        $Qpau = (string) ($input['pau'] ?? '');
        $a_sel = (array) ($input['sel'] ?? []);
        $id_activ = 0;
        $id_nom = 0;
        if (!empty($a_sel)) {
            if ($Qpau === 'p') {
                $id_activ = (int) strtok($a_sel[0], '#');
                $id_nom = (int) ($input['id_pau'] ?? 0);
            } elseif ($Qpau === 'a') {
                $id_nom = (int) strtok($a_sel[0], '#');
                $id_activ = (int) ($input['id_pau'] ?? 0);
            }
        } else {
            $id_activ = (int) ($input['id_activ'] ?? 0);
            $id_nom = (int) ($input['id_nom'] ?? 0);
        }
        $id_activ_old = (int) ($input['id_activ_old'] ?? 0);

        if ($id_activ <= 0 || $id_nom <= 0) {
            return _("faltan parametros id_activ / id_nom");
        }

        if ($mod === 'nuevo') {
            self::abrirDossier1301($id_nom);
        }

        if ($mod === 'mover') {
            if ($id_activ_old <= 0) {
                return _("falta id_activ_old");
            }
            $err = AsistenteEliminar::execute([
                'pau' => 'p',
                'sel' => [],
                'id_activ' => $id_activ_old,
                'id_nom' => $id_nom,
            ]);
            if ($err !== '') {
                return $err;
            }
        }

        return self::guardar($id_activ, $id_nom, $mod, $input);
    }

    private static function guardar(int $id_activ, int $id_nom, string $mod, array $input): string
    {
        $asistenteAppService = $GLOBALS['container']->get(AsistenteApplicationService::class);
        $oAsistente = $asistenteAppService->findById($id_activ, $id_nom);
        if ($oAsistente === null) {
            $oAsistente = new Asistente();
            $oAsistente->setId_activ($id_activ);
            $oAsistente->setId_nom($id_nom);
        }

        if ($mod === 'editar' && $oAsistente->perm_modificar() === false) {
            return _("los datos de asistencia los modifica la dl del asistente");
        }

        $oAsistente->setEncargo((string) ($input['encargo'] ?? ''));
        $oAsistente->setObserv((string) ($input['observ'] ?? ''));
        $oAsistente->setObservEstVo((string) ($input['observ_est'] ?? ''));
        $oAsistente->setPlazaVoComprobando((int) ($input['plaza'] ?? 0));
        $oAsistente->setPropio(is_true((string) ($input['propio'] ?? '')));
        $oAsistente->setEst_ok(is_true((string) ($input['est_ok'] ?? '')));
        $oAsistente->setCfi(is_true((string) ($input['cfi'] ?? '')));
        $oAsistente->setFalta(is_true((string) ($input['falta'] ?? '')));
        $oAsistente->setCfi_con((int) ($input['cfi_con'] ?? 0));

        if ($mod === 'mover') {
            $oAsistente->setPropio('t');
        }
        $oAsistente->setDlResponsableVo(ConfigGlobal::mi_delef());

        $Qpropietario = (string) ($input['propietario'] ?? '');
        if ($Qpropietario === 'xxx') {
            $Qpropietario = '';
        }
        if ($Qpropietario !== '') {
            $oAsistente->setPropietarioVo($Qpropietario);
        }

        if ($asistenteAppService->guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }

    private static function abrirDossier1301(int $id_nom): void
    {
        $DossierRepository = $GLOBALS['container']->get(DossierRepositoryInterface::class);
        $pk = DossierPk::fromArray([
            'tabla' => 'p',
            'id_pau' => $id_nom,
            'id_tipo_dossier' => 1301,
        ]);
        $oDossier = $DossierRepository->findByPk($pk);
        if ($oDossier === null) {
            $oDossier = $DossierRepository->crearDossier($pk);
        }
        $oDossier->abrir();
        $DossierRepository->Guardar($oDossier);
    }
}
