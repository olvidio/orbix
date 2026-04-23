<?php

namespace src\casas\application\services;

use DateInterval;
use DateTime;
use src\shared\domain\value_objects\DateTimeLocal;

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
        $oInicio->sub(new DateInterval('PT' . $delta_h . 'H'));
        return $oInicio->getIsoTime();
    }

    /**
     * Dia siguiente a las delta_h h de la fecha dada.
     */
    public static function finPeriodo(string $iso_fin, int $delta_h = 10): string
    {
        $oFin = DateTimeLocal::createFromFormat('Ymd', $iso_fin);
        $oFin->add(new DateInterval('P1DT' . $delta_h . 'H'));
        return $oFin->getIsoTime();
    }

    /**
     * Días por sección (1=sv, 2=sf, 3=otros) repartidos según los
     * periodos de casa.
     *
     * @param array $aPeriodos array con iso_ini/iso_fin/sfsv.
     * @return array{1:int|float,2:int|float,3:int|float}
     */
    public static function reparto(array $aPeriodos): array
    {
        $aOcupacion = [1 => 0, 2 => 0, 3 => 0];
        foreach ($aPeriodos as $row) {
            $oInicio = DateTime::createFromFormat('Ymd', $row['iso_ini']);
            $oFin = DateTime::createFromFormat('Ymd', $row['iso_fin']);
            $num_dias = $oInicio->diff($oFin)->format('%a');
            $sfsv = (int)$row['sfsv'];
            if (!isset($aOcupacion[$sfsv])) {
                $aOcupacion[$sfsv] = 0;
            }
            $aOcupacion[$sfsv] .= $num_dias;
        }
        return $aOcupacion;
    }

    /**
     * Dias que una actividad ocupa dentro de cada sección (sv/sf)
     * del periodo total de una casa.
     *
     * @return array{1:float,2:float,avisos:string[]}
     */
    public static function diasOcupacion(array $aPeriodos, $oActividad, $oIniTot, $oFinTot): array
    {
        $avisos = [];
        $oF_ini = clone $oActividad->getF_ini();
        $oF_fin = clone $oActividad->getF_fin();
        $h_ini = $oActividad->getH_ini();
        $h_fin = $oActividad->getH_fin();
        $nom_activ = (string)$oActividad->getNom_activ();

        $num_dias = $oActividad->getDuracionReal();

        $oIniTot = clone $oIniTot;
        $oFinTot = clone $oFinTot;
        $oIniTot->setTime(0, 0, 0);
        $oFinTot->setTime(23, 59, 59);

        if (empty($h_ini)) {
            [$ini_h, $ini_m, $ini_s] = [21, 0, 0];
        } else {
            [$ini_h, $ini_m, $ini_s] = array_pad(explode(':', $h_ini), 3, 0);
        }
        if (empty($h_fin)) {
            [$fin_h, $fin_m, $fin_s] = [21, 0, 0];
        } else {
            [$fin_h, $fin_m, $fin_s] = array_pad(explode(':', $h_fin), 3, 0);
        }
        $oF_ini->setTime((int)$ini_h, (int)$ini_m, (int)$ini_s);
        $oF_fin->setTime((int)$fin_h, (int)$fin_m, (int)$fin_s);

        if ($oF_ini < $oIniTot) {
            $isoActivIni = $oIniTot->format('YmdHis');
            $oLocal = clone $oIniTot;
            $num_dias = $oLocal->duracion($oF_fin);
        } else {
            $isoActivIni = $oF_ini->format('YmdHis');
        }
        if ($oF_fin > $oFinTot) {
            $isoActivFin = $oFinTot->format('YmdHis');
            $oLocal = clone $oF_fin;
            $num_dias = $oLocal->duracion($oFinTot);
        } else {
            $isoActivFin = $oF_fin->format('YmdHis');
        }

        $p = 0;
        $aOcupacion = [1 => 0, 2 => 0];
        foreach ($aPeriodos as $row) {
            $iniPeriodo = self::inicioPeriodo($row['iso_ini']);
            $finPeriodo = self::finPeriodo($row['iso_fin']);
            if ($isoActivIni <= $finPeriodo && $isoActivIni >= $iniPeriodo) {
                if ($isoActivFin >= $iniPeriodo && $isoActivFin <= $finPeriodo) {
                    $sfsv = (int)$aPeriodos[$p]['sfsv'];
                    $aOcupacion[$sfsv] = $num_dias;
                    break;
                } elseif ($isoActivFin > $finPeriodo) {
                    if (isset($aPeriodos[$p + 1])) {
                        $iniPeriodoNext = self::inicioPeriodo($aPeriodos[$p + 1]['iso_ini']);
                        $finPeriodoNext = self::finPeriodo($aPeriodos[$p + 1]['iso_fin']);
                        if ($isoActivFin > $finPeriodoNext && isset($aPeriodos[$p + 2])) {
                            $avisos[] = sprintf((string)_("OJO: %s ocupa más de 2 periodos. Lo calcula bien"), $nom_activ);
                            $finPeriodoNext = self::finPeriodo($aPeriodos[$p + 2]['iso_fin']);
                            if ($isoActivFin > $finPeriodoNext) {
                                $avisos[] = sprintf((string)_("OJO: %s ocupa más de 3 periodos. No calcula bien"), $nom_activ);
                                break;
                            }
                            $finPeriodoLimit = self::finPeriodo($aPeriodos[$p + 1]['iso_fin'], 0);
                            $oFinPer = new DateTimeLocal($finPeriodoLimit);
                            $num_dias = $oF_ini->duracion($oFinPer);
                            $sfsv = (int)$aPeriodos[$p + 1]['sfsv'];
                            $aOcupacion[$sfsv] = $num_dias;
                            $iniPeriodoNextLimit = self::inicioPeriodo($aPeriodos[$p + 2]['iso_ini'], 0);
                            $oIniPer = new DateTimeLocal($iniPeriodoNextLimit);
                            $num_dias = $oIniPer->duracion($oF_fin);
                            $sfsv = (int)$aPeriodos[$p + 2]['sfsv'];
                            $aOcupacion[$sfsv] = $num_dias;
                        }
                        $finPeriodoLimit = self::finPeriodo($aPeriodos[$p]['iso_fin'], 0);
                        $oFinPer = new DateTimeLocal($finPeriodoLimit);
                        $num_dias = $oF_ini->duracion($oFinPer);
                        $sfsv = (int)$aPeriodos[$p]['sfsv'];
                        $aOcupacion[$sfsv] = $num_dias;
                        $iniPeriodoNextLimit = self::inicioPeriodo($aPeriodos[$p + 1]['iso_ini'], 0);
                        $oIniPer = new DateTimeLocal($iniPeriodoNextLimit);
                        $num_dias = $oIniPer->duracion($oF_fin);
                        $sfsv = (int)$aPeriodos[$p + 1]['sfsv'];
                        $aOcupacion[$sfsv] = $num_dias;
                    }
                    break;
                }
            } else {
                $p++;
            }
        }
        $aOcupacion['avisos'] = $avisos;
        return $aOcupacion;
    }
}
