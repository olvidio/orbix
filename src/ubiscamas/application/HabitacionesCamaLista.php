<?php

namespace src\ubiscamas\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use src\ubiscamas\domain\value_objects\TipoLavabo;
use function src\shared\domain\helpers\is_true;

class HabitacionesCamaLista
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private AsistenteActividadService $asistenteActividadService,
        private HabitacionDlRepositoryInterface $habitacionRepository,
        private CamaDlRepositoryInterface $camaRepository,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(int $id_activ): array
    {
        $oActividad = $this->actividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            return ['error' => 'Actividad not found'];
        }
        $id_ubi = $oActividad->getId_ubi();
        $solo_vip = ($oActividad->getDesc_activ() === 'camasVIP');

        if (empty($id_ubi)) {
            return ['error' => 'No Ubi assigned to activity'];
        }

        $cHabitaciones = $this->habitacionRepository->getHabitaciones(['id_ubi' => $id_ubi, '_ordre' => 'orden, planta']);

        $aIdsHabitacion = array_map(fn($h) => "'" . $h->getIdHabitacionVo()->value() . "'", $cHabitaciones);
        if ($aIdsHabitacion === []) {
            $cCamas = [];
        } else {
            $cCamas = $this->camaRepository->getCamas(
                ['id_habitacion' => implode(',', $aIdsHabitacion)],
                ['id_habitacion' => 'IN']
            );
        }

        $camasPorHabitacion = [];
        foreach ($cHabitaciones as $oHabitacion) {
            $id_hab = $oHabitacion->getIdHabitacionVo()->value();
            $camasPorHabitacion[$id_hab] = [
                'habitacion' => $oHabitacion,
                'camas' => []
            ];
        }

        foreach ($cCamas as $oCama) {
            $id_hab = $oCama->getIdHabitacionVo()->value();
            if (isset($camasPorHabitacion[$id_hab])) {
                $camasPorHabitacion[$id_hab]['camas'][] = $oCama;
            }
        }

        $cAsistentes = $this->asistenteActividadService->getAsistentesDeActividad($id_activ);

        $asistentesSinCama = [];
        $camasConAsistentes = [];

        foreach ($cAsistentes as $apellidos => $oAsistente) {
            $camaId = $oAsistente->getCamaVo()?->value();
            if (!empty($camaId)) {
                $camasConAsistentes[$camaId] = [
                    'id_nom' => $oAsistente->getId_nom(),
                    'apellidos' => $apellidos,
                ];
            } else {
                $asistentesSinCama[] = [
                    'id_nom' => $oAsistente->getId_nom(),
                    'apellidos' => $apellidos,
                ];
            }
        }

        $a_cabeceras = [
            _("nombre"),
            _("planta"),
            _("adaptada"),
            _("lavabo"),
            _("sillón"),
            _("despacho"),
            _("cama"),
            _("larga"),
            _("vip"),
            _("ocupada por"),
            _("observ"),
        ];

        $a_botones = [];
        $a_valores = [];
        $i = 0;
        $arrayTiposLavabo = TipoLavabo::getArrayTipoLavabo();
        foreach ($camasPorHabitacion as $roomData) {
            $oHabitacion = $roomData['habitacion'];
            foreach ($roomData['camas'] as $oCama) {
                if ($solo_vip && !$oCama->isVip()) {
                    continue;
                }
                $i++;
                $id_cama = $oCama->getIdCamaVo()->value();
                $id_habitacion = $oHabitacion->getIdHabitacionVo()->value();
                $tipo_lavabo = $arrayTiposLavabo[$oHabitacion->getTipoLavaboVo()?->value() ?? 0] ?? '';
                $aRow = [];
                $aRow['sel'] = "$id_habitacion#$id_cama";
                $aRow['id_cama'] = $id_cama;

                $aRow[1] = $oHabitacion->getNombre();
                $aRow[2] = $oHabitacion->getPlanta();
                $aRow[3] = $oHabitacion->isAdaptada() ? 'X' : '';
                $aRow[4] = $tipo_lavabo;
                $aRow[5] = $oHabitacion->isSillon() ? 'X' : '';
                $aRow[6] = $oHabitacion->isDespacho() ? 'X' : '';
                $aRow[7] = $oCama->getDescripcion();
                $aRow[8] = $oCama->isLarga() ? 'X' : '';
                $aRow[9] = is_true($oCama->isVip()) ? 'X' : '';

                $ocupada_por = '';
                $id_nom = '';
                if (isset($camasConAsistentes[$id_cama])) {
                    $aAsistente = $camasConAsistentes[$id_cama];
                    $ocupada_por = $aAsistente['apellidos'];
                    $id_nom = $aAsistente['id_nom'];
                }
                $aRow[10] = $ocupada_por;
                $aRow['id_nom'] = $id_nom;

                $aRow[11] = $oHabitacion->getObservacionesVo()?->value() ?? '';

                $a_valores[$i] = $aRow;
            }
        }

        $habitacionesConCamasParaJson = [];
        foreach ($camasPorHabitacion as $id_hab => $roomData) {
            $habitacionesConCamasParaJson[$id_hab] = [
                'habitacion' => $roomData['habitacion']->toArrayForDatabase(),
                'camas' => array_map(fn($oCama) => $oCama->toArrayForDatabase(), $roomData['camas']),
            ];
        }

        return [
            'success' => true,
            'id_activ' => $id_activ,
            'id_ubi' => $id_ubi,
            'solo_vip' => $solo_vip,
            'habitaciones_con_camas' => $habitacionesConCamasParaJson,
            'camas_con_asistentes' => $camasConAsistentes,
            'asistentes_sin_cama' => $asistentesSinCama,
            'a_cabeceras' => $a_cabeceras,
            'a_botones' => $a_botones,
            'a_valores' => $a_valores,
        ];
    }
}
