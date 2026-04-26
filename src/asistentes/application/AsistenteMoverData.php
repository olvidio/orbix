<?php

namespace src\asistentes\application;

use frontend\shared\web\Desplegable;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\actividades\domain\value_objects\StatusId;
use src\actividadestudios\domain\contracts\ActividadAsignaturaDlRepositoryInterface;
use src\actividadestudios\domain\PosiblesCa;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\services\AsistenteActividadService;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use web\Hash;

/**
 * Modal mover asistente (`asistente_mover.php`).
 */
final class AsistenteMoverData
{
    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function build(array $input): array
    {
        $a_sel = (array)($input['sel'] ?? []);
        if (!empty($a_sel)) {
            $Qid_nom = (int)strtok($a_sel[0], '#');
        } else {
            $Qid_nom = (int)($input['id_nom'] ?? 0);
        }

        $Qid_activ_old = (int)($input['id_activ'] ?? 0);
        $Qid_nom = (int)($input['id_pau'] ?? $Qid_nom);

        $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
        $AsistenteRepositoryInterface = $AsistenteActividadService->getRepoAsistente($Qid_nom, $Qid_activ_old);
        $AsistenteRepository = $GLOBALS['container']->get($AsistenteRepositoryInterface);
        $oAsistente = $AsistenteRepository->findById($Qid_activ_old, $Qid_nom);
        $web = rtrim(ConfigGlobal::getWeb(), '/');
        $url_guardar = $web . '/src/asistentes/asistente_guardar';

        if ($oAsistente->perm_modificar() === false) {
            return [
                'aviso_txt' => _('los datos de asistencia los modifica la dl del asistente'),
                'url_guardar' => $url_guardar,
                'hash_campos_html' => '',
                'desplegable_actividades_html' => '',
                'observ' => '',
            ];
        }

        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $ActividadPlazasRepository = $GLOBALS['container']->get(ActividadPlazasRepositoryInterface::class);
        $mi_dele = ConfigGlobal::mi_delef();
        $cDelegaciones = $repoDelegacion->getDelegaciones(['dl' => $mi_dele]);
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

            $oActividad = $ActividadAllRepository->findById($Qid_activ_old);
            $id_tipo = $oActividad->getId_tipo_activ();

            $dl = preg_replace('/f$/', '', $oActividad->getDl_org());
            $propietario = "$dl>$mi_dele";

            $oTipoActiv = new TiposActividades($id_tipo);
            $sactividad = $oTipoActiv->getActividadText();

            $oInicurs = null;
            $oFincurs = null;
            switch ($sactividad) {
                case 'ca':
                case 'cv':
                    $any = $_SESSION['oConfig']->any_final_curs('est');
                    $oInicurs = \src\shared\domain\helpers\curso_est('inicio', $any, 'est');
                    $oFincurs = \src\shared\domain\helpers\curso_est('fin', $any, 'est');
                    break;
                case 'crt':
                    $any = $_SESSION['oConfig']->any_final_curs('crt');
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

            $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
            $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);

            if (ConfigGlobal::is_app_installed('actividadplazas')) {
                $cActividadesPreferidas = [];
                $PlazaPeticionRepository = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
                $cPlazasPeticion = $PlazaPeticionRepository->getPlazasPeticion(['id_nom' => $Qid_nom, 'tipo' => $sactividad, '_ordre' => 'orden']);
                foreach ($cPlazasPeticion as $oPlazaPeticion) {
                    $id_activ_pref = $oPlazaPeticion->getId_activ();
                    $oActividadPref = $ActividadAllRepository->findById($id_activ_pref);
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

        $aOpciones = [];
        foreach ($cActividades as $oActividadItem) {
            $id_activ = 0;
            $nom_activ = '--------------';
            $txt_plazas = '';
            $txt_creditos = '';
            if (is_object($oActividadItem)) {
                $id_activ = $oActividadItem->getId_activ();
                if ($id_activ === $Qid_activ_old) {
                    continue;
                }
                $nom_activ = $oActividadItem->getNom_activ();
                $dl_org = $oActividadItem->getDl_org();
                if (ConfigGlobal::is_app_installed('actividadplazas')) {
                    $concedidas = 0;
                    $cActividadPlazas = $ActividadPlazasRepository->getActividadesPlazas(['id_dl' => $id_dl, 'id_activ' => $id_activ]);
                    foreach ($cActividadPlazas as $oActividadPlazas) {
                        if ($dl_org === $oActividadPlazas->getDl_tabla()) {
                            $concedidas = $oActividadPlazas->getPlazas();
                        }
                    }
                    $ocupadas = $AsistenteActividadService->getPlazasOcupadasPorDl($id_activ, $mi_dele);
                    $libres = $ocupadas < 0 ? '-' : ($concedidas - $ocupadas);
                    if (!empty($concedidas)) {
                        $txt_plazas = sprintf(_('plazas libres/concedidas: %s/%s'), $libres, $concedidas);
                    }
                }
                $ActividadAsignaturaDlRepository = $GLOBALS['container']->get(ActividadAsignaturaDlRepositoryInterface::class);
                $aAsignaturasCa = $ActividadAsignaturaDlRepository->getAsignaturasCa($id_activ);
                $result = $oPosiblesCa->contar_creditos($Qid_nom, $aAsignaturasCa);
                $creditos = $result['suma'];
                if (!empty($creditos)) {
                    $txt_creditos = sprintf(_('créditos: %s'), $creditos);
                }
            }
            $aOpciones[$id_activ] = "$nom_activ $txt_plazas  $txt_creditos";
        }

        $oDesplActividades = new Desplegable();
        $oDesplActividades->setNombre('id_activ');
        $oDesplActividades->setOpciones($aOpciones);

        $oHash = new Hash();
        $oHash->setCamposNo('falta!est_ok');
        $a_camposHidden = [
            'id_nom' => $Qid_nom,
            'id_activ_old' => $Qid_activ_old,
            'mod' => $mod,
            'propio' => $propio,
            'plaza' => PlazaId::ASIGNADA,
            'propietario' => $propietario,
        ];
        $oHash->setCamposForm('observ!id_activ');
        $oHash->setArraycamposHidden($a_camposHidden);

        return [
            'aviso_txt' => '',
            'url_guardar' => $url_guardar,
            'hash_campos_html' => $oHash->getCamposHtml(),
            'desplegable_actividades_html' => $oDesplActividades->desplegable(),
            'observ' => $observ,
        ];
    }
}
