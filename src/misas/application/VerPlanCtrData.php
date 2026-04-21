<?php

namespace src\misas\application;

use core\ConfigGlobal;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Cuadricula HTML del plan de misas por centro (filas encargo, columnas dia).
 */
class VerPlanCtrData
{
    /**
     * @return array{html: string}
     */
    public static function getData(
        int $id_ubi,
        string $periodo,
        string $empiezamin,
        string $empiezamax,
    ): array {
        $container = $GLOBALS['container'];

        $UsuarioRepository = $container->get(UsuarioRepositoryInterface::class);
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_sacd = $oMiUsuario->getCsvIdPauVo()?->value();
        $id_role = $oMiUsuario->getId_role();

        $RoleRepository = $container->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();
        $role = '';
        $jefe_zona = false;

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
            $role = 'sacd';
            $ZonaRepository = $container->get(ZonaRepositoryInterface::class);
            $cZonas = $ZonaRepository->getZonas(['id_nom' => $id_sacd]);
            $jefe_zona = is_array($cZonas) && count($cZonas) > 0;
        }

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'Centro sv' || $aRoles[$id_role] === 'Centro sf')) {
            $role = 'ctr';
        }

        [$Qempiezamin_rep, $Qempiezamax_rep] = self::resolveDateRange($periodo, $empiezamin, $empiezamax);

        $a_dias_semana_breve = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];

        $oInicio = new DateTimeLocal($Qempiezamin_rep);
        $oFin = new DateTimeLocal($Qempiezamax_rep);
        $interval = new \DateInterval('P1D');
        $date_range = new \DatePeriod($oInicio, $interval, $oFin, 0);

        $dates = iterator_to_array($date_range);

        $EncargoCtrRepository = $container->get(EncargoCtrRepositoryInterface::class);
        $cEncargosCtr = $EncargoCtrRepository->getEncargosCentro($id_ubi);
        $EncargoRepository = $container->get(EncargoRepositoryInterface::class);
        $EncargoDiaRepository = $container->get(EncargoDiaRepositoryInterface::class);
        $InicialesSacdRepository = $container->get(InicialesSacdRepositoryInterface::class);
        $PersonaSacdRepository = $container->get(PersonaSacdRepositoryInterface::class);

        $html = '<table class="plan-ctr-misas" style="width:100%">';
        $html .= '<thead><tr>';
        $html .= '<th class="cell-title" style="width:10%">' . htmlspecialchars(_('Encargo'), ENT_QUOTES, 'UTF-8') . '</th>';
        foreach ($dates as $date) {
            $dia_week = (int)$date->format('N');
            $num_dia = $date->format('j');
            $num_mes = $date->format('n');
            $nom_dia2 = $a_dias_semana_breve[$dia_week] . '<br>' . $num_dia . '.' . $num_mes;
            $html .= '<th class="cell-title" style="width:60px">' . $nom_dia2 . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        $lista_sacd = [];
        $nombre_sacd = [];

        foreach ($cEncargosCtr as $oEncargoCtr) {
            $id_enc_row = $oEncargoCtr->getId_enc();
            $oEncargo = $EncargoRepository->findById($id_enc_row);
            if ($oEncargo === null) {
                continue;
            }
            $desc_enc = $oEncargo->getDesc_enc();

            $html .= '<tr><td>' . htmlspecialchars((string)$desc_enc, ENT_QUOTES, 'UTF-8') . '</td>';

            foreach ($dates as $date) {
                $iniciales = ' -- ';
                $status = EncargoDiaStatus::STATUS_COMUNICADO_CTR;

                $id_dia = $date->format('Y-m-d');

                $inicio_dia = $id_dia . ' 00:00:00';
                $fin_dia_ts = $id_dia . ' 23:59:59';

                $aWhere = [
                    'id_enc' => $id_enc_row,
                    'tstart' => "'$inicio_dia', '$fin_dia_ts'",
                    '_ordre' => 'tstart',
                ];
                $aOperador = [
                    'tstart' => 'BETWEEN',
                ];

                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
                foreach ($cEncargosDia as $oEncargoDia) {
                    $id_nom = $oEncargoDia->getId_nom();
                    $status = $oEncargoDia->getStatus();
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    $hora_fin = $oEncargoDia->getTend()->format('H:i');
                    if ($hora_ini === '00:00') {
                        $hora_ini = '';
                    }
                    if ($hora_fin === '00:00') {
                        $hora_fin = '';
                    }
                    $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
                    $iniciales = $InicialesSacd !== null ? $InicialesSacd->getIniciales() : ' -- ';
                    $lista_sacd[$id_nom] = $iniciales;
                    $PersonaSacd = $PersonaSacdRepository->findById($id_nom);
                    $sacd = $PersonaSacd !== null ? $PersonaSacd->getNombreApellidos() : '';
                    $nombre_sacd[$id_nom] = $sacd;
                    $iniciales .= ' ' . $hora_ini;
                    $iniciales .= empty($oEncargoDia->getObserv()) ? '' : '*';
                }

                $visible = $jefe_zona
                    || (($role === 'ctr') && ($status === EncargoDiaStatus::STATUS_COMUNICADO_CTR))
                    || (($role === 'sacd')
                        && (($status === EncargoDiaStatus::STATUS_COMUNICADO_SACD)
                            || ($status === EncargoDiaStatus::STATUS_COMUNICADO_CTR)));

                $cell = $visible ? $iniciales : ' -- ';
                $html .= '<td>' . htmlspecialchars($cell, ENT_QUOTES, 'UTF-8') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        $html .= '<table class="plan-ctr-leyenda"><tr><td>';
        asort($lista_sacd);
        $parts = [];
        foreach ($lista_sacd as $id => $inic) {
            $nom = $nombre_sacd[$id] ?? '';
            $parts[] = htmlspecialchars((string)$inic, ENT_QUOTES, 'UTF-8') . ': '
                . htmlspecialchars((string)$nom, ENT_QUOTES, 'UTF-8');
        }
        $html .= implode(' &nbsp; ', $parts);
        $html .= '</td></tr></table>';

        return ['html' => $html];
    }

    /**
     * @return array{0: string, 1: string}
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
                $oInicio = DateTimeLocal::createFromLocal($empiezamin);
                $oFin = DateTimeLocal::createFromLocal($empiezamax);
                if ($oInicio === false || $oFin === false) {
                    $Qempiezamin_rep = str_replace('/', '-', $empiezamin);
                    $Qempiezamax_rep = str_replace('/', '-', $empiezamax);
                } else {
                    $Qempiezamin_rep = $oInicio->format('Y-m-d');
                    $Qempiezamax_rep = $oFin->format('Y-m-d');
                }
                break;
        }

        return [$Qempiezamin_rep, $Qempiezamax_rep];
    }
}
