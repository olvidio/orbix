<?php

namespace src\asistentes\application;

use Psr\Container\ContainerInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\entity\TiposActividades;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\PosiblesCa;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Modal mover asistente (`asistente_mover.php`).
 *
 * URL de guardado, hash y desplegable HTML: {@see \frontend\asistentes\helpers\AsistenteMoverRender}.
 */
final class AsistenteMoverData
{
    public function __construct(
        private ContainerInterface $container,
        private AsistenteActividadService $asistenteActividadService,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private DelegacionRepositoryInterface $delegacionRepository,
        private ActividadPlazasRepositoryInterface $actividadPlazasRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private PlazaPeticionRepositoryInterface $plazaPeticionRepository,
        private ActividadAsignaturaDlRepositoryInterface $actividadAsignaturaDlRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function build(array $input): array
    {
        $a_sel = (array)($input['sel'] ?? []);
        if (!empty($a_sel)) {
            $Qid_nom = (int)strtok($a_sel[0], '#');
        } else {
            $Qid_nom = (int)($input['id_nom'] ?? 0);
        }

        $Qid_activ_old = (int)($input['id_activ'] ?? 0);
        $Qid_nom = (int)($input['id_pau'] ?? $Qid_nom);

        $AsistenteRepositoryInterface = $this->asistenteActividadService->getRepoAsistente($Qid_nom, $Qid_activ_old);
        /** @var AsistenteRepositoryInterface $AsistenteRepository */
        $AsistenteRepository = $this->container->get($AsistenteRepositoryInterface);
        $oAsistente = $AsistenteRepository->findById($Qid_activ_old, $Qid_nom);
        if ($oAsistente === null) {
            return [
                'aviso_txt' => sprintf(_('no se encuentra el asistente (id_nom: %s, id_activ: %s)'), $Qid_nom, $Qid_activ_old),
                'observ' => '',
                'paths' => [
                    'guardar' => 'src/asistentes/asistente_guardar',
                ],
            ];
        }

        if ($oAsistente->perm_modificar() === false) {
            return [
                'aviso_txt' => _('los datos de asistencia los modifica la dl del asistente'),
                'observ' => '',
                'paths' => [
                    'guardar' => 'src/asistentes/asistente_guardar',
                ],
            ];
        }

        $mi_dele = ConfigGlobal::mi_delef();
        $cDelegaciones = $this->delegacionRepository->getDelegaciones(['dl' => $mi_dele]);
        $oDelegacion = $cDelegaciones[0];
        $id_dl = $oDelegacion->getIdDlVo()->value();

        $oPosiblesCa = new PosiblesCa();
        $propietario = '';
        $mod = '';
        $propio = 't';
        $falta = 'f';
        $est_ok = 'f';
        $observ = '';
        $cActividades = [];
        if (!empty($Qid_activ_old) && !empty($Qid_nom)) {
            $mod = 'mover';

            $oActividad = $this->actividadAllRepository->findById($Qid_activ_old);
            if ($oActividad !== null) {
            $id_tipo = $oActividad->getId_tipo_activ();

            $dl = preg_replace('/f$/', '', $oActividad->getDl_org());
            $propietario = "$dl>$mi_dele";

            $oTipoActiv = new TiposActividades($id_tipo);
            $sactividad = $oTipoActiv->getActividadText();

            /** @var ConfigSnapshot $oConfig */
            $oConfig = $_SESSION['oConfig'];
            $oInicurs = null;
            $oFincurs = null;
            switch ($sactividad) {
                case 'ca':
                case 'cv':
                    $any = $oConfig->any_final_curs('est');
                    $oInicurs = \src\shared\domain\helpers\curso_est('inicio', $any, 'est');
                    $oFincurs = \src\shared\domain\helpers\curso_est('fin', $any, 'est');
                    break;
                case 'crt':
                    $any = $oConfig->any_final_curs('crt');
                    $oInicurs = \src\shared\domain\helpers\curso_est('inicio', $any, 'crt');
                    $oFincurs = \src\shared\domain\helpers\curso_est('fin', $any, 'crt');
                    break;
            }
            $inicurs_iso = $oInicurs ? $oInicurs->format('Y-m-d') : '';
            $fincurs_iso = $oFincurs ? $oFincurs->format('Y-m-d') : '';

            $aWhere = [];
            $aOperador = [];
            $aWhere['f_ini'] = "'$inicurs_iso','$fincurs_iso'";
            $aOperador['f_ini'] = 'BETWEEN';
            $aWhere['id_tipo_activ'] = '^' . $id_tipo;
            $aOperador['id_tipo_activ'] = '~';
            $aWhere['status'] = StatusId::ACTUAL;
            $aWhere['_ordre'] = 'f_ini';

            $cActividades = $this->actividadRepository->getActividades($aWhere, $aOperador);

            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $cActividadesPreferidas = [];
                $cPlazasPeticion = $this->plazaPeticionRepository->getPlazasPeticion(['id_nom' => $Qid_nom, 'tipo' => $sactividad, '_ordre' => 'orden']);
                foreach ($cPlazasPeticion as $oPlazaPeticion) {
                    $id_activ_pref = $oPlazaPeticion->getId_activ();
                    $oActividadPref = $this->actividadAllRepository->findById($id_activ_pref);
                    if ($oActividadPref === null) {
                        continue;
                    }
                    if ($oActividadPref->getStatus() !== StatusId::ACTUAL) {
                        continue;
                    }
                    if ($oInicurs !== null && $oActividadPref->getF_ini() < $oInicurs) {
                        continue;
                    }
                    $cActividadesPreferidas[] = $oActividadPref;
                }
                if (!empty($cActividadesPreferidas)) {
                    $cActividades = array_merge($cActividadesPreferidas, ['--------'], $cActividades);
                }
            }
            }
        }

        $aOpciones = [];
        foreach ($cActividades as $oActividadItem) {
            $id_activ = 0;
            $nom_activ = '--------------';
            $txt_plazas = '';
            $txt_creditos = '';
            if ($oActividadItem instanceof ActividadAll) {
                $id_activ = $oActividadItem->getId_activ();
                if ($id_activ === $Qid_activ_old) {
                    continue;
                }
                $nom_activ = $oActividadItem->getNom_activ();
                $dl_org = $oActividadItem->getDl_org();
                if (ConfigGlobal::is_app_installed('actividadplazas')) {
                    $concedidas = 0;
                    $cActividadPlazas = $this->actividadPlazasRepository->getActividadesPlazas(['id_dl' => $id_dl, 'id_activ' => $id_activ]);
                    foreach ($cActividadPlazas as $oActividadPlazas) {
                        if ($dl_org === $oActividadPlazas->getDl_tabla()) {
                            $concedidas = $oActividadPlazas->getPlazas();
                        }
                    }
                    $ocupadas = $this->asistenteActividadService->getPlazasOcupadasPorDl($id_activ, $mi_dele);
                    $libres = $ocupadas < 0 ? '-' : ($concedidas - $ocupadas);
                    if (!empty($concedidas)) {
                        $txt_plazas = sprintf(_('plazas libres/concedidas: %s/%s'), $libres, $concedidas);
                    }
                }
                $aAsignaturasCa = $this->actividadAsignaturaDlRepository->getAsignaturasCa($id_activ);
                $result = $oPosiblesCa->contar_creditos($Qid_nom, $aAsignaturasCa);
                $creditos = $result['suma'];
                if (!empty($creditos)) {
                    $txt_creditos = sprintf(_('créditos: %s'), $creditos);
                }
            }
            $aOpciones[$id_activ] = "$nom_activ $txt_plazas  $txt_creditos";
        }

        return [
            'aviso_txt' => '',
            'observ' => $observ,
            'paths' => [
                'guardar' => 'src/asistentes/asistente_guardar',
            ],
            'hash_main' => [
                'campos_no' => 'falta!est_ok',
                'campos_form' => 'observ!id_activ',
                'campos_hidden' => [
                    'id_nom' => $Qid_nom,
                    'id_activ_old' => $Qid_activ_old,
                    'mod' => $mod,
                    'propio' => $propio,
                    'plaza' => PlazaId::ASIGNADA,
                    'propietario' => $propietario,
                ],
            ],
            'opciones_actividades' => $aOpciones,
        ];
    }
}
