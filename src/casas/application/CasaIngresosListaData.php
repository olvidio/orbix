<?php

namespace src\casas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\usuarios\domain\value_objects\PauType;
use web\Periodo;
use web\TiposActividades;

/**
 * Data builder: listado económico de actividades por casa (pantalla
 * `casa_que` con `que=get`).
 *
 * Devuelve las cabeceras y filas listas para renderizar con
 * `web\Lista`. El filtrado por permisos de actividad se sigue haciendo
 * aquí porque depende del usuario de la sesión.
 */
final class CasaIngresosListaData
{
    public static function execute(array $input): array
    {
        $periodo = (string)($input['periodo'] ?? '');
        $year = (string)($input['year'] ?? '');
        $empiezamin = (string)($input['empiezamin'] ?? '');
        $empiezamax = (string)($input['empiezamax'] ?? '');
        /** @var array $ids_ubi */
        $ids_ubi = (array)($input['id_cdc'] ?? []);

        $aCabeceras = [
            _("inicio"),
            _("fin"),
            _("tipo de actividad"),
            _("precio"),
            _("asistentes previstos"),
            _("asistentes reales"),
            _("dif. asistencias"),
            _("ingresos previstos"),
            _("ingresos reales"),
            _("ing. previstos acumulados"),
            _("ing. reales acumulados"),
            _("observaciones"),
        ];

        $aGrupos = [];
        $CasaDl = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        foreach ($ids_ubi as $id_ubi) {
            $id_ubi = (int)$id_ubi;
            if ($id_ubi === 0) { continue; }
            $oCasa = $CasaDl->findById($id_ubi);
            if ($oCasa === null) { continue; }
            $aGrupos[$id_ubi] = $oCasa->getNombreUbiVo()?->value() ?? '';
        }
        if ($aGrupos === []) {
            return [
                'ok' => false,
                'error' => (string)_("Debe seleccionar una casa."),
                'a_cabeceras' => $aCabeceras,
                'a_valores' => [],
                'a_grupos' => [],
                'nota' => '',
                'errores' => '',
            ];
        }

        $oPeriodo = new Periodo();
        $oPeriodo->setDefaultAny('next');
        $oPeriodo->setAny($year);
        $oPeriodo->setEmpiezaMin($empiezamin);
        $oPeriodo->setEmpiezaMax($empiezamax);
        $oPeriodo->setPeriodo($periodo);
        $inicioIso = $oPeriodo->getF_ini_iso();
        $finIso = $oPeriodo->getF_fin_iso();

        $aWhere = [];
        $aOperador = [];
        if ($periodo === 'desdeHoy') {
            $aWhere['f_fin'] = "'$inicioIso','$finIso'";
            $aOperador['f_fin'] = 'BETWEEN';
        } else {
            $aWhere['f_ini'] = "'$inicioIso','$finIso'";
            $aOperador['f_ini'] = 'BETWEEN';
        }

        $miRolePau = ConfigGlobal::mi_role_pau();
        $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $IngresoRepository = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
        $TarifaUbiRepository = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);

        $a_valores = [];
        $txt_err = '';
        foreach ($aGrupos as $id_ubi => $titulo) {
            $id_ubi = (int)$id_ubi;
            $aWhere['id_ubi'] = $id_ubi;
            $aWhere['status'] = StatusId::BORRABLE;
            $aOperador['status'] = '<';
            $aWhere['_ordre'] = 'f_ini';
            $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador);

            $a = 0;
            $i_previstos_acumulados = 0.0;
            $i_acumulados = 0.0;
            $tot_asis_pr = [1 => 0, 2 => 0, 'tot' => 0];
            $tot_asis = [1 => 0, 2 => 0, 'tot' => 0];
            $tot_ing_pr = [1 => 0.0, 2 => 0.0, 'tot' => 0.0];
            $tot_ing = [1 => 0.0, 2 => 0.0, 'tot' => 0.0];
            $tot_ing_acu = [1 => 0.0, 2 => 0.0];

            if (is_array($cActividades)) {
                foreach ($cActividades as $oActividad) {
                    $id_activ = (int)$oActividad->getId_activ();
                    $id_tipo_activ = (string)$oActividad->getId_tipo_activ();
                    $dl_org = $oActividad->getDl_org();
                    $id_tarifa = (string)$oActividad->getTarifa();
                    $precio = $oActividad->getPrecio();
                    $oF_ini = $oActividad->getF_ini();
                    $oF_fin = $oActividad->getF_fin();
                    if ($oF_ini === null || $oF_fin === null) { continue; }

                    $num_dias_act = (float)$oActividad->getDuracionAumentada();
                    $num_dias = (float)$oActividad->getDuracionEnPeriodo($oF_ini, $oF_fin);
                    $num_dias_real = (float)$oActividad->getDuracionReal();
                    $factor_dias = $num_dias_real > 0 ? ($num_dias / $num_dias_real) : 0.0;
                    $factor = $num_dias_real > 0 ? ($num_dias_act - $num_dias_real) / $num_dias_real : 0.0;
                    $num_dias_ajust = round($num_dias * (1 + $factor), 1);

                    $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
                    $oPermEco = $_SESSION['oPermActividades']->getPermisoActual('economic');
                    if (!$oPermEco->have_perm_action('ver')) { continue; }
                    $permiso_modificar = $oPermEco->have_perm_action('modificar');

                    $oTipoTarifa = $TipoTarifaRepository->findById($id_tarifa);
                    $modo = $oTipoTarifa?->getModo() ?? 0;

                    $cTarifasUbi = $TarifaUbiRepository->getTarifaUbis([
                        'id_tarifa' => $id_tarifa,
                        'id_ubi' => $id_ubi,
                        'year' => $year,
                    ]);
                    $cantidad = 0.0;
                    if (is_array($cTarifasUbi) && isset($cTarifasUbi[0])) {
                        $cantidad = (float)$cTarifasUbi[0]->getCantidad();
                    }
                    if (empty($precio)) {
                        $flag = ($factor_dias != 1) ? '*' : '';
                        if ($modo === 1) {
                            $precio_txt = round($factor_dias * $cantidad, 2) . $flag;
                            $precio_pr = round($factor_dias * $cantidad, 2);
                        } else {
                            $precio_txt = sprintf(_('%s %s días x %s ~= %s'), $flag, $num_dias_ajust, $cantidad, ($num_dias_ajust * $cantidad));
                            $precio_pr = round($num_dias_ajust * $cantidad, 2);
                        }
                    } else {
                        $precio_txt = $precio;
                        $precio_pr = round($factor_dias * (float)$precio, 2);
                    }

                    $oTipoActiv = new TiposActividades($id_tipo_activ);
                    $nom_activ = $oTipoActiv->getNomGral();
                    if ($permiso_modificar) {
                        $cell_nom = ['script' => "fnjs_modificar($id_activ)", 'valor' => $nom_activ];
                    } else {
                        $cell_nom = $nom_activ;
                    }

                    $oIngreso = $IngresoRepository->findById($id_activ);
                    $num_asistentes_previstos = $oIngreso?->getNumAsistentesPrevistosVo()?->value() ?? 0;
                    if (empty($num_asistentes_previstos)) {
                        $txt_err .= ($txt_err === '' ? '' : '<br>') . sprintf(_("No está definido el núm. de asistente previstos para: %s"), $nom_activ);
                        $num_asistentes_previstos = 0;
                    }
                    $num_asistentes = $oIngreso?->getNumAsistentesVo()?->value() ?? 0;
                    if (empty($num_asistentes)) {
                        $txt_err .= ($txt_err === '' ? '' : '<br>') . sprintf(_("No está definido el núm. de asistente para: %s"), $nom_activ);
                        $num_asistentes = 0;
                    }
                    $ingresos_reales = $oIngreso?->getIngresosVo()?->value() ?? 0.0;
                    if (empty($ingresos_reales)) {
                        $txt_err .= ($txt_err === '' ? '' : '<br>') . sprintf(_("No se han introducido los ingresos para: %s"), $nom_activ);
                        $ingresos_reales = 0.0;
                    }
                    $ingresos_previstos = $num_asistentes_previstos * $precio_pr;
                    $ingresos = round($factor_dias * (float)$ingresos_reales, 2);
                    $observ = $oIngreso?->getObservVo()?->value() ?? '';

                    $i_previstos_acumulados += $ingresos_previstos;
                    $i_acumulados += $ingresos;

                    $sfsv = (int)substr($id_tipo_activ, 0, 1);
                    if (isset($tot_asis_pr[$sfsv])) {
                        $tot_asis_pr[$sfsv] += $num_asistentes_previstos;
                        $tot_asis[$sfsv] += $num_asistentes;
                        $tot_ing_pr[$sfsv] += $ingresos_previstos;
                        $tot_ing[$sfsv] += $ingresos;
                        $tot_ing_acu[$sfsv] += $ingresos;
                    }
                    $tot_asis_pr['tot'] += $num_asistentes_previstos;
                    $tot_asis['tot'] += $num_asistentes;
                    $tot_ing_pr['tot'] += $ingresos_previstos;
                    $tot_ing['tot'] += $ingresos;

                    $a_valores[$id_ubi][$a] = [
                        1 => $oF_ini->getFromLocal(),
                        2 => $oF_fin->getFromLocal(),
                        3 => $cell_nom,
                        4 => $precio_txt,
                        5 => $num_asistentes_previstos,
                        6 => $num_asistentes,
                        7 => $num_asistentes - $num_asistentes_previstos,
                        8 => $ingresos_previstos,
                        9 => $ingresos,
                        10 => $i_previstos_acumulados,
                        11 => $i_acumulados,
                        12 => $observ,
                        'clase' => 'derecha',
                    ];
                    $a++;
                }
            }

            $oF_ini_periodo = $oPeriodo->getF_ini();
            $oF_fin_periodo = $oPeriodo->getF_fin();
            $a_valores[$id_ubi][$a] = [
                1 => $oF_ini_periodo?->getFromLocal() ?? '',
                2 => $oF_fin_periodo?->getFromLocal() ?? '',
                3 => _('totales sv'),
                4 => '',
                5 => $tot_asis_pr[1],
                6 => $tot_asis[1],
                7 => empty($tot_asis['tot']) ? '-' : round($tot_asis[1] / $tot_asis['tot'] * 100, 2) . '%',
                8 => $tot_ing_pr[1],
                9 => $tot_ing[1],
                10 => empty($tot_ing['tot']) ? '-' : round($tot_ing[1] / $tot_ing['tot'] * 100, 2) . '%',
                11 => $tot_ing_acu[1],
                12 => '',
                'clase' => 'derecha',
            ];
            $a_valores[$id_ubi][$a + 1] = [
                1 => $oF_ini_periodo?->getFromLocal() ?? '',
                2 => $oF_fin_periodo?->getFromLocal() ?? '',
                3 => _('totales sf'),
                4 => '',
                5 => $tot_asis_pr[2],
                6 => $tot_asis[2],
                7 => empty($tot_asis['tot']) ? '-' : round($tot_asis[2] / $tot_asis['tot'] * 100, 2) . '%',
                8 => $tot_ing_pr[2],
                9 => $tot_ing[2],
                10 => empty($tot_ing['tot']) ? '-' : round($tot_ing[2] / $tot_ing['tot'] * 100, 2) . '%',
                11 => $tot_ing_acu[2],
                12 => '',
                'clase' => 'derecha',
            ];
            $a_valores[$id_ubi][$a + 2] = [
                1 => $oF_ini_periodo?->getFromLocal() ?? '',
                2 => $oF_fin_periodo?->getFromLocal() ?? '',
                3 => _('totales'),
                4 => '',
                5 => $tot_asis_pr['tot'],
                6 => $tot_asis['tot'],
                7 => '',
                8 => $tot_ing_pr['tot'],
                9 => $tot_ing['tot'],
                10 => $i_previstos_acumulados,
                11 => $i_acumulados,
                12 => '',
                'clase' => 'derecha',
            ];
        }

        return [
            'ok' => true,
            'error' => '',
            'a_cabeceras' => $aCabeceras,
            'a_valores' => $a_valores,
            'a_grupos' => $aGrupos,
            'nota' => (string)_("* Se cuentan los ingresos proporcionales correspondientes al periodo."),
            'errores' => $txt_err,
        ];
    }
}
