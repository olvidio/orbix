<?php

namespace src\actividades\application;

use src\shared\config\ConfigGlobal;
use src\dossiers\application\PermisoDossier;
use src\permisos\domain\PermisosActividadesTrue;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use frontend\shared\web\Lista;
use frontend\shared\web\Periodo;
use src\actividades\domain\entity\TiposActividades;

/**
 * Caso de uso para la pantalla `calendario_listas`.
 *
 * Aglutina los accesos a repositorios y servicios necesarios para componer el
 * calendario de actividades de casas o de oficinas, en un periodo dado, y
 * devuelve el HTML listo para inyectar en el DOM.
 *
 * Devuelve:
 *   - html (string)  Lista paginada con el calendario, precedida de las
 *                    advertencias de actividades sin lugar asignado.
 */
final class CalendarioListasDatos
{
    private const EQUIVALENCIAS_GM_OFICINA = [
        'n' => 'sm',
        'agd' => 'agd',
        's' => 'sg',
        'sg' => 'sg',
        'sss+' => 'des',
        'sr' => 'sr',
    ];

    public function ejecutar(array $input): array
    {
        $Qque = (string)($input['que'] ?? '');
        $Qver_ctr = (string)($input['ver_ctr'] ?? '');
        $Qperiodo = (string)($input['periodo'] ?? '');
        $Qyear = (string)($input['year'] ?? '');
        $Qyeardefault = (string)($input['yeardefault'] ?? '');
        $Qempiezamin = (string)($input['empiezamin'] ?? '');
        $Qempiezamax = (string)($input['empiezamax'] ?? '');
        $Qaid_cdc = (array)($input['id_cdc'] ?? []);

        $miSfsv = ConfigGlobal::mi_sfsv();
        $ver_ctr = empty($Qver_ctr) ? 'no' : $Qver_ctr;

        $aWhereCasa = [];
        $aOperadorCasa = [];
        $mi_of = '';
        $tipo = '';
        switch ($Qque) {
            case 'lista_cdc':
                $tipo = 'casa';
                if (!empty($Qaid_cdc)) {
                    $v = '{' . implode(', ', $Qaid_cdc) . '}';
                    $aWhereCasa['id_ubi'] = $v;
                    $aOperadorCasa['id_ubi'] = 'ANY';
                }
                break;
            case 'c_comunes':
            case 'c_comunes_sf':
            case 'c_comunes_sv':
                $tipo = 'casa';
                $aWhereCasa['tipo_ubi'] = 'cdcdl';
                $aWhereCasa['sv'] = 't';
                $aWhereCasa['sf'] = 't';
                break;
            case 'c_todas':
                $tipo = 'casa';
                $aWhereCasa['tipo_ubi'] = 'cdcdl';
                break;
            case 'c_todas_sf':
                $tipo = 'casa';
                $aWhereCasa['tipo_ubi'] = 'cdcdl';
                $aWhereCasa['sf'] = 't';
                break;
            case 'c_todas_sv':
                $tipo = 'casa';
                $aWhereCasa['tipo_ubi'] = 'cdcdl';
                $aWhereCasa['sv'] = 't';
                break;
            case 'o_actual':
                $tipo = 'oficina';
                $mi_of = ConfigGlobal::mi_oficina();
                break;
            case 'o_todas':
                $tipo = 'oficina';
                $mi_of = 'all';
                break;
            default:
                return [
                    'html' => sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__),
                ];
        }

        $Qyeardefault = empty($Qyeardefault) ? 'next' : $Qyeardefault;
        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny($Qyeardefault);
        $oPeriodo->setAny($Qyear);
        $oPeriodo->setEmpiezaMin($Qempiezamin);
        $oPeriodo->setEmpiezaMax($Qempiezamax);
        $oPeriodo->setPeriodo($Qperiodo);

        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();

        $aGrupos = [];
        $oTiposActividades = null;
        switch ($tipo) {
            case 'casa':
                $CasaDlRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
                $cCasas = $CasaDlRepository->getCasas($aWhereCasa, $aOperadorCasa);
                foreach ($cCasas as $oCasa) {
                    $aGrupos[$oCasa->getId_ubi()] = $oCasa->getNombre_ubi();
                }
                break;
            case 'oficina':
                $oTiposActividades = new TiposActividades();
                $oTiposActividades->setSfSvId($miSfsv);
                if ($mi_of === 'all') {
                    $aGrupos = $oTiposActividades->getAsistentesPosibles();
                } else {
                    $oPermisoOficinas = new PermisoDossier();
                    $aGrupos = $oTiposActividades->getAsistentesPosibles();
                    foreach ($aGrupos as $sasistentes) {
                        $oficina = self::EQUIVALENCIAS_GM_OFICINA[$sasistentes] ?? null;
                        if ($oficina !== null
                            && !$oPermisoOficinas->have_perm_oficina($oficina)
                            && ($key = array_search($sasistentes, $aGrupos)) !== false
                        ) {
                            unset($aGrupos[$key]);
                        }
                    }
                }
                break;
        }

        $warnings = '';
        $a_ubi_activ = [];
        $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $CentroEncargadoRepository = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
        $CasaRepository = $GLOBALS['container']->get(CasaRepositoryInterface::class);
        $CentroRepository = $GLOBALS['container']->get(CentroRepositoryInterface::class);
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $AsistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);

        foreach (array_keys($aGrupos) as $key) {
            $aWhere = [];
            $aOperador = [];
            $aWhere['f_ini'] = "'$inicioIso','$finIso'";
            $aOperador['f_ini'] = 'BETWEEN';
            $aWhere['status'] = 4;
            $aOperador['status'] = '<';

            $cActividades = [];
            switch ($tipo) {
                case 'casa':
                    $aWhere['id_ubi'] = $key;
                    $aWhere['_ordre'] = 'id_ubi,f_ini';
                    $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
                    break;
                case 'oficina':
                    $aWhere['_ordre'] = 'f_ini';
                    if ($mi_of === 'des') {
                        $aWhere['id_tipo_activ'] = '^16';
                        $aOperador['id_tipo_activ'] = '~';
                        $cActividadesSSSC = $ActividadRepository->getActividades($aWhere, $aOperador);
                        $aWhere['id_tipo_activ'] = '^1(124)|^1(.41)';
                        $aOperador['id_tipo_activ'] = '~';
                        $cActividadesOtros = $ActividadRepository->getActividades($aWhere, $aOperador);
                        $cActividades = array_merge($cActividadesOtros, $cActividadesSSSC);
                    } else {
                        $oTiposActividades->setAsistentesId($key);
                        $aWhere['id_tipo_activ'] = $oTiposActividades->getNom_tipoRegexp();
                        $aOperador['id_tipo_activ'] = '~';
                        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);
                    }
                    break;
            }

            if (!is_array($cActividades) || count($cActividades) === 0) {
                $a_ubi_activ[$key] = 1;
                continue;
            }

            $a = 0;
            foreach ($cActividades as $oActividad) {
                $a++;
                $id_activ = $oActividad->getId_activ();
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $nom_activ = $oActividad->getNom_activ();
                $dl_org = $oActividad->getDl_org();
                $f_ini = $oActividad->getF_ini()?->getFromLocal();
                $f_fin = $oActividad->getF_fin()?->getFromLocal();
                $h_ini = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $oActividad->getH_ini() ?? '');
                $h_fin = preg_replace('/(\d+):(\d+):(\d+)/', '$1:$2', $oActividad->getH_fin() ?? '');
                $tarifa = $oActividad->getTarifa();

                $id_ubi = $oActividad->getId_ubi();
                $lugar_esp = $oActividad->getLugar_esp();
                $nombre_ubi = '';
                if (empty($lugar_esp)) {
                    if (empty($id_ubi)) {
                        $warnings .= sprintf(_("La actividad: %s  No tiene lugar asignado"), $nom_activ) . '<br>';
                    } else {
                        $nombre_ubi = $CasaRepository->findById($id_ubi)?->getNombre_ubi();
                        if (empty($nombre_ubi)) {
                            $nombre_ubi = $CentroRepository->findById($id_ubi)?->getNombre_ubi();
                        }
                    }
                } else {
                    $nombre_ubi = $lugar_esp;
                }

                $oTipoActiv = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActiv->getSfsvText();
                $sasistentes = $oTipoActiv->getAsistentesText();
                $sactividad = $oTipoActiv->getActividadText();
                $snom_tipo = $oTipoActiv->getNom_tipoText();

                if (ConfigGlobal::$dmz) {
                    $num_asistentes = '?';
                } else {
                    $cAsistentes = $AsistenteActividadService->getAsistentesDeActividad($id_activ);
                    $num_asistentes = count($cAsistentes);
                }

                if (ConfigGlobal::is_app_installed('procesos')) {
                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
                    $oPermCtr = $_SESSION['oPermActividades']->getPermisoActual('ctr');
                } else {
                    $oPermActividades = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                    $oPermActiv = $oPermActividades->getPermisoActual('datos');
                    $oPermCtr = $oPermActividades->getPermisoActual('ctr');
                }

                if (!$oPermActiv->have_perm_action('ocupado')) {
                    continue;
                }

                if (!$oPermActiv->have_perm_action('ver')) {
                    $a_ubi_activ[$key][$a]['sfsv'] = $ssfsv;
                    $a_ubi_activ[$key][$a]['tipo_activ'] = _('ocupado');
                    if ($tipo === 'oficina') {
                        $a_ubi_activ[$key][$a]['cdc'] = "$nombre_ubi";
                    }
                    $a_ubi_activ[$key][$a]['fechas'] = "$f_ini - $f_fin";
                    $a_ubi_activ[$key][$a]['h_ini'] = $h_ini;
                    $a_ubi_activ[$key][$a]['h_fin'] = $h_fin;
                    $a_ubi_activ[$key][$a]['num_asistentes'] = '';
                    $a_ubi_activ[$key][$a]['id_tarifa'] = '';
                } else {
                    $a_ubi_activ[$key][$a]['sfsv'] = $ssfsv;
                    $a_ubi_activ[$key][$a]['tipo_activ'] = "$sasistentes $sactividad $snom_tipo";
                    if ($tipo === 'oficina') {
                        $a_ubi_activ[$key][$a]['cdc'] = "$nombre_ubi";
                    }
                    $a_ubi_activ[$key][$a]['fechas'] = "$f_ini - $f_fin";
                    $a_ubi_activ[$key][$a]['h_ini'] = $h_ini;
                    $a_ubi_activ[$key][$a]['h_fin'] = $h_fin;
                    $a_ubi_activ[$key][$a]['num_asistentes'] = $num_asistentes;
                    $oTipoTarifa = $TipoTarifaRepository->findById($tarifa);
                    $a_ubi_activ[$key][$a]['id_tarifa'] = $oTipoTarifa?->getLetra() ?? '';
                }

                $a_ubi_activ[$key][$a]['ctr_encargados'] = '';
                if ($ver_ctr === 'si' && $oPermCtr->have_perm_action('ver')) {
                    $cCtrsEncargados = $CentroEncargadoRepository->getCentrosEncargados([
                        'id_activ' => $id_activ,
                        '_ordre' => 'num_orden',
                    ]);
                    $txt_ctr = '';
                    foreach ($cCtrsEncargados as $oCentroEncargado) {
                        $id_ubi_ctr = $oCentroEncargado->getId_ubi();
                        $oCentroDl = $CentroDlRepository->findById($id_ubi_ctr);
                        $nombre_ctr = $oCentroDl?->getNombre_ubi() ?? '';
                        $txt_ctr .= empty($txt_ctr) ? $nombre_ctr : "; $nombre_ctr";
                    }
                    $a_ubi_activ[$key][$a]['ctr_encargados'] = $txt_ctr;
                }
            }
        }

        switch ($tipo) {
            case 'casa':
                $aCabeceras = [
                    _('sv/sf'),
                    _('tipo actividad'),
                    _('fechas'),
                    _('hora inicio'),
                    _('hora fin'),
                    _('asistentes previstos'),
                    _('id_tarifa'),
                    _('centros encargados'),
                ];
                break;
            case 'oficina':
                $aCabeceras = [
                    _('sv/sf'),
                    _('tipo actividad'),
                    _('cdc'),
                    _('fechas'),
                    _('hora inicio'),
                    _('hora fin'),
                    _('asistentes previstos'),
                    _('id_tarifa'),
                    _('centros encargados'),
                ];
                break;
            default:
                $aCabeceras = [];
        }

        $oTabla = new Lista();
        $oTabla->setGrupos($aGrupos);
        $oTabla->setCabeceras($aCabeceras);
        $oTabla->setDatos($a_ubi_activ);
        $html = $warnings . $oTabla->listaPaginada();

        return [
            'html' => $html,
        ];
    }
}
