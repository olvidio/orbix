<?php

/**
 * Funcion global que construye el periodo (Slice 8 - revision). Vive fuera de
 * cualquier `namespace` para que el fragmento procedural herede el espacio
 * global y los `use` que tenia el controlador legacy sigan resolviendo.
 */

use Ramsey\Uuid\Uuid as RamseyUuid;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaTend;
use src\misas\domain\value_objects\EncargoDiaTstart;
use src\misas\domain\value_objects\PlantillaConfig;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

/**
 * @see \src\misas\application\CrearNuevoPeriodoData::build()
 */
function misas_crear_nuevo_periodo_build(array $in): array
{
    $Qid_zona = (int)($in['id_zona'] ?? 0);
    $QTipoPlantilla = (string)($in['tipo_plantilla'] ?? '');
    $Qseleccion = (int)($in['seleccion'] ?? 0);
    $Qperiodo = (string)($in['periodo'] ?? '');
    $Qempiezamin = (string)($in['empiezamin'] ?? '');
    $Qempiezamax = (string)($in['empiezamax'] ?? '');
    $Qorden = (string)($in['orden'] ?? '');
    if ($Qorden === '') {
        $Qorden = 'desc_enc';
    }

    $columns_cuadricula = [];
    $data_cuadricula = [];
    $error_txt = '';

    try {
        require __DIR__ . '/_crear_nuevo_periodo_data_fragment.php';
    } catch (\RuntimeException $e) {
        return [
            'error' => $e->getMessage(),
            'columns_cuadricula' => '[]',
            'data_cuadricula' => [],
            'id_zona' => $Qid_zona,
            'tipo_plantilla' => $QTipoPlantilla,
            'orden' => $Qorden,
            'seleccion' => $Qseleccion,
            'periodo' => $Qperiodo,
            'empieza_min' => $Qempiezamin,
            'empieza_max' => $Qempiezamax,
        ];
    }

    return [
        'error' => $error_txt,
        'columns_cuadricula' => json_encode($columns_cuadricula),
        'data_cuadricula' => $data_cuadricula,
        'id_zona' => $Qid_zona,
        'tipo_plantilla' => $QTipoPlantilla,
        'orden' => $Qorden,
        'seleccion' => $Qseleccion,
        'periodo' => $Qperiodo,
        'empieza_min' => $Qempiezamin,
        'empieza_max' => $Qempiezamax,
    ];
}
