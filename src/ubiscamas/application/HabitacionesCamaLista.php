<?php

namespace src\ubiscamas\application;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;
use src\ubiscamas\domain\contracts\HabitacionDlRepositoryInterface;
use web\Hash;

class HabitacionesCamaLista
{
    private ActividadAllRepositoryInterface $actividadAllRepository;
    private AsistenteActividadService $asistenteActividadService;
    private HabitacionDlRepositoryInterface $habitacionRepository;
    private CamaDlRepositoryInterface $camaRepository;

    public function __construct() {
        $this->actividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $this->asistenteActividadService = $GLOBALS['container']->get(AsistenteActividadService::class);
        $this->habitacionRepository = $GLOBALS['container']->get(HabitacionDlRepositoryInterface::class);
        $this->camaRepository = $GLOBALS['container']->get(CamaDlRepositoryInterface::class);
    }

    public function __invoke(int $id_activ): array
    {
        // 1. Get Ubi ID from Activity
        $oActividad = $this->actividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            return ['error' => 'Actividad not found'];
        }
        $id_ubi = $oActividad->getId_ubi();
        $solo_vip = ($oActividad->getDesc_activ() === 'camasVIP');
        
        if (empty($id_ubi)) {
            return ['error' => 'No Ubi assigned to activity'];
        }

        // 2. Fetch Rooms and Beds
        $cHabitaciones = $this->habitacionRepository->getHabitaciones(['id_ubi' => $id_ubi, '_ordre' => 'orden, planta']);
        
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

        // 5. Prepare Table Data (a_valores)
        $a_cabeceras = [
            _("nombre"),
            _("planta"),
            _("cama"),
            _("L"),
            _("V"),
            _("ocupada por"),
        ];

        $a_botones = [];
        $a_valores = [];
        $i = 0;
        foreach ($camasPorHabitacion as $roomData) {
            $oHabitacion = $roomData['habitacion'];
            $aHabitacion = $oHabitacion->toArrayForDatabase();
            
            foreach ($roomData['camas'] as $oCama) {
                if ($solo_vip && !$oCama->isVip()) {
                    continue;
                }
                $i++;
                $aCama = $oCama->toArrayForDatabase();
                
                // Combine room and bed data
                $aRow = array_merge($aHabitacion, $aCama);
                
                $id_cama = $oCama->getIdCamaVo()->value();
                $id_habitacion = $oHabitacion->getIdHabitacionVo()->value();

                $aRow['sel'] = "$id_habitacion#$id_cama";
                
                $aRow[1] = $oHabitacion->getNombre();
                $aRow[2] = $oHabitacion->getPlanta();
                $aRow[3] = $oCama->getDescripcion();
                $aRow[4] = $oCama->isLarga() ? 'X' : '';
                $aRow[5] = $oCama->isVip() ? 'X' : '';
                
                $ocupada_por = '';
                $id_nom = '';
                if (isset($camasConAsistentes[$id_cama])) {
                    $aAsistente = $camasConAsistentes[$id_cama];
                    $ocupada_por = $aAsistente['apellidos'];
                    $id_nom = $aAsistente['id_nom'];
                }
                $aRow[6] = $ocupada_por;
                $aRow['id_nom'] = $id_nom;

                /*
                $pagina = Hash::link(ConfigGlobal::getWeb() . '/frontend/ubiscamas/controller/cama_form.php?'
                    . http_build_query(array('id_habitacion' => $id_habitacion, 'id_cama' => $id_cama))
                );
                $aRow[7] = array('ira' => $pagina, 'valor' => _('editar'));
                */

                $a_valores[$i] = $aRow;
            }
        }

        // 6. Final preparation (convert objects to arrays for frontend)
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