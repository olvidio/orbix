<?php

namespace src\misas\application;

use src\shared\config\ConfigGlobal;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\application\support\EncargoDiaTimeHelper;
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

    public function __construct(
        private readonly UsuarioRepositoryInterface $usuarioRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly ZonaRepositoryInterface $zonaRepository,
        private readonly EncargoCtrRepositoryInterface $encargoCtrRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
        private readonly EncargoDiaRepositoryInterface $encargoDiaRepository,
        private readonly InicialesSacdRepositoryInterface $inicialesSacdRepository,
        private readonly PersonaSacdRepositoryInterface $personaSacdRepository,
    ) {
    }
    /**
     * @return array{
     *     columns: array<int, array{letra: string, num_dia: string, num_mes: string, id_dia: string}>,
     *     rows: array<int, array{desc_enc: string, cells: array<int, string>}>,
     *     legend: array<int, array{iniciales: string, nombre: string}>
     * }
     */
    public function getData(
        int $id_ubi,
        string $periodo,
        string $empiezamin,
        string $empiezamax,
    ): array {
        $oMiUsuario = $this->usuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        if ($oMiUsuario === null) {
            return [
                'columns' => [],
                'rows' => [],
                'legend' => [],
            ];
        }

        $id_sacd = $oMiUsuario->getCsvIdPauVo()?->value();
        $id_role = $oMiUsuario->getId_role();
        $aRoles = $this->roleRepository->getArrayRoles();
        $role = '';
        $jefe_zona = false;

        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p-sacd')) {
            $role = 'sacd';
            $cZonas = $this->zonaRepository->getZonas(['id_nom' => $id_sacd]);
            $jefe_zona = count($cZonas) > 0;
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
        $cEncargosCtr = $this->encargoCtrRepository->getEncargosCentro($id_ubi);
                
        $rows = [];
        $lista_sacd = [];
        $nombre_sacd = [];

        foreach ($cEncargosCtr as $oEncargoCtr) {
            $id_enc_row = $oEncargoCtr->getId_enc();
            if ($id_enc_row === null) {
                continue;
            }
            $oEncargo = $this->encargoRepository->findById($id_enc_row);
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
                $cEncargosDia = $this->encargoDiaRepository->getEncargoDias($aWhere, $aOperador);
                foreach ($cEncargosDia as $oEncargoDia) {
                    $id_nom = $oEncargoDia->getId_nom();
                    if ($id_nom === null) {
                        continue;
                    }
                    $status = $oEncargoDia->getStatus();
                    $hora_ini = EncargoDiaTimeHelper::format($oEncargoDia->getTstart(), 'H:i');
                    if ($hora_ini === '00:00') {
                        $hora_ini = '';
                    }
                    $InicialesSacd = $this->inicialesSacdRepository->findById($id_nom);
                    $iniciales = $InicialesSacd !== null ? $InicialesSacd->getIniciales() : ' -- ';
                    $lista_sacd[$id_nom] = $iniciales;
                    $PersonaSacd = $this->personaSacdRepository->findById($id_nom);
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
