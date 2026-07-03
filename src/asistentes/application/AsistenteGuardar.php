<?php

namespace src\asistentes\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\services\AsistenteApplicationService;
use src\asistentes\domain\contracts\PlazaPropietarioAsignacionInterface;
use src\asistentes\domain\entity\Asistente;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\value_objects\DossierPk;

/**
 * Crea, edita o mueve un `Asistente`.
 *
 * Sustituye a los cases `nuevo`, `editar` y `mover` del antiguo
 * `apps/asistentes/controller/update_3101.php`:
 *
 * - `mod === 'nuevo'`: abre el dossier 1301 y guarda el asistente.
 * - `mod === 'editar'`: guarda el asistente existente (valida `perm_modificar`).
 * - `mod === 'mover'`: guarda en `id_activ` (validando plazas, etc.) y solo si
 *   tiene éxito elimina el asistente origen (`id_activ_old`).
 */
final class AsistenteGuardar
{
    public function __construct(
        private AsistenteApplicationService $asistenteApplicationService,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private DossierRepositoryInterface $dossierRepository,
        private AsistenteEliminar $asistenteEliminar,
        private PlazaPropietarioAsignacionInterface $plazaPropietarioAsignacion,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $mod = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'mod');
        if (!in_array($mod, ['nuevo', 'editar', 'mover'], true)) {
            return sprintf(_("mod no soportado: %s"), $mod);
        }

        $Qpau = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'pau');
        $a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($input, 'sel');
        $id_activ = 0;
        $id_nom = 0;
        if ($a_sel !== []) {
            $selKey = $a_sel[0];
            if ($Qpau === 'p') {
                $id_activ = (int) strtok($selKey, '#');
                $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
            } elseif ($Qpau === 'a') {
                $id_nom = (int) strtok($selKey, '#');
                $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_pau');
            }
        } else {
            $id_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ');
            $id_nom = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_nom');
        }
        $id_activ_old = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_activ_old');

        if ($id_activ === 0 || $id_nom === 0) {
            return _("faltan parametros id_activ / id_nom");
        }

        if ($mod === 'nuevo') {
            $this->abrirDossier1301($id_nom);
        }

        if ($mod === 'mover') {
            if ($id_activ_old === 0) {
                return _("falta id_activ_old");
            }
            $err = $this->guardar($id_activ, $id_nom, $mod, $input);
            if ($err !== '') {
                return $err;
            }

            return $this->asistenteEliminar->execute([
                'pau' => 'p',
                'sel' => [],
                'id_activ' => $id_activ_old,
                'id_nom' => $id_nom,
            ]);
        }

        return $this->guardar($id_activ, $id_nom, $mod, $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function guardar(int $id_activ, int $id_nom, string $mod, array $input): string
    {
        $asistenteAppService = $this->asistenteApplicationService;
        $oAsistente = $asistenteAppService->findById($id_activ, $id_nom);
        if ($oAsistente === null) {
            $oAsistente = new Asistente();
            $oAsistente->setId_activ($id_activ);
            $oAsistente->setId_nom($id_nom);
        }

        if ($mod === 'editar' && $oAsistente->perm_modificar() === false) {
            return _("los datos de asistencia los modifica la dl del asistente");
        }

        $oAsistente->setEncargo(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'encargo'));
        $oAsistente->setObserv(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ'));
        $oAsistente->setObservEstVo(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'observ_est'));
        $oAsistente->setPropio(\src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'propio')) ?? false);
        $oAsistente->setEst_ok(\src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'est_ok')) ?? false);
        $oAsistente->setCfi(\src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'cfi')) ?? false);
        $oAsistente->setFalta(\src\shared\domain\helpers\FuncTablasSupport::isTrue(\src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'falta')) ?? false);
        $oAsistente->setCfi_con(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'cfi_con'));

        if ($mod === 'mover') {
            $oAsistente->setPropio(true);
        }
        $oAsistente->setDlResponsableVo(ConfigGlobal::mi_delef());

        $Qpropietario = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'propietario');
        if ($Qpropietario === 'xxx') {
            $Qpropietario = '';
        }
        if ($mod === 'mover' && $Qpropietario === '' && ConfigGlobal::is_app_installed('actividadplazas')) {
            $Qpropietario = $this->resolverPropietarioMover($id_activ);
        }
        $oAsistente->setPropietarioVo($Qpropietario);
        $err_plaza = $oAsistente->setPlazaVoComprobando(
            \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'plaza'),
            $this->plazaPropietarioAsignacion,
        );
        if ($err_plaza !== '') {
            return $err_plaza;
        }

        if ($asistenteAppService->guardar($oAsistente) === false) {
            return _("hay un error, no se ha guardado");
        }
        return '';
    }

    private function resolverPropietarioMover(int $id_activ): string
    {
        $oActividad = $this->actividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            return '';
        }
        $dl = (string) preg_replace('/f$/', '', $oActividad->getDl_org() ?? '');
        $mi_dele = ConfigGlobal::mi_delef();
        if ($dl === '' || $mi_dele === '') {
            return '';
        }

        return "$dl>$mi_dele";
    }

    private function abrirDossier1301(int $id_nom): void
    {
        $DossierRepository = $this->dossierRepository;
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
