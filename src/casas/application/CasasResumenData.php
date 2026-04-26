<?php

namespace src\casas\application;

use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\casas\application\services\CasasResumenOcupacion;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CasaPeriodoRepositoryInterface;
use frontend\shared\web\Periodo;

/**
 * Use case: resumen económico de casas (dias ocupados, asistentes
 * previstos/reales, ingresos, gastos, aportaciones, superávit).
 * Sucesor de `apps/casas/controller/casas_resumen_ajax.php`.
 *
 * Dos modos:
 *  - `que=''`  → un único periodo (año/trimestre/rango) por casa.
 *  - `que!=''` → estadística por año (5 años) por casa.
 */
final class CasasResumenData
{
    public static function execute(array $input): array
    {
        $Qque = (string)($input['que'] ?? '');
        $Qcdc_sel = (int)($input['cdc_sel'] ?? 0);
        /** @var array $id_cdc */
        $id_cdc = (array)($input['id_cdc'] ?? []);

        $cCasasDl = self::selectCasas($Qcdc_sel, $id_cdc);

        $avisos = [];
        if ($Qque === '') {
            $oPeriodo = new Periodo();
            $oPeriodo->setDefaultAny('next');
            $oPeriodo->setAny((string)($input['year'] ?? ''));
            $oPeriodo->setEmpiezaMin((string)($input['empiezamin'] ?? ''));
            $oPeriodo->setEmpiezaMax((string)($input['empiezamax'] ?? ''));
            $oPeriodo->setPeriodo((string)($input['periodo'] ?? ''));
            $oInicio = $oPeriodo->getF_ini();
            $oFin = $oPeriodo->getF_fin();

            $a_resumen = [];
            foreach ($cCasasDl as $oCasaDl) {
                $rowCasa = self::computeCasaPeriodo($oCasaDl, $oInicio, $oFin);
                $a_resumen[$oCasaDl->getId_ubi()] = $rowCasa['row'];
                $avisos = array_merge($avisos, $rowCasa['avisos']);
            }
            self::aplicarSuperavitPadreHijo($a_resumen);
            $tot = self::calcularTotales($a_resumen);

            return [
                'modo' => 'periodo',
                'a_resumen' => $a_resumen,
                'tot' => $tot,
                'avisos' => $avisos,
            ];
        }

        // Modo anual: 6 años desde el próximo hacia atrás.
        $any_prox = (int)date('Y') + 1;
        $a_anys = [];
        for ($i = 0; $i < 6; $i++) {
            $a_anys[] = $any_prox - $i;
        }

        $a_resumen = [];
        $tot = [];
        foreach ($cCasasDl as $oCasaDl) {
            $id_ubi = $oCasaDl->getId_ubi();
            $a_resumen[$id_ubi] = [];
            foreach ($a_anys as $any) {
                $oInicio = new DateTimeLocal("$any/1/1");
                $oFin = new DateTimeLocal("$any/12/31");
                $rowCasa = self::computeCasaPeriodo($oCasaDl, $oInicio, $oFin);
                $a_resumen[$id_ubi][$any] = $rowCasa['row'];
                $avisos = array_merge($avisos, $rowCasa['avisos']);
            }
            self::aplicarSuperavitPadreHijoAnual($a_resumen, $a_anys);
        }
        foreach ($a_anys as $any) {
            $snapshot = [];
            foreach ($a_resumen as $id_ubi => $rowsAny) {
                if (isset($rowsAny[$any])) {
                    $snapshot[$id_ubi] = $rowsAny[$any];
                }
            }
            $tot[$any] = self::calcularTotales($snapshot);
        }

        return [
            'modo' => 'anual',
            'a_resumen' => $a_resumen,
            'a_anys' => $a_anys,
            'tot' => $tot,
            'avisos' => $avisos,
        ];
    }

    /** @return array Lista de objetos CasaDl/CentroEllas. */
    private static function selectCasas(int $cdc_sel, array $id_cdc): array
    {
        $aWhere = [];
        $aOperador = [];
        $cCentrosSf = [];
        switch ($cdc_sel) {
            case 1: $aWhere['sv'] = 't'; $aWhere['sf'] = 't'; break;
            case 2: $aWhere['sv'] = 'f'; $aWhere['sf'] = 't'; break;
            case 3:
                $aWhere['sv'] = 't'; $aWhere['sf'] = 't';
                $aWhere['tipo_ubi'] = 'cdcdl';
                $aWhere['tipo_casa'] = 'cdc|cdr';
                $aOperador['tipo_casa'] = '~';
                break;
            case 4: $aWhere['sv'] = 't'; break;
            case 5: $aWhere['sf'] = 't'; break;
            case 6:
                $aWhere['sf'] = 't';
                $GesCentrosSf = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                $cCentrosSf = $GesCentrosSf->getCentros(['cdc' => 't', '_ordre' => 'nombre_ubi']) ?: [];
                break;
            case 9:
                if (!empty($id_cdc)) {
                    $aWhere['id_ubi'] = '^' . implode('$|^', $id_cdc) . '$';
                    $aOperador['id_ubi'] = '~';
                }
                break;
        }
        $aWhere['_ordre'] = 'nombre_ubi';
        $GesCasaDl = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        $cCasasDl = $GesCasaDl->getCasas($aWhere, $aOperador) ?: [];
        if ($cdc_sel === 6 && $cCentrosSf) {
            foreach ($cCentrosSf as $oCentroSf) {
                $cCasasDl[] = $oCentroSf;
            }
        }
        return $cCasasDl;
    }

    /**
     * Calcula la fila resumen (secciones 0/1/2) para una casa en un
     * periodo concreto, sin aplicar override padre-hijo.
     *
     * @return array{row:array, avisos:string[]}
     */
    private static function computeCasaPeriodo($oCasaDl, $oInicio, $oFin): array
    {
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $GrupoCasaRepository = $GLOBALS['container']->get(GrupoCasaRepositoryInterface::class);
        $CasaPeriodoRepository = $GLOBALS['container']->get(CasaPeriodoRepositoryInterface::class);
        $IngresoRepository = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
        $UbiGastoRepository = $GLOBALS['container']->get(UbiGastoRepositoryInterface::class);

        $id_ubi = $oCasaDl->getId_ubi();
        $nombre_ubi = $oCasaDl->getNombre_ubi();
        $avisos = [];

        $cGrupoCasas1 = $GrupoCasaRepository->getGrupoCasas(['id_ubi_padre' => $id_ubi]) ?: [];
        $cGrupoCasas2 = $GrupoCasaRepository->getGrupoCasas(['id_ubi_hijo' => $id_ubi]) ?: [];
        $cGrupoCasas = array_merge($cGrupoCasas1, $cGrupoCasas2);
        $id_ubi_padre = 0;
        $id_ubi_hijo = 0;
        foreach ($cGrupoCasas as $oGrupoCasa) {
            $id_ubi_padre = $oGrupoCasa->getId_ubi_padre();
            $id_ubi_hijo = $oGrupoCasa->getId_ubi_hijo();
        }

        $aPeriodos = $CasaPeriodoRepository->getArrayCasaPeriodos($id_ubi, $oInicio, $oFin);

        $row = [
            0 => ['nom' => $nombre_ubi, 'detalles' => [], 'gasto' => 0],
            1 => self::inicialSeccion(),
            2 => self::inicialSeccion(),
            '_id_ubi_padre' => $id_ubi_padre,
            '_id_ubi_hijo' => $id_ubi_hijo,
        ];

        $inicioIso = $oInicio->getIso();
        $finIso = $oFin->getIso();
        $aWhere = [
            'f_ini' => $finIso,
            'f_fin' => $inicioIso,
            'id_ubi' => $id_ubi,
            'status' => 4,
            '_ordre' => 'f_ini',
        ];
        $aOperador = ['f_ini' => '<=', 'f_fin' => '>=', 'status' => '<'];
        $cActividades = $ActividadRepository->getActividades($aWhere, $aOperador) ?: [];

        $a = 0;
        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $oF_ini_act = $oActividad->getF_ini();
            $oF_fin_act = $oActividad->getF_fin();
            $nom_activ = $oActividad->getNom_activ();

            $num_dias_act = $oActividad->getDuracionAumentada();
            $num_dias = $oActividad->getDuracionEnPeriodo($oF_ini_act, $oF_fin_act);
            $num_dias_real = $oActividad->getDuracionReal();
            if ($num_dias_real <= 0) { continue; }
            $factor_dias = ($num_dias / $num_dias_real);

            $a_ocupacion = CasasResumenOcupacion::diasOcupacion($aPeriodos, $oActividad, $oF_ini_act, $oF_fin_act);
            if (!empty($a_ocupacion['avisos'])) {
                $avisos = array_merge($avisos, $a_ocupacion['avisos']);
            }
            $factor = ($num_dias_act - $num_dias_real) / $num_dias_real;
            $a_ocupacion[1] = round(((float)$a_ocupacion[1]) * (1 + $factor), 1);
            $a_ocupacion[2] = round(((float)$a_ocupacion[2]) * (1 + $factor), 1);

            $row[1]['dias'] += $a_ocupacion[1];
            $row[2]['dias'] += $a_ocupacion[2];

            $oIngreso = $IngresoRepository->findById($id_activ);
            $num_asistentes_previstos = $oIngreso?->getNumAsistentesPrevistosVo()?->value() ?? 0;
            $num_asistentes = $oIngreso?->getNumAsistentesVo()?->value() ?? 0;
            $ingresos_previstos_raw = $oIngreso?->getIngresosPrevistosVo()?->value();
            $ingresos_raw = $oIngreso?->getIngresosVo()?->value();
            if ($ingresos_previstos_raw === null || $ingresos_previstos_raw === 0.0) {
                $avisos[] = sprintf((string)_("No hay ingresos previstos para la actividad %s"), $nom_activ);
                $ingresos_previstos = 0.0;
                $ingresos = 0.0;
            } else {
                $ingresos_previstos = $factor_dias * (float)$ingresos_previstos_raw;
                $ingresos = $factor_dias * (float)($ingresos_raw ?? 0);
            }
            if (empty($num_asistentes_previstos)) {
                $avisos[] = sprintf((string)_("No hay asistentes previstos para la actividad %s"), $nom_activ);
            }

            $row[1]['asist_prev'] += $num_asistentes_previstos * $a_ocupacion[1];
            $row[2]['asist_prev'] += $num_asistentes_previstos * $a_ocupacion[2];
            $row[1]['asist'] += $num_asistentes * $a_ocupacion[1];
            $row[2]['asist'] += $num_asistentes * $a_ocupacion[2];

            $in = [1 => '', 2 => ''];
            if (($a_ocupacion[1] + $a_ocupacion[2]) > 0) {
                $sumOcup = $a_ocupacion[1] + $a_ocupacion[2];
                $in[1] = round(($ingresos / $sumOcup) * $a_ocupacion[1], 2);
                $in[2] = round(($ingresos / $sumOcup) * $a_ocupacion[2], 2);
                $row[1]['in_prev_acu'] += round(($ingresos_previstos / $sumOcup) * $a_ocupacion[1], 2);
                $row[2]['in_prev_acu'] += round(($ingresos_previstos / $sumOcup) * $a_ocupacion[2], 2);
                $row[1]['in_acu'] += $in[1];
                $row[2]['in_acu'] += $in[2];
            }
            $row[0]['detalles'][] = [
                'nom_activ' => $nom_activ,
                'ocup_sv' => $a_ocupacion[1],
                'ocup_sf' => $a_ocupacion[2],
                'in_sv' => $in[1],
                'in_sf' => $in[2],
            ];
            $a++;
        }
        if ($a < 1) {
            $row[0]['detalles'][] = ['vacio' => true];
        }

        foreach ([1, 2] as $s) {
            $row[$s]['dias%'] = self::pct($row[1]['dias'] + $row[2]['dias'], $row[$s]['dias']);
            $row[$s]['asist_prev%'] = self::pct($row[1]['asist_prev'] + $row[2]['asist_prev'], $row[$s]['asist_prev']);
            $row[$s]['asist%'] = self::pct($row[1]['asist'] + $row[2]['asist'], $row[$s]['asist']);
            $row[$s]['in_prev_acu%'] = self::pct($row[1]['in_prev_acu'] + $row[2]['in_prev_acu'], $row[$s]['in_prev_acu']);
            $row[$s]['in_acu%'] = self::pct($row[1]['in_acu'] + $row[2]['in_acu'], $row[$s]['in_acu']);
        }

        $row[1]['aportacion'] = (float)$UbiGastoRepository->getSumaGastos($id_ubi, 1, $oInicio, $oFin);
        $row[2]['aportacion'] = (float)$UbiGastoRepository->getSumaGastos($id_ubi, 2, $oInicio, $oFin);
        $row[0]['gasto'] = (float)$UbiGastoRepository->getSumaGastos($id_ubi, 3, $oInicio, $oFin);

        $a_repartoGastos = CasasResumenOcupacion::reparto($aPeriodos);
        $sumReparto = (float)$a_repartoGastos[1] + (float)$a_repartoGastos[2];
        if ($sumReparto > 0) {
            $row[1]['gasto%'] = round(((float)$a_repartoGastos[1]) / $sumReparto * 100, 2);
            $row[2]['gasto%'] = round(((float)$a_repartoGastos[2]) / $sumReparto * 100, 2);
            $row[1]['gasto'] = round($row[0]['gasto'] * $row[1]['gasto%'] / 100, 2);
            $row[2]['gasto'] = round($row[0]['gasto'] * $row[2]['gasto%'] / 100, 2);
            $row[1]['superavit'] = round(($row[1]['aportacion'] + $row[1]['in_acu']) - $row[1]['gasto'], 2);
            $row[2]['superavit'] = round(($row[2]['aportacion'] + $row[2]['in_acu']) - $row[2]['gasto'], 2);
        } else {
            $row[1]['gasto%'] = '-';
            $row[2]['gasto%'] = '-';
            $row[1]['superavit'] = 0;
            $row[2]['superavit'] = 0;
        }

        return ['row' => $row, 'avisos' => $avisos];
    }

    private static function inicialSeccion(): array
    {
        return [
            'dias' => 0, 'dias%' => '-',
            'asist_prev' => 0, 'asist_prev%' => '-',
            'asist' => 0, 'asist%' => '-',
            'in_prev_acu' => 0, 'in_prev_acu%' => '-',
            'in_acu' => 0, 'in_acu%' => '-',
            'gasto' => 0, 'gasto%' => '-',
            'aportacion' => 0,
            'superavit' => 0,
        ];
    }

    private static function pct(float $total, float $parte): string
    {
        if ($total <= 0) { return '-'; }
        return (string)round($parte / $total * 100, 2);
    }

    private static function aplicarSuperavitPadreHijo(array &$a_resumen): void
    {
        foreach ($a_resumen as $id_ubi => $row) {
            $id_ubi_padre = $row['_id_ubi_padre'] ?? 0;
            $id_ubi_hijo = $row['_id_ubi_hijo'] ?? 0;
            if ($id_ubi === $id_ubi_hijo && $id_ubi_padre && isset($a_resumen[$id_ubi_padre])) {
                $padre = $a_resumen[$id_ubi_padre];
                foreach ([1, 2] as $s) {
                    if (is_numeric($padre[$s]['gasto%'])) {
                        $in = ($padre[$s]['aportacion'] + $padre[$s]['in_acu'] + $row[$s]['in_acu']);
                        $out = round($padre[$s]['gasto%'] * $padre[0]['gasto'] / 100, 2);
                        $a_resumen[$id_ubi_padre][$s]['superavit'] = round($in - $out, 2);
                    }
                    $a_resumen[$id_ubi][$s]['superavit'] = '';
                }
            }
        }
    }

    private static function aplicarSuperavitPadreHijoAnual(array &$a_resumen, array $a_anys): void
    {
        foreach ($a_anys as $any) {
            $snapshot = [];
            foreach ($a_resumen as $id_ubi => $rowsAny) {
                if (isset($rowsAny[$any])) {
                    $snapshot[$id_ubi] = $rowsAny[$any];
                }
            }
            self::aplicarSuperavitPadreHijo($snapshot);
            foreach ($snapshot as $id_ubi => $row) {
                $a_resumen[$id_ubi][$any] = $row;
            }
        }
    }

    private static function calcularTotales(array $a_resumen): array
    {
        $tot = [
            0 => ['gasto' => 0],
            1 => self::inicialSeccion(),
            2 => self::inicialSeccion(),
        ];
        foreach ([1, 2] as $s) {
            foreach (['dias', 'asist_prev', 'asist', 'in_prev_acu', 'in_acu', 'gasto', 'aportacion', 'superavit'] as $k) {
                $tot[$s][$k] = 0;
                foreach ($a_resumen as $row) {
                    $v = $row[$s][$k] ?? 0;
                    if (is_numeric($v)) {
                        $tot[$s][$k] += (float)$v;
                    }
                }
            }
        }
        foreach ($a_resumen as $row) {
            $tot[0]['gasto'] += (float)($row[0]['gasto'] ?? 0);
        }
        foreach (['dias', 'asist_prev', 'asist', 'in_prev_acu', 'in_acu', 'gasto'] as $k) {
            $tot[1][$k . '%'] = self::pct($tot[1][$k] + $tot[2][$k], $tot[1][$k]);
            $tot[2][$k . '%'] = self::pct($tot[1][$k] + $tot[2][$k], $tot[2][$k]);
        }
        return $tot;
    }
}
