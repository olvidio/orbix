<?php

namespace src\misas\application;

use core\ConfigGlobal;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Tabla HTML del plan de misas de un sacerdote en un rango de fechas.
 */
class VerPlanSacdData
{
    /**
     * @return array{html: string}
     */
    public static function getData(
        string $id_sacd_key,
        string $periodo,
        string $empiezamin,
        string $empiezamax,
    ): array {
        $container = $GLOBALS['container'];

        $UsuarioRepository = $container->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_sacd_csv = $oMiUsuario->getCsvIdPauAsString();

        $ZonasRepository = $container->get(ZonaRepositoryInterface::class);
        $cZonas = $ZonasRepository->getZonas(['id_nom' => $id_sacd_csv]);
        $jefe_zona = is_array($cZonas) && count($cZonas) > 0;

        $exp_id_sacd = explode('#', $id_sacd_key);
        $Qid_sacd = $exp_id_sacd[0];

        [$Qempiezamin_rep, $Qempiezamax_rep] = self::resolveDateRange($periodo, $empiezamin, $empiezamax);

        $a_dias_semana_breve = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];

        $oInicio = new DateTimeLocal($Qempiezamin_rep);
        $oFin = new DateTimeLocal($Qempiezamax_rep);
        $interval = new \DateInterval('P1D');
        $date_range = new \DatePeriod($oInicio, $interval, $oFin);

        $EncargoDiaRepository = $container->get(EncargoDiaRepositoryInterface::class);
        $EncargoRepository = $container->get(EncargoRepositoryInterface::class);

        $html = '<table class="plan-sacd-misas">';
        $html .= '<thead><tr>';
        $html .= '<th class="cell-title" style="width:10%">' . htmlspecialchars(_('Dia'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th class="cell-title" style="width:30%">' . htmlspecialchars(_('Encargo'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th class="cell-title" style="width:30%">' . htmlspecialchars(_('Observaciones'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($date_range as $date) {
            $num_dia = $date->format('j');
            $num_mes = $date->format('n');
            $dia_week = (int)$date->format('N');
            $dia = $a_dias_semana_breve[$dia_week] . ' ' . $num_dia . '.' . $num_mes;

            $id_dia = $date->format('Y-m-d');
            $inicio_dia = $id_dia . ' 00:00:00';
            $fin_dia = $id_dia . ' 23:59:59';

            $aWhere = [
                'id_nom' => $Qid_sacd,
                'tstart' => "'$inicio_dia', '$fin_dia'",
                '_ordre' => 'tstart',
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

            if (count($cEncargosDia) === 0) {
                $html .= '<tr><td>' . htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') . '</td><td></td><td></td></tr>';
                continue;
            }

            foreach ($cEncargosDia as $oEncargoDia) {
                $id_enc = $oEncargoDia->getId_enc();
                $status = $oEncargoDia->getStatus();
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                $hora_fin = $oEncargoDia->getTend()->format('H:i');
                if ($hora_ini === '00:00') {
                    $hora_ini = '';
                }
                if ($hora_fin === '00:00') {
                    $hora_fin = '';
                }
                $observ = $oEncargoDia->getObserv();
                $dia_y_hora = $dia;
                if ($hora_ini !== '') {
                    $dia_y_hora .= ' ' . $hora_ini;
                }
                if ($hora_fin !== '') {
                    $dia_y_hora .= '-' . $hora_fin;
                }

                $visible = $jefe_zona
                    || $status === EncargoDiaStatus::STATUS_COMUNICADO_SACD
                    || $status === EncargoDiaStatus::STATUS_COMUNICADO_CTR;
                if (!$visible) {
                    continue;
                }

                $oEncargo = $EncargoRepository->findById($id_enc);
                $desc_enc = $oEncargo !== null ? $oEncargo->getDesc_enc() : '';

                $html .= '<tr><td>' . htmlspecialchars($dia_y_hora, ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td>' . htmlspecialchars((string)$desc_enc, ENT_QUOTES, 'UTF-8') . '</td>';
                $html .= '<td>' . htmlspecialchars((string)$observ, ENT_QUOTES, 'UTF-8') . '</td></tr>';
            }
        }

        $html .= '</tbody></table>';

        return ['html' => $html];
    }

    /**
     * @return array{0: string, 1: string} [Y-m-d, Y-m-d]
     */
    private static function resolveDateRange(string $periodo, string $empiezamin, string $empiezamax): array
    {
        switch ($periodo) {
            case 'esta_semana':
                $dia_week = (int)date('N');
                $dia_week--;
                if ($dia_week === -1) {
                    $dia_week = 6;
                }
                $empiezamin_dt = new DateTimeLocal(date('Y-m-d'));
                $intervalo = 'P' . $dia_week . 'D';
                $di = new \DateInterval($intervalo);
                $di->invert = 1;
                $empiezamin_dt->add($di);
                $Qempiezamin_rep = $empiezamin_dt->format('Y-m-d');
                $empiezamax_dt = $empiezamin_dt;
                $empiezamax_dt->add(new \DateInterval('P7D'));
                $Qempiezamax_rep = $empiezamax_dt->format('Y-m-d');
                break;
            case 'proxima_semana':
                $dia_week = (int)date('N');
                $empiezamin_dt = new DateTimeLocal(date('Y-m-d'));
                $empiezamin_dt->add(new \DateInterval('P' . (8 - $dia_week) . 'D'));
                $Qempiezamin_rep = $empiezamin_dt->format('Y-m-d');
                $empiezamax_dt = $empiezamin_dt;
                $empiezamax_dt->add(new \DateInterval('P7D'));
                $Qempiezamax_rep = $empiezamax_dt->format('Y-m-d');
                break;
            case 'este_mes':
                $este_mes = date('m');
                $anyo = date('Y');
                $empiezamin_dt = new DateTimeLocal(date($anyo . '-' . $este_mes . '-01'));
                $Qempiezamin_rep = $empiezamin_dt->format('Y-m-d');
                $siguiente_mes = (int)$este_mes + 1;
                if ($siguiente_mes === 13) {
                    $siguiente_mes = 1;
                    $anyo++;
                }
                $empiezamax_dt = new DateTimeLocal(date($anyo . '-' . $siguiente_mes . '-01'));
                $Qempiezamax_rep = $empiezamax_dt->format('Y-m-d');
                break;
            case 'proximo_mes':
                $proximo_mes = (int)date('m') + 1;
                $anyo = date('Y');
                if ($proximo_mes === 13) {
                    $proximo_mes = 1;
                    $anyo++;
                }
                $empiezamin_dt = new DateTimeLocal(date($anyo . '-' . $proximo_mes . '-01'));
                $Qempiezamin_rep = $empiezamin_dt->format('Y-m-d');
                $siguiente_mes = $proximo_mes + 1;
                if ($siguiente_mes === 13) {
                    $siguiente_mes = 1;
                    $anyo++;
                }
                $empiezamax_dt = new DateTimeLocal(date($anyo . '-' . $siguiente_mes . '-01'));
                $Qempiezamax_rep = $empiezamax_dt->format('Y-m-d');
                break;
            default:
                $Qempiezamin_rep = str_replace('/', '-', $empiezamin);
                $Qempiezamax_rep = str_replace('/', '-', $empiezamax);
                break;
        }

        return [$Qempiezamin_rep, $Qempiezamax_rep];
    }
}
