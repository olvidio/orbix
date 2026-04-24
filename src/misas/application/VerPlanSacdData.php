<?php

namespace src\misas\application;

use src\shared\config\ConfigGlobal;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\application\support\PeriodoDateRange;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Datos para la vista `ver_plan_sacd.phtml`: plan de misas de un
 * sacerdote en un rango de fechas.
 */
class VerPlanSacdData
{
    /**
     * @return array{rows: array<int, array{dia: string, encargo: string, observ: string}>}
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

        [$Qempiezamin_rep, $Qempiezamax_rep] = PeriodoDateRange::resolve($periodo, $empiezamin, $empiezamax);

        $a_dias_semana_breve = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];

        $oInicio = new DateTimeLocal($Qempiezamin_rep);
        $oFin = new DateTimeLocal($Qempiezamax_rep);
        $interval = new \DateInterval('P1D');
        $date_range = new \DatePeriod($oInicio, $interval, $oFin);

        $EncargoDiaRepository = $container->get(EncargoDiaRepositoryInterface::class);
        $EncargoRepository = $container->get(EncargoRepositoryInterface::class);

        $rows = [];
        foreach ($date_range as $date) {
            $dia_week = (int)$date->format('N');
            $dia = $a_dias_semana_breve[$dia_week] . ' ' . $date->format('j') . '.' . $date->format('n');

            $id_dia = $date->format('Y-m-d');
            $aWhere = [
                'id_nom' => $Qid_sacd,
                'tstart' => "'$id_dia 00:00:00', '$id_dia 23:59:59'",
                '_ordre' => 'tstart',
            ];
            $aOperador = ['tstart' => 'BETWEEN'];
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

            if (count($cEncargosDia) === 0) {
                $rows[] = ['dia' => $dia, 'encargo' => '', 'observ' => ''];
                continue;
            }

            foreach ($cEncargosDia as $oEncargoDia) {
                $status = $oEncargoDia->getStatus();
                $visible = $jefe_zona
                    || $status === EncargoDiaStatus::STATUS_COMUNICADO_SACD
                    || $status === EncargoDiaStatus::STATUS_COMUNICADO_CTR;
                if (!$visible) {
                    continue;
                }

                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                $hora_fin = $oEncargoDia->getTend()->format('H:i');
                if ($hora_ini === '00:00') {
                    $hora_ini = '';
                }
                if ($hora_fin === '00:00') {
                    $hora_fin = '';
                }
                $dia_y_hora = $dia;
                if ($hora_ini !== '') {
                    $dia_y_hora .= ' ' . $hora_ini;
                }
                if ($hora_fin !== '') {
                    $dia_y_hora .= '-' . $hora_fin;
                }

                $oEncargo = $EncargoRepository->findById($oEncargoDia->getId_enc());
                $rows[] = [
                    'dia' => $dia_y_hora,
                    'encargo' => $oEncargo !== null ? (string)$oEncargo->getDesc_enc() : '',
                    'observ' => (string)$oEncargoDia->getObserv(),
                ];
            }
        }

        return ['rows' => $rows];
    }
}
