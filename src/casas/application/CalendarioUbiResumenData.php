<?php

namespace src\casas\application;

use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use web\TiposActividades;

/**
 * Data builder: estudio económico y de ocupación de una casa.
 *
 * Sucesor de `apps/casas/controller/calendario_ubi_resumen_ajax.php`.
 * Calcula todos los agregados (tarifas actuales/previstas, días de
 * ocupación, gastos, previsión de ingresos, actividades del año
 * próximo…) y devuelve un payload plano para que el frontend construya
 * el HTML del informe.
 */
final class CalendarioUbiResumenData
{
    /**
     * @return array{
     *   ok: bool,
     *   error: string,
     *   any_anterior: int,
     *   any_actual: int,
     *   any_prev: int,
     *   id_ubi: int,
     *   seccion: string,
     *   nombre_ubi: string,
     *   plazas_min: int,
     *   G: int,
     *   inc_t: int,
     *   p_dv: int,
     *   p_df: int,
     *   a_tarifas_actual: array<int,array{letra:string,modo:int,cantidad:float}>,
     *   a_tarifas_prev: array<int,array{letra:string,modo:int,cantidad:float,id_item:int}>,
     *   r_it: float,
     *   r_idl: float,
     *   r_idef: float,
     *   r_ip: float,
     *   r_ta: int,
     *   r_tia: float,
     *   p_ip: float,
     *   p_ip_txt: string,
     *   p_ta_min: int,
     *   p_ta_min_txt: string,
     *   p_dseccion: int,
     *   total_txt: string,
     *   a_actividades: array<int,array{nom:string,dias:float,asistentes:int|string,asistencias:int|string,id_tarifa:string,ingresos:string|float}>,
     *   p_tac: int,
     *   p_tda: float,
     *   p_tap: int,
     *   p_ta: int|float,
     *   p_tia: float,
     *   p_tarifa: float,
     *   p_ti_min: float,
     *   dias_libres: int|float,
     *   dif_asistencias: float,
     *   dif_ingresos: float,
     *   inc_p: int|string,
     *   inc_d: int|float,
     *   inc_pt: int|float
     * }
     */
    public static function execute(array $input): array
    {
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $seccion = (string)($input['seccion'] ?? '');
        $G = (int)($input['G'] ?? 0);
        $inc_t = (int)($input['inc_t'] ?? 0);

        $any_actual = (int)date('Y');
        $any_anterior = $any_actual - 1;
        $any_prev = $any_actual + 1;

        $isfsv = 0;
        if ($seccion === 'sv') {
            $isfsv = 1;
        } elseif ($seccion === 'sf') {
            $isfsv = 2;
        }

        $casaRepo = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $oCasa = $casaRepo->findById($id_ubi);
        if ($oCasa === null) {
            return self::empty($id_ubi, $seccion, $any_actual, $any_anterior, $any_prev, $G, $inc_t)
                + ['ok' => false, 'error' => (string)_("Casa no encontrada")];
        }
        $nombre_ubi = (string)$oCasa->getNombre_ubi();
        $plazas_min = (int)$oCasa->getPlazas_min();

        // Tarifas
        $tipoTarifaRepo = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $tarifaUbiRepo = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
        $cTipoTarifas = $tipoTarifaRepo->getTipoTarifas(['sfsv' => $isfsv]);
        $a_tarifas_actual = [];
        $a_tarifas_prev = [];
        $ultimo_id_tarifa = 0;
        if (is_array($cTipoTarifas)) {
            foreach ($cTipoTarifas as $oTipoTarifa) {
                $id_tarifa = (int)$oTipoTarifa->getId_tarifa();
                $ultimo_id_tarifa = $id_tarifa;
                $modo = (int)$oTipoTarifa->getModo();
                $letra = (string)$oTipoTarifa->getLetra();

                $cTarifasUbi = $tarifaUbiRepo->getTarifaUbis([
                    'id_tarifa' => $id_tarifa,
                    'id_ubi' => $id_ubi,
                    'year' => $any_actual,
                ]);
                $cantidad_actual = 0.0;
                if (is_array($cTarifasUbi) && isset($cTarifasUbi[0])) {
                    $cantidad_actual = (float)$cTarifasUbi[0]->getCantidad();
                }
                $a_tarifas_actual[$id_tarifa] = [
                    'letra' => $letra,
                    'modo' => $modo,
                    'cantidad' => $cantidad_actual,
                ];

                $cTarifasUbiPrev = $tarifaUbiRepo->getTarifaUbis([
                    'id_tarifa' => $id_tarifa,
                    'id_ubi' => $id_ubi,
                    'year' => $any_prev,
                ]);
                if (is_array($cTarifasUbiPrev) && isset($cTarifasUbiPrev[0])) {
                    $oTarifaPrev = $cTarifasUbiPrev[0];
                    $cantidad = (float)$oTarifaPrev->getCantidad();
                    if ($inc_t !== 0) {
                        $cantidad = $cantidad_actual * (1 + $inc_t / 100);
                    }
                    $a_tarifas_prev[$id_tarifa] = [
                        'id_item' => (int)$oTarifaPrev->getId_item(),
                        'modo' => $modo,
                        'letra' => $letra,
                        'cantidad' => $cantidad,
                    ];
                } else {
                    $cantidad = $inc_t !== 0
                        ? $cantidad_actual * (1 + $inc_t / 100)
                        : $cantidad_actual;
                    $a_tarifas_prev[$id_tarifa] = [
                        'id_item' => 0,
                        'modo' => $modo,
                        'letra' => $letra,
                        'cantidad' => $cantidad,
                    ];
                }
            }
        }

        // Días de ocupación (año previsto)
        $oIniPrev = new DateTimeLocal("$any_prev/1/1");
        $oFinPrev = new DateTimeLocal("$any_prev/12/31");
        $casaPeriodoRepo = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        $p_dv = (int)$casaPeriodoRepo->getCasaPeriodosDias(1, $id_ubi, $oIniPrev, $oFinPrev);
        $p_df = (int)$casaPeriodoRepo->getCasaPeriodosDias(2, $id_ubi, $oIniPrev, $oFinPrev);

        // Gastos año anterior
        $oIniAnt = new DateTimeLocal("$any_anterior/1/1");
        $oFinAnt = new DateTimeLocal("$any_anterior/12/31");
        $ubiGastoRepo = $GLOBALS['container']->get(UbiGastoRepositoryInterface::class);
        $r_idl_sv = (float)$ubiGastoRepo->getSumaGastos($id_ubi, 1, $oIniAnt, $oFinAnt);
        $r_idl_sf = (float)$ubiGastoRepo->getSumaGastos($id_ubi, 2, $oIniAnt, $oFinAnt);
        $r_it = (float)$ubiGastoRepo->getSumaGastos($id_ubi, 3, $oIniAnt, $oFinAnt);

        if ($r_it <= 0.0) {
            return self::empty($id_ubi, $seccion, $any_actual, $any_anterior, $any_prev, $G, $inc_t)
                + [
                    'ok' => false,
                    'error' => 'sin_gastos_anterior',
                    'nombre_ubi' => $nombre_ubi,
                    'plazas_min' => $plazas_min,
                    'a_tarifas_actual' => $a_tarifas_actual,
                    'a_tarifas_prev' => $a_tarifas_prev,
                    'p_dv' => $p_dv,
                    'p_df' => $p_df,
                ];
        }

        // Previsión de ingresos por sección
        $p_ip = 0.0;
        $p_ip_txt = '';
        $p_ta_min = 0;
        $p_ta_min_txt = '';
        $p_dseccion = 0;
        $total_txt = '';
        $r_idl = 0.0;
        if ($seccion === 'sv') {
            $r_idl = $r_idl_sv;
            $p_ta_min = $p_dv * $plazas_min;
            $p_dseccion = $p_dv;
            $p_ta_min_txt = (string)_("Mínimo de asistencias (p_dv.M)");
            $total_txt = (string)_("según asignación dias sv");
            $p_ip = ($p_dv + $p_df) === 0
                ? 0.0
                : round((1 + $G / 100) * $r_it * $p_dv / ($p_dv + $p_df), 2);
            $p_ip_txt = (string)_("Previsión de ingresos sv a 2 años vista (1+G).r_it(p_dv/(p_dv+p_df))");
        } elseif ($seccion === 'sf') {
            $r_idl = $r_idl_sf;
            $p_ta_min = $p_df * $plazas_min;
            $p_dseccion = $p_df;
            $p_ta_min_txt = (string)_("Mínimo de asistencias (p_df.M)");
            $total_txt = (string)_("según asignación dias sf");
            $p_ip = ($p_dv + $p_df) === 0
                ? 0.0
                : round((1 + $G / 100) * $r_it * $p_df / ($p_dv + $p_df), 2);
            $p_ip_txt = (string)_("Previsión de ingresos sf a 2 años vista (1+G).r_it(p_df/(p_dv+p_df))");
        }

        // Actividades del año previsto
        $aWhere = [
            'id_ubi' => $id_ubi,
            'f_ini' => $oFinPrev->getIso(),
            'f_fin' => $oIniPrev->getIso(),
            'id_tipo_activ' => "^$isfsv",
            '_ordre' => 'f_ini',
        ];
        $aOperador = [
            'f_ini' => '<=',
            'f_fin' => '>=',
            'id_tipo_activ' => '~',
        ];
        $actividadRepo = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $cActividades = $actividadRepo->getActividades($aWhere, $aOperador);

        $ingresoRepo = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
        $a_actividades = [];
        $p_tda = 0.0;
        $p_tap = 0;
        $p_ta = 0;
        $p_tia = 0.0;
        $r_tia = 0.0;
        $i = 0;
        if (is_array($cActividades)) {
            foreach ($cActividades as $oActividad) {
                $i++;
                $id_activ = (int)$oActividad->getId_activ();
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $f_ini_local = $oActividad->getF_ini()?->getFromLocal() ?? '';
                $f_fin_local = $oActividad->getF_fin()?->getFromLocal() ?? '';

                $num_dias_act = (float)$oActividad->getDuracionAumentada();
                $num_dias_real = (float)$oActividad->getDuracionReal();
                $num_dias_periodo = (float)$oActividad->getDuracionEnPeriodo($oIniPrev, $oFinPrev);

                $factor = $num_dias_real > 0
                    ? ($num_dias_act - $num_dias_real) / $num_dias_real
                    : 0.0;
                $num_dias = round($num_dias_periodo * (1 + $factor), 1);

                $oTipoActiv = new TiposActividades($id_tipo_activ);
                $nom = $oTipoActiv->getNom() . " ($f_ini_local - $f_fin_local)";

                // Tarifa asociada: la legacy sólo miraba el último id_tarifa del
                // bucle anterior; replicamos ese comportamiento.
                $id_tarifa = $ultimo_id_tarifa;
                if ($id_tarifa > 0 && isset($a_tarifas_prev[$id_tarifa])) {
                    $oIngreso = $ingresoRepo->findById($id_activ);
                    $num_asistentes = $oIngreso?->getNum_asistentes() ?? 0;
                    if (empty($num_asistentes)) {
                        $num_asistentes = $plazas_min;
                    }
                    $asistencias = $num_dias * $num_asistentes;
                    $modoPrev = (int)($a_tarifas_prev[$id_tarifa]['modo'] ?? 0);
                    $cantidadPrev = (float)($a_tarifas_prev[$id_tarifa]['cantidad'] ?? 0);
                    $cantidadActual = (float)($a_tarifas_actual[$id_tarifa]['cantidad'] ?? 0);

                    if ($modoPrev === 1) {
                        $ingresos = round($num_asistentes * $cantidadPrev, 2);
                        $ingresos_actual = round($num_asistentes * $cantidadActual, 2);
                    } else {
                        $ingresos = round($asistencias * $cantidadPrev, 2);
                        $ingresos_actual = round($asistencias * $cantidadActual, 2);
                    }
                    $letra_tarifa = (string)($a_tarifas_actual[$id_tarifa]['letra'] ?? '');
                } else {
                    $num_asistentes = 0;
                    $asistencias = '?';
                    $ingresos = (string)_("tar. no definida");
                    $ingresos_actual = 0.0;
                    $letra_tarifa = '';
                }

                $a_actividades[] = [
                    'nom' => $nom,
                    'dias' => $num_dias,
                    'asistentes' => $num_asistentes,
                    'asistencias' => $asistencias,
                    'id_tarifa' => $letra_tarifa,
                    'ingresos' => $ingresos,
                ];
                $p_tda += $num_dias;
                $p_tap += (int)$num_asistentes;
                if (is_numeric($asistencias)) {
                    $p_ta += $asistencias;
                }
                if (is_numeric($ingresos)) {
                    $p_tia += $ingresos;
                }
                $r_tia += (float)$ingresos_actual;
            }
        }
        $p_tac = $i < 1 ? 1 : $i;

        $p_tarifa = $p_ta > 0 ? round($p_tia / $p_ta, 2) : 0.0;
        $p_ti_min = round($p_ta_min * $p_tarifa, 2);

        $dias_libres = match ($seccion) {
            'sv' => $p_dv - $p_tda,
            'sf' => $p_df - $p_tda,
            default => 0,
        };
        $dif_asistencias = round($p_ta_min - $p_ta, 2);
        $dif_ingresos = round($p_ti_min - $p_tia, 2);

        if ($p_tarifa === 0.0) {
            $inc_p = (string)_("no disponible");
            $inc_d = 0;
            $inc_pt = 0;
        } else {
            $inc_p = (int)round(($p_ip - $p_tia) / $p_tarifa);
            $inc_d = $plazas_min > 0 ? round($inc_p / $plazas_min) : 0;
            $inc_pt = $r_tia > 0 ? (int)round(($p_ip / $r_tia - 1) * 100) : 0;
        }

        return [
            'ok' => true,
            'error' => '',
            'id_ubi' => $id_ubi,
            'seccion' => $seccion,
            'any_actual' => $any_actual,
            'any_anterior' => $any_anterior,
            'any_prev' => $any_prev,
            'G' => $G,
            'inc_t' => $inc_t,
            'nombre_ubi' => $nombre_ubi,
            'plazas_min' => $plazas_min,
            'p_dv' => $p_dv,
            'p_df' => $p_df,
            'a_tarifas_actual' => $a_tarifas_actual,
            'a_tarifas_prev' => $a_tarifas_prev,
            'r_it' => $r_it,
            'r_idl' => $r_idl,
            'r_idef' => 0.0,
            'r_ip' => 0.0,
            'r_ta' => 0,
            'r_tia' => $r_tia,
            'r_tac' => 0,
            'r_tda' => 0,
            'r_tap' => 0,
            'p_ip' => $p_ip,
            'p_ip_txt' => $p_ip_txt,
            'p_ta_min' => $p_ta_min,
            'p_ta_min_txt' => $p_ta_min_txt,
            'p_dseccion' => $p_dseccion,
            'total_txt' => $total_txt,
            'a_actividades' => $a_actividades,
            'p_tac' => $p_tac,
            'p_tda' => $p_tda,
            'p_tap' => $p_tap,
            'p_ta' => $p_ta,
            'p_tia' => $p_tia,
            'p_tarifa' => $p_tarifa,
            'p_ti_min' => $p_ti_min,
            'dias_libres' => $dias_libres,
            'dif_asistencias' => $dif_asistencias,
            'dif_ingresos' => $dif_ingresos,
            'inc_p' => $inc_p,
            'inc_d' => $inc_d,
            'inc_pt' => $inc_pt,
        ];
    }

    private static function empty(
        int $id_ubi,
        string $seccion,
        int $any_actual,
        int $any_anterior,
        int $any_prev,
        int $G,
        int $inc_t
    ): array {
        return [
            'id_ubi' => $id_ubi,
            'seccion' => $seccion,
            'any_actual' => $any_actual,
            'any_anterior' => $any_anterior,
            'any_prev' => $any_prev,
            'G' => $G,
            'inc_t' => $inc_t,
            'nombre_ubi' => '',
            'plazas_min' => 0,
            'p_dv' => 0,
            'p_df' => 0,
            'a_tarifas_actual' => [],
            'a_tarifas_prev' => [],
            'r_it' => 0.0,
            'r_idl' => 0.0,
            'r_idef' => 0.0,
            'r_ip' => 0.0,
            'r_ta' => 0,
            'r_tia' => 0.0,
            'r_tac' => 0,
            'r_tda' => 0,
            'r_tap' => 0,
            'p_ip' => 0.0,
            'p_ip_txt' => '',
            'p_ta_min' => 0,
            'p_ta_min_txt' => '',
            'p_dseccion' => 0,
            'total_txt' => '',
            'a_actividades' => [],
            'p_tac' => 0,
            'p_tda' => 0.0,
            'p_tap' => 0,
            'p_ta' => 0,
            'p_tia' => 0.0,
            'p_tarifa' => 0.0,
            'p_ti_min' => 0.0,
            'dias_libres' => 0,
            'dif_asistencias' => 0.0,
            'dif_ingresos' => 0.0,
            'inc_p' => 0,
            'inc_d' => 0,
            'inc_pt' => 0,
        ];
    }
}
