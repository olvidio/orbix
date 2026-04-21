<?php

/**
 * Funcion global que construye la cuadricula (Slice 6b). Debe vivir fuera de
 * cualquier `namespace` para que el fragmento procedural herede el espacio
 * global y los `use` del controlador original sigan resolviendo igual.
 */

use core\ConfigGlobal;
use src\actividadcargos\domain\contracts\ActividadCargoRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\EncargoConstants;
use src\misas\application\services\InicialesSacdService;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\misas\domain\value_objects\EncargoDiaStatus;
use src\misas\domain\value_objects\PlantillaConfig;
use src\shared\domain\value_objects\DateTimeLocal;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use src\usuarios\domain\value_objects\TipoPreferencia;
use src\usuarios\domain\value_objects\ValorPreferencia;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use web\TiposActividades;

/**
 * @see \src\misas\application\CuadriculaZonaGridData::build()
 */
function misas_cuadricula_zona_grid_build(array $in): array
{
    $preference_warning = '';

    $Qid_zona = (int)($in['id_zona'] ?? 0);
    $QTipoPlantilla = (string)($in['tipo_plantilla'] ?? '');
    $Qperiodo = (string)($in['periodo'] ?? '');
    $Qorden = (string)($in['orden'] ?? '');
    $Qempiezamin = (string)($in['empiezamin'] ?? '');
    $Qempiezamax = (string)($in['empiezamax'] ?? '');
    $Qfila = (int)($in['fila'] ?? 0);
    $Qcolumna = (int)($in['columna'] ?? 0);
    $Qseleccion = (int)($in['seleccion'] ?? 0);

    // Defaults so the return below never references undefined vars if the
    // fragment exits early or the file fails to load (include would warn).
    $columns_cuadricula = '[]';
    $data_cuadricula = [];

    try {
        require __DIR__ . '/_cuadricula_zona_grid_fragment.php';
    } catch (\RuntimeException $e) {
        return [
            'error' => $e->getMessage(),
            'preference_warning' => $preference_warning,
            'columns_cuadricula' => '[]',
            'data_cuadricula' => [],
            'id_zona' => $Qid_zona,
            'tipo_plantilla' => $QTipoPlantilla,
            'orden' => $Qorden,
            'seleccion' => $Qseleccion,
            'periodo' => $Qperiodo,
            'empieza_min' => $Qempiezamin,
            'empieza_max' => $Qempiezamax,
            'fila' => $Qfila,
            'columna' => $Qcolumna,
        ];
    }

    return [
        'error' => '',
        'preference_warning' => $preference_warning,
        'columns_cuadricula' => $columns_cuadricula,
        'data_cuadricula' => $data_cuadricula,
        'id_zona' => $Qid_zona,
        'tipo_plantilla' => $QTipoPlantilla,
        'orden' => $Qorden,
        'seleccion' => $Qseleccion,
        'periodo' => $Qperiodo,
        'empieza_min' => $Qempiezamin,
        'empieza_max' => $Qempiezamax,
        'fila' => $Qfila,
        'columna' => $Qcolumna,
    ];
}
