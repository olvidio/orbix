<?php

namespace src\misas\application;

use src\shared\config\ConfigGlobal;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\application\support\PeriodoDateRange;
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
 * Datos para la vista `ver_plan_ctr.phtml`: cuadricula del plan de misas
 * por centro (filas: encargos, columnas: días).
 */
class VerPlanCtrData
{
    /**
     * @return array{
     *     columns: array<int, array{letra: string, num_dia: string, num_mes: string, id_dia: string}>,
     *     rows: array<int, array{desc_enc: string, cells: array<int, string>}>,
     *     legend: array<int, array{iniciales: string, nombre: string}>
     * }
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

        [$Qempiezamin_rep, $Qempiezamax_rep] = PeriodoDateRange::resolve($periodo, $empiezamin, $empiezamax);

        $a_dias_semana_breve = [1 => 'L', 2 => 'M', 3 => 'X', 4 => 'J', 5 => 'V', 6 => 'S', 7 => 'D'];

        $oInicio = new DateTimeLocal($Qempiezamin_rep);
        $oFin = new DateTimeLocal($Qempiezamax_rep);
        $interval = new \DateInterval('P1D');
        $date_range = new \DatePeriod($oInicio, $interval, $oFin, 0);

        $dates = iterator_to_array($date_range);

        $columns = [];
        foreach ($dates as $date) {
            $dia_week = (int)$date->format('N');
            $columns[] = [
                'letra' => $a_dias_semana_breve[$dia_week],
                'num_dia' => $date->format('j'),
                'num_mes' => $date->format('n'),
                'id_dia' => $date->format('Y-m-d'),
            ];
        }

        $EncargoCtrRepository = $container->get(EncargoCtrRepositoryInterface::class);
        $cEncargosCtr = $EncargoCtrRepository->getEncargosCentro($id_ubi);
        $EncargoRepository = $container->get(EncargoRepositoryInterface::class);
        $EncargoDiaRepository = $container->get(EncargoDiaRepositoryInterface::class);
        $InicialesSacdRepository = $container->get(InicialesSacdRepositoryInterface::class);
        $PersonaSacdRepository = $container->get(PersonaSacdRepositoryInterface::class);

        $rows = [];
        $lista_sacd = [];
        $nombre_sacd = [];

        foreach ($cEncargosCtr as $oEncargoCtr) {
            $id_enc_row = $oEncargoCtr->getId_enc();
            $oEncargo = $EncargoRepository->findById($id_enc_row);
            if ($oEncargo === null) {
                continue;
            }
            $cells = [];
            foreach ($dates as $date) {
                $iniciales = ' -- ';
                $status = EncargoDiaStatus::STATUS_COMUNICADO_CTR;

                $id_dia = $date->format('Y-m-d');
                $aWhere = [
                    'id_enc' => $id_enc_row,
                    'tstart' => "'$id_dia 00:00:00', '$id_dia 23:59:59'",
                    '_ordre' => 'tstart',
                ];
                $aOperador = ['tstart' => 'BETWEEN'];
                $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);
                foreach ($cEncargosDia as $oEncargoDia) {
                    $id_nom = $oEncargoDia->getId_nom();
                    $status = $oEncargoDia->getStatus();
                    $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                    if ($hora_ini === '00:00') {
                        $hora_ini = '';
                    }
                    $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
                    $iniciales = $InicialesSacd !== null ? $InicialesSacd->getIniciales() : ' -- ';
                    $lista_sacd[$id_nom] = $iniciales;
                    $PersonaSacd = $PersonaSacdRepository->findById($id_nom);
                    $nombre_sacd[$id_nom] = $PersonaSacd !== null ? $PersonaSacd->getNombreApellidos() : '';
                    $iniciales .= ' ' . $hora_ini;
                    $iniciales .= empty($oEncargoDia->getObserv()) ? '' : '*';
                }

                $visible = $jefe_zona
                    || (($role === 'ctr') && ($status === EncargoDiaStatus::STATUS_COMUNICADO_CTR))
                    || (($role === 'sacd')
                        && (($status === EncargoDiaStatus::STATUS_COMUNICADO_SACD)
                            || ($status === EncargoDiaStatus::STATUS_COMUNICADO_CTR)));

                $cells[] = $visible ? $iniciales : ' -- ';
            }

            $rows[] = [
                'desc_enc' => (string)$oEncargo->getDesc_enc(),
                'cells' => $cells,
            ];
        }

        asort($lista_sacd);
        $legend = [];
        foreach ($lista_sacd as $id => $inic) {
            $legend[] = [
                'iniciales' => (string)$inic,
                'nombre' => (string)($nombre_sacd[$id] ?? ''),
            ];
        }

        return [
            'columns' => $columns,
            'rows' => $rows,
            'legend' => $legend,
        ];
    }
}
