<?php

namespace src\ubiscamas\infrastructure\ui\http\controllers;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;

class ListaHabitacionesAjax
{
    private ActividadAllRepositoryInterface $actividadAllRepository;
    private AsistenteActividadService $asistenteActividadService;
    private HabitacionDlRepositoryInterface $habitacionRepository;
    private CamaDlRepositoryInterface $camaRepository;

    public function __construct(
        ActividadAllRepositoryInterface $actividadAllRepository,
        AsistenteActividadService $asistenteActividadService,
        HabitacionDlRepositoryInterface $habitacionRepository,
        CamaDlRepositoryInterface $camaRepository
    ) {
        $this->actividadAllRepository = $actividadAllRepository;
        $this->asistenteActividadService = $asistenteActividadService;
        $this->habitacionRepository = $habitacionRepository;
        $this->camaRepository = $camaRepository;
    }

    public function __invoke(int $id_activ): array
    {
        // 1. Get Ubi ID from Activity
        $oActividad = $this->actividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            return ['error' => 'Actividad not found'];
        }
        $id_ubi = $oActividad->getId_ubi();
        
        if (empty($id_ubi)) {
            return ['error' => 'No Ubi assigned to activity'];
        }

        // 2. Fetch Rooms and Beds
        $cHabitaciones = $this->habitacionRepository->getHabitaciones(['id_ubi' => $id_ubi, '_ordre' => 'orden']);
        
        $aIdsHabitacion = array_map(fn($h) => "'" . $h->getIdHabitacionVo()->value() . "'", $cHabitaciones);
        if (empty($aIdsHabitacion)) {
            $cCamas = [];
        } else {
            $cCamas = $this->camaRepository->getCamas(
                ['id_habitacion' => implode(',', $aIdsHabitacion)],
                ['id_habitacion' => 'IN']
            );
        }
        
        // Structure beds by room
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

        // 3. Fetch Assistants
        $cAsistentes = $this->asistenteActividadService->getAsistentesDeActividad($id_activ);

        // 4. Match Assistants to Beds
        $asistentesSinCama = [];
        $camasConAsistentes = [];

        foreach ($cAsistentes as $apellidos => $oAsistente) {
            $camaId = $oAsistente->getCamaVo()?->value();
            if (!empty($camaId)) {
                $camasConAsistentes[$camaId] = $oAsistente;
            } else {
                $asistentesSinCama[$apellidos] = $oAsistente;
            }
        }

        return [
            'success' => true,
            'habitaciones_con_camas' => $camasPorHabitacion,
            'camas_con_asistentes' => $camasConAsistentes,
            'asistentes_sin_cama' => $asistentesSinCama,
            'id_activ' => $id_activ,
            'id_ubi' => $id_ubi,
        ];
    }
}
