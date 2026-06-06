<?php

namespace src\casas\application\services;

use DateInterval;
use DateTime;
use src\actividades\domain\entity\ActividadAll;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullTimeLocal;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Funciones auxiliares compartidas por `CasasResumenData` y
 * `CalendarioUbiResumenData`. Portadas de
 * `apps/casas/controller/casas_resumen_ajax.php`.
 */
final class CasasResumenOcupacion
{
    /**
     * Dia anterior a las (24 - delta_h) h de la fecha dada.
     */
    public static function inicioPeriodo(string $iso_ini, int $delta_h = 4): string
    {
        $oInicio = DateTimeLocal::createFromFormat('Ymd', $iso_ini);
        if ($oInicio === false) {
            return '';
        }
        $oInicio->sub(new DateInterval('PT' . $delta_h . 'H'));

        return $oInicio->getIsoTime();
    }

    /**
     * Dia siguiente a las delta_h h de la fecha dada.
     */
    public static function finPeriodo(string $iso_fin, int $delta_h = 10): string
    {
        $oFin = DateTimeLocal::createFromFormat('Ymd', $iso_fin);
        if ($oFin === false) {
            return '';
        }
        $oFin->add(new DateInterval('P1DT' . $delta_h . 'H'));

        return $oFin->getIsoTime();
    }

    /**
     * Días por sección (1=sv, 2=sf, 3=otros) repartidos según los
     * periodos de casa.
     *
     * @param list<array{iso_ini: string, iso_fin: string, sfsv: int|string}> $aPeriodos
     * @return array{1: float, 2: float, 3: float}
     */
    public static function reparto(array $aPeriodos): array
    {
        $aOcupacion = [1 => 0.0, 2 => 0.0, 3 => 0.0];
        foreach ($aPeriodos as $row) {
            $oInicio = DateTime::createFromFormat('Ymd', $row['iso_ini']);
            $oFin = DateTime::createFromFormat('Ymd', $row['iso_fin']);
            if ($oInicio === false || $oFin === false) {
                continue;
            }
            $num_dias = (float) $oInicio->diff($oFin)->format('%a');
            $sfsv = (int) $row['sfsv'];
            if (!isset($aOcupacion[$sfsv])) {
                $aOcupacion[$sfsv] = 0.0;
            }
            $aOcupacion[$sfsv] += $num_dias;
        }

        return [
            1 => (float) ($aOcupacion[1] ?? 0.0),
            2 => (float) ($aOcupacion[2] ?? 0.0),
            3 => (float) ($aOcupacion[3] ?? 0.0),
        ];
    }

    /**
     * Dias que una actividad ocupa dentro de cada sección (sv/sf)
     * del periodo total de una casa.
     *
     * @param list<array{iso_ini: string, iso_fin: string, sfsv: int|string}> $aPeriodos
     * @return array{1: float, 2: float, avisos: list<string>}
     */
    public static function diasOcupacion(
        array $aPeriodos,
        ActividadAll $oActividad,
        DateTimeLocal $oIniTot,
        DateTimeLocal $oFinTot,
    ): array {
        $avisos = [];
        $oF_ini_raw = $oActividad->getF_ini();
        $oF_fin_raw = $oActividad->getF_fin();
        if ($oF_ini_raw === null || $oF_fin_raw === null) {
            return [1 => 0.0, 2 => 0.0, 'avisos' => $avisos];
        }
        $oF_ini = clone $oF_ini_raw;
        $oF_fin = clone $oF_fin_raw;
        $h_ini = $oActividad->getH_ini();
        $h_fin = $oActividad->getH_fin();
        $nom_activ = (string) $oActividad->getNom_activ();

        $num_dias = $oActividad->getDuracionReal();

        $oIniTotLocal = clone $oIniTot;
        $oFinTotLocal = clone $oFinTot;
        $oIniTotLocal->setTime(0, 0, 0);
        $oFinTotLocal->setTime(23, 59, 59);

        $h_ini_str = self::fmtTime($h_ini);
        $h_fin_str = self::fmtTime($h_fin);
        if ($h_ini_str === '') {
            [$ini_h, $ini_m, $ini_s] = [21, 0, 0];
        } else {
            [$ini_h, $ini_m, $ini_s] = array_pad(explode(':', $h_ini_str), 3, 0);
        }
        if ($h_fin_str === '') {
            [$fin_h, $fin_m, $fin_s] = [21, 0, 0];
        } else {
            [$fin_h, $fin_m, $fin_s] = array_pad(explode(':', $h_fin_str), 3, 0);
        }
        $oF_ini->setTime((int) $ini_h, (int) $ini_m, (int) $ini_s);
        $oF_fin->setTime((int) $fin_h, (int) $fin_m, (int) $fin_s);

        if ($oF_ini < $oIniTotLocal) {
            $isoActivIni = $oIniTotLocal->format('YmdHis');
            $oLocal = clone $oIniTotLocal;
            $num_dias = $oLocal->duracion($oF_fin);
        } else {
            $isoActivIni = $oF_ini->format('YmdHis');
        }
        if ($oF_fin > $oFinTotLocal) {
            $isoActivFin = $oFinTotLocal->format('YmdHis');
            $oLocal = clone $oF_fin;
            $num_dias = $oLocal->duracion($oFinTotLocal);
        } else {
            $isoActivFin = $oF_fin->format('YmdHis');
        }

        $p = 0;
        $aOcupacion = [1 => 0.0, 2 => 0.0];
        foreach ($aPeriodos as $row) {
            $iniPeriodo = self::inicioPeriodo($row['iso_ini']);
            $finPeriodo = self::finPeriodo($row['iso_fin']);
            if ($isoActivIni <= $finPeriodo && $isoActivIni >= $iniPeriodo) {
                if ($isoActivFin >= $iniPeriodo && $isoActivFin <= $finPeriodo) {
                    $sfsv = (int) $aPeriodos[$p]['sfsv'];
                    $aOcupacion[$sfsv] = $num_dias;
                    break;
                } elseif ($isoActivFin > $finPeriodo) {
                    if (isset($aPeriodos[$p + 1])) {
                        $finPeriodoNext = self::finPeriodo($aPeriodos[$p + 1]['iso_fin']);
                        if ($isoActivFin > $finPeriodoNext && isset($aPeriodos[$p + 2])) {
                            $avisos[] = sprintf((string) _("OJO: %s ocupa más de 2 periodos. Lo calcula bien"), $nom_activ);
                            $finPeriodoNext = self::finPeriodo($aPeriodos[$p + 2]['iso_fin']);
                            if ($isoActivFin > $finPeriodoNext) {
                                $avisos[] = sprintf((string) _("OJO: %s ocupa más de 3 periodos. No calcula bien"), $nom_activ);
                                break;
                            }
                            $finPeriodoLimit = self::finPeriodo($aPeriodos[$p + 1]['iso_fin'], 0);
                            $oFinPer = new DateTimeLocal($finPeriodoLimit);
                            $num_dias = $oF_ini->duracion($oFinPer);
                            $sfsv = (int) $aPeriodos[$p + 1]['sfsv'];
                            $aOcupacion[$sfsv] = $num_dias;
                            $iniPeriodoNextLimit = self::inicioPeriodo($aPeriodos[$p + 2]['iso_ini'], 0);
                            $oIniPer = new DateTimeLocal($iniPeriodoNextLimit);
                            $num_dias = $oIniPer->duracion($oF_fin);
                            $sfsv = (int) $aPeriodos[$p + 2]['sfsv'];
                            $aOcupacion[$sfsv] = $num_dias;
                        }
                        $finPeriodoLimit = self::finPeriodo($aPeriodos[$p]['iso_fin'], 0);
                        $oFinPer = new DateTimeLocal($finPeriodoLimit);
                        $num_dias = $oF_ini->duracion($oFinPer);
                        $sfsv = (int) $aPeriodos[$p]['sfsv'];
                        $aOcupacion[$sfsv] = $num_dias;
                        $iniPeriodoNextLimit = self::inicioPeriodo($aPeriodos[$p + 1]['iso_ini'], 0);
                        $oIniPer = new DateTimeLocal($iniPeriodoNextLimit);
                        $num_dias = $oIniPer->duracion($oF_fin);
                        $sfsv = (int) $aPeriodos[$p + 1]['sfsv'];
                        $aOcupacion[$sfsv] = $num_dias;
                    }
                    break;
                }
            } else {
                $p++;
            }
        }

        return [
            1 => (float) $aOcupacion[1],
            2 => (float) $aOcupacion[2],
            'avisos' => $avisos,
        ];
    }

    private static function fmtTime(TimeLocal|NullTimeLocal|null $h): string
    {
        if ($h === null || $h instanceof NullTimeLocal) {
            return '';
        }

        return $h->toDatabaseString();
    }
}
