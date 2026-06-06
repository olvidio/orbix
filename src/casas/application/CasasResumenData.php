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
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroEllas;
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
    public function __construct(
        private CasaDlRepositoryInterface $casaDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private ActividadRepositoryInterface $actividadRepository,
        private GrupoCasaRepositoryInterface $grupoCasaRepository,
        private CasaPeriodoRepositoryInterface $casaPeriodoRepository,
        private IngresoRepositoryInterface $ingresoRepository,
        private UbiGastoRepositoryInterface $ubiGastoRepository,
    ) {
    }

    /**
     * @param array{
     *   que?: string,
     *   cdc_sel?: int|string,
     *   id_cdc?: list<int|string>,
     *   year?: string,
     *   empiezamin?: string,
     *   empiezamax?: string,
     *   periodo?: string
     * } $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $Qque = $input['que'] ?? '';
        $Qcdc_sel = isset($input['cdc_sel']) && is_numeric($input['cdc_sel']) ? (int) $input['cdc_sel'] : 0;
        $id_cdc = $input['id_cdc'] ?? [];

        $cCasasDl = $this->selectCasas($Qcdc_sel, $id_cdc);

        $avisos = [];
        if ($Qque === '') {
            $oPeriodo = new Periodo();
            $oPeriodo->setDefaultAny('next');
            $oPeriodo->setAny($input['year'] ?? '');
            $oPeriodo->setEmpiezaMin($input['empiezamin'] ?? '');
            $oPeriodo->setEmpiezaMax($input['empiezamax'] ?? '');
            $oPeriodo->setPeriodo($input['periodo'] ?? '');
            $oInicio = new DateTimeLocal($oPeriodo->getF_ini()->getIso());
            $oFin = new DateTimeLocal($oPeriodo->getF_fin()->getIso());

            $a_resumen = [];
            foreach ($cCasasDl as $oCasaDl) {
                $rowCasa = $this->computeCasaPeriodo($oCasaDl, $oInicio, $oFin);
                $a_resumen[(int) $oCasaDl->getId_ubi()] = $rowCasa['row'];
                $avisos = array_merge($avisos, $rowCasa['avisos']);
            }
            $a_resumen = $this->aplicarSuperavitPadreHijo($a_resumen);
            $tot = $this->calcularTotales($a_resumen);

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
            $id_ubi = (int) $oCasaDl->getId_ubi();
            $a_resumen[$id_ubi] = [];
            foreach ($a_anys as $any) {
                $oInicio = new DateTimeLocal("$any/1/1");
                $oFin = new DateTimeLocal("$any/12/31");
                $rowCasa = $this->computeCasaPeriodo($oCasaDl, $oInicio, $oFin);
                $a_resumen[$id_ubi][$any] = $rowCasa['row'];
                $avisos = array_merge($avisos, $rowCasa['avisos']);
            }
            $a_resumen = $this->aplicarSuperavitPadreHijoAnual($a_resumen, $a_anys);
        }
        foreach ($a_anys as $any) {
            $snapshot = [];
            foreach ($a_resumen as $id_ubi => $rowsAny) {
                if (isset($rowsAny[$any])) {
                    $snapshot[$id_ubi] = $rowsAny[$any];
                }
            }
            $tot[$any] = $this->calcularTotales($snapshot);
        }

        return [
            'modo' => 'anual',
            'a_resumen' => $a_resumen,
            'a_anys' => $a_anys,
            'tot' => $tot,
            'avisos' => $avisos,
        ];
    }

    /**
     * @param list<int|string> $id_cdc
     * @return list<Casa|CentroEllas>
     */
    private function selectCasas(int $cdc_sel, array $id_cdc): array
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
                $cCentrosSf = $this->centroEllasRepository->getCentros(['cdc' => 't', '_ordre' => 'nombre_ubi']) ?: [];
                break;
            case 9:
                if (!empty($id_cdc)) {
                    $aWhere['id_ubi'] = '^' . implode('$|^', $id_cdc) . '$';
                    $aOperador['id_ubi'] = '~';
                }
                break;
        }
        $aWhere['_ordre'] = 'nombre_ubi';
        /** @var list<Casa|CentroEllas> $cCasasDl */
        $cCasasDl = $this->casaDlRepository->getCasas($aWhere, $aOperador) ?: [];
        if ($cdc_sel === 6 && $cCentrosSf !== []) {
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
     * @return array{
     *   row: array{
     *     0: array{nom: string, detalles: list<array<string, mixed>>, gasto: float|int},
     *     1: array<string, float|int|string>,
     *     2: array<string, float|int|string>,
     *     _id_ubi_padre: int,
     *     _id_ubi_hijo: int
     *   },
     *   avisos: list<string>
     * }
     */
    private function computeCasaPeriodo(Casa|CentroEllas $oCasaDl, DateTimeLocal $oInicio, DateTimeLocal $oFin): array
    {
        $id_ubi = $oCasaDl->getId_ubi();
        $nombre_ubi = $oCasaDl->getNombre_ubi();
        $avisos = [];

        $cGrupoCasas1 = $this->grupoCasaRepository->getGrupoCasas(['id_ubi_padre' => $id_ubi]) ?: [];
        $cGrupoCasas2 = $this->grupoCasaRepository->getGrupoCasas(['id_ubi_hijo' => $id_ubi]) ?: [];
        $cGrupoCasas = array_merge($cGrupoCasas1, $cGrupoCasas2);
        $id_ubi_padre = 0;
        $id_ubi_hijo = 0;
        foreach ($cGrupoCasas as $oGrupoCasa) {
            $id_ubi_padre = $oGrupoCasa->getId_ubi_padre();
            $id_ubi_hijo = $oGrupoCasa->getId_ubi_hijo();
        }

        $aPeriodos = $this->casaPeriodoRepository->getArrayCasaPeriodos($id_ubi, $oInicio, $oFin);

        /** @var list<array{iso_ini: string, iso_fin: string, sfsv: int|string}> $aPeriodosList */
        $aPeriodosList = array_values($aPeriodos);

        $row = [
            0 => ['nom' => $nombre_ubi, 'detalles' => [], 'gasto' => 0],
            1 => $this->inicialSeccion(),
            2 => $this->inicialSeccion(),
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
        $cActividades = $this->actividadRepository->getActividades($aWhere, $aOperador) ?: [];

        $a = 0;
        foreach ($cActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $oF_ini_act = $oActividad->getF_ini();
            $oF_fin_act = $oActividad->getF_fin();
            if (!($oF_ini_act instanceof DateTimeLocal) || !($oF_fin_act instanceof DateTimeLocal)) {
                continue;
            }
            $nom_activ = $oActividad->getNom_activ();

            $num_dias_act = $oActividad->getDuracionAumentada();
            $num_dias = $oActividad->getDuracionEnPeriodo($oF_ini_act, $oF_fin_act);
            $num_dias_real = $oActividad->getDuracionReal();
            if ($num_dias_real <= 0) { continue; }
            $factor_dias = ($num_dias / $num_dias_real);

            $a_ocupacion = CasasResumenOcupacion::diasOcupacion($aPeriodosList, $oActividad, $oF_ini_act, $oF_fin_act);
            if (!empty($a_ocupacion['avisos'])) {
                $avisos = array_merge($avisos, $a_ocupacion['avisos']);
            }
            $factor = ($num_dias_act - $num_dias_real) / $num_dias_real;
            $a_ocupacion[1] = round(((float)$a_ocupacion[1]) * (1 + $factor), 1);
            $a_ocupacion[2] = round(((float)$a_ocupacion[2]) * (1 + $factor), 1);

            $row[1]['dias'] += $a_ocupacion[1];
            $row[2]['dias'] += $a_ocupacion[2];

            $oIngreso = $this->ingresoRepository->findById($id_activ);
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
            $row[$s]['dias%'] = $this->pct($row[1]['dias'] + $row[2]['dias'], $row[$s]['dias']);
            $row[$s]['asist_prev%'] = $this->pct($row[1]['asist_prev'] + $row[2]['asist_prev'], $row[$s]['asist_prev']);
            $row[$s]['asist%'] = $this->pct($row[1]['asist'] + $row[2]['asist'], $row[$s]['asist']);
            $row[$s]['in_prev_acu%'] = $this->pct($row[1]['in_prev_acu'] + $row[2]['in_prev_acu'], $row[$s]['in_prev_acu']);
            $row[$s]['in_acu%'] = $this->pct($row[1]['in_acu'] + $row[2]['in_acu'], $row[$s]['in_acu']);
        }

        $row[1]['aportacion'] = (float)$this->ubiGastoRepository->getSumaGastos($id_ubi, 1, $oInicio, $oFin);
        $row[2]['aportacion'] = (float)$this->ubiGastoRepository->getSumaGastos($id_ubi, 2, $oInicio, $oFin);
        $row[0]['gasto'] = (float)$this->ubiGastoRepository->getSumaGastos($id_ubi, 3, $oInicio, $oFin);

        $a_repartoGastos = CasasResumenOcupacion::reparto($aPeriodosList);
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

    /** @return array<string, int|float|string> */
    private function inicialSeccion(): array
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

    private function pct(float $total, float $parte): string
    {
        if ($total <= 0) { return '-'; }
        return (string)round($parte / $total * 100, 2);
    }

    /**
     * @param array<int, array{
     *   0: array{nom: string, detalles: list<array<string, mixed>>, gasto: float|int},
     *   1: array<string, float|int|string>,
     *   2: array<string, float|int|string>,
     *   _id_ubi_padre: int,
     *   _id_ubi_hijo: int
     * }> $a_resumen
     * @return array<int, array{
     *   0: array{nom: string, detalles: list<array<string, mixed>>, gasto: float|int},
     *   1: array<string, float|int|string>,
     *   2: array<string, float|int|string>,
     *   _id_ubi_padre: int,
     *   _id_ubi_hijo: int
     * }>
     */
    private function aplicarSuperavitPadreHijo(array $a_resumen): array
    {
        $original = $a_resumen;
        foreach ($a_resumen as $id_ubi => $row) {
            $id_ubi_padre = $row['_id_ubi_padre'];
            $id_ubi_hijo = $row['_id_ubi_hijo'];
            if ($id_ubi !== $id_ubi_hijo || $id_ubi_padre === 0 || !isset($original[$id_ubi_padre])) {
                continue;
            }
            $padre = $original[$id_ubi_padre];
            foreach ([1, 2] as $s) {
                $gastoPctPadre = $padre[$s]['gasto%'];
                if (is_numeric($gastoPctPadre)) {
                    $aportacionPadre = is_numeric($padre[$s]['aportacion']) ? (float) $padre[$s]['aportacion'] : 0.0;
                    $inAcuPadre = is_numeric($padre[$s]['in_acu']) ? (float) $padre[$s]['in_acu'] : 0.0;
                    $inAcuHijo = is_numeric($row[$s]['in_acu']) ? (float) $row[$s]['in_acu'] : 0.0;
                    $gastoTotalPadre = (float) $padre[0]['gasto'];
                    $in = $aportacionPadre + $inAcuPadre + $inAcuHijo;
                    $out = round((float) $gastoPctPadre * $gastoTotalPadre / 100, 2);
                    $padreRow = $a_resumen[$id_ubi_padre];
                    $padreRow[$s]['superavit'] = round($in - $out, 2);
                    $a_resumen[$id_ubi_padre] = $padreRow;
                }
                $hijoRow = $a_resumen[$id_ubi];
                $hijoRow[$s]['superavit'] = '';
                $a_resumen[$id_ubi] = $hijoRow;
            }
        }

        return $a_resumen;
    }

    /**
     * @param array<int, array<int, array{
     *   0: array{nom: string, detalles: list<array<string, mixed>>, gasto: float|int},
     *   1: array<string, float|int|string>,
     *   2: array<string, float|int|string>,
     *   _id_ubi_padre: int,
     *   _id_ubi_hijo: int
     * }>> $a_resumen
     * @param list<int> $a_anys
     * @return array<int, array<int, array{
     *   0: array{nom: string, detalles: list<array<string, mixed>>, gasto: float|int},
     *   1: array<string, float|int|string>,
     *   2: array<string, float|int|string>,
     *   _id_ubi_padre: int,
     *   _id_ubi_hijo: int
     * }>>
     */
    private function aplicarSuperavitPadreHijoAnual(array $a_resumen, array $a_anys): array
    {
        foreach ($a_anys as $any) {
            $snapshot = [];
            foreach ($a_resumen as $id_ubi => $rowsAny) {
                if (isset($rowsAny[$any])) {
                    $snapshot[$id_ubi] = $rowsAny[$any];
                }
            }
            $snapshot = $this->aplicarSuperavitPadreHijo($snapshot);
            foreach ($snapshot as $id_ubi => $row) {
                $a_resumen[$id_ubi][$any] = $row;
            }
        }

        return $a_resumen;
    }

    /**
     * @param array<int, array{
     *   0: array{nom: string, detalles: list<array<string, mixed>>, gasto: float|int},
     *   1: array<string, float|int|string>,
     *   2: array<string, float|int|string>,
     *   _id_ubi_padre: int,
     *   _id_ubi_hijo: int
     * }> $a_resumen
     * @return array<int|string, mixed>
     */
    private function calcularTotales(array $a_resumen): array
    {
        $tot = [
            0 => ['gasto' => 0.0],
            1 => $this->inicialSeccion(),
            2 => $this->inicialSeccion(),
        ];
        foreach ([1, 2] as $s) {
            foreach (['dias', 'asist_prev', 'asist', 'in_prev_acu', 'in_acu', 'gasto', 'aportacion', 'superavit'] as $k) {
                $sum = 0.0;
                foreach ($a_resumen as $row) {
                    $v = $row[$s][$k];
                    if (is_numeric($v)) {
                        $sum += (float) $v;
                    }
                }
                $tot[$s][$k] = $sum;
            }
        }
        foreach ($a_resumen as $row) {
            $tot[0]['gasto'] += (float) $row[0]['gasto'];
        }
        foreach (['dias', 'asist_prev', 'asist', 'in_prev_acu', 'in_acu', 'gasto'] as $k) {
            $parte1 = (float) $tot[1][$k];
            $parte2 = (float) $tot[2][$k];
            $total = $parte1 + $parte2;
            $tot[1][$k . '%'] = $this->pct($total, $parte1);
            $tot[2][$k . '%'] = $this->pct($total, $parte2);
        }
        return $tot;
    }
}
