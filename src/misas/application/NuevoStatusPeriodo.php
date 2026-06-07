<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\shared\domain\value_objects\DateTimeLocal;

class NuevoStatusPeriodo
{

    public function __construct(
        private readonly EncargoTipoRepositoryInterface $encargoTipoRepository,
        private readonly EncargoDiaRepositoryInterface $encargoDiaRepository,
        private readonly EncargoHorarioRepositoryInterface $encargoHorarioRepository,
        private readonly EncargoRepositoryInterface $encargoRepository,
    ) {
    }
    /**
     * Actualiza `status` de todos los `EncargoDia` de encargos 8100+ de la zona en el rango.
     *
     * @return array{error: string}
     */
    public function execute(
        int $id_zona,
        string $periodo,
        string $empiezamin,
        string $empiezamax,
        int $estado
    ): array {
        $error_txt = '';
        $menos_un_dia = new \DateInterval('P1D');
        $menos_un_dia->invert = 1;

        switch ($periodo) {
            case 'proxima_semana':
                $dia_week = date('N');
                $empiezamin_o = new DateTimeLocal(date('Y-m-d'));
                $intervalo = 'P' . (8 - $dia_week) . 'D';
                $empiezamin_o->add(new \DateInterval($intervalo));
                $Qempiezamin_rep = $empiezamin_o->format('Y-m-d');
                $empiezamax_o = clone $empiezamin_o;
                $empiezamax_o->add(new \DateInterval('P7D'));
                $empiezamax_o->add($menos_un_dia);
                $Qempiezamax_rep = $empiezamax_o->format('Y-m-d');
                break;
            case 'proximo_mes':
                $proximo_mes = (int)date('m') + 1;
                $anyo = (int)date('Y');
                if ($proximo_mes === 13) {
                    $proximo_mes = 1;
                    $anyo++;
                }
                $empiezamin_o = new DateTimeLocal(date($anyo . '-' . $proximo_mes . '-01'));
                $Qempiezamin_rep = $empiezamin_o->format('Y-m-d');
                $siguiente_mes = $proximo_mes + 1;
                if ($siguiente_mes === 13) {
                    $siguiente_mes = 1;
                    $anyo++;
                }
                $empiezamax_o = new DateTimeLocal(date($anyo . '-' . $siguiente_mes . '-01'));
                $empiezamax_o->add($menos_un_dia);
                $Qempiezamax_rep = $empiezamax_o->format('Y-m-d');
                break;
            default:
                $partes_min = explode('/', $empiezamin);
                $Qempiezamin_rep = $partes_min[2] . '-' . $partes_min[1] . '-' . $partes_min[0];
                $partes_max = explode('/', $empiezamax);
                $Qempiezamax_rep = $partes_max[2] . '-' . $partes_max[1] . '-' . $partes_max[0];
        }

        $sInicio = $Qempiezamin_rep . ' 00:00:00';
        $sFin = $Qempiezamax_rep . ' 23:59:59';
        $grupo = '8...';
        $aWhere = ['id_tipo_enc' => '^' . $grupo];
        $aOperador = ['id_tipo_enc' => '~'];
        $cEncargoTipos = $this->encargoTipoRepository->getEncargoTipos($aWhere, $aOperador);

        $a_tipo_enc = [];
        foreach ($cEncargoTipos as $oEncargoTipo) {
            if ($oEncargoTipo->getId_tipo_enc() >= 8100) {
                $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
            }
        }

        $oInicio = new DateTimeLocal($sInicio);
        $oFin = new DateTimeLocal($sFin);
        $orden = 'prioridad';

        $EncargosZona = new EncargosZona($id_zona, $oInicio, $oFin, $this->encargoHorarioRepository, $this->encargoRepository, $orden);
        $EncargosZona->setATipoEnc($a_tipo_enc);
        $cEncargosZona = $EncargosZona->getEncargos();
        foreach ($cEncargosZona as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $aWhereDia = [
                'id_enc' => $id_enc,
                'tstart' => "'$sInicio', '$sFin'",
            ];
            $aOperadorDia = ['tstart' => 'BETWEEN'];

            $cEncargosaCambiar = $this->encargoDiaRepository->getEncargoDias($aWhereDia, $aOperadorDia);
            foreach ($cEncargosaCambiar as $oEncargoaCambiar) {
                $oEncargoaCambiar->setStatus($estado);
                if ($this->encargoDiaRepository->Guardar($oEncargoaCambiar) === false) {
                    $error_txt .= $this->encargoDiaRepository->getErrorTxt();
                }
            }
        }

        return ['error' => $error_txt];
    }
}
