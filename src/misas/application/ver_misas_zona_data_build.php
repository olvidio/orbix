<?php

/**
 * Funcion global que construye la cuadricula de "ver misas zona". Vive fuera
 * de cualquier `namespace` para que el fragmento procedural herede el espacio
 * global y los `use` que tenia el controlador legacy sigan resolviendo.
 */

use src\encargossacd\domain\EncargoConstants;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * @see \src\misas\application\VerMisasZonaData::build()
 */
function misas_ver_misas_zona_build(array $in): array
{
    $Qid_zona = (int)($in['id_zona'] ?? 0);
    $QEmpiezaMin = (string)($in['empiezamin'] ?? '');
    $QEmpiezaMax = (string)($in['empiezamax'] ?? '');
    $Qseleccion = (int)($in['seleccion'] ?? 0);

    $columns_cuadricula = [];
    $data_cuadricula = [];

    try {
        require __DIR__ . '/_ver_misas_zona_data_fragment.php';
    } catch (\RuntimeException $e) {
        return [
            'error' => $e->getMessage(),
            'columns_cuadricula' => '[]',
            'data_cuadricula' => [],
            'id_zona' => $Qid_zona,
            'seleccion' => $Qseleccion,
            'empieza_min' => $QEmpiezaMin,
            'empieza_max' => $QEmpiezaMax,
        ];
    }

    return [
        'error' => '',
        'columns_cuadricula' => json_encode($columns_cuadricula, JSON_UNESCAPED_UNICODE),
        'data_cuadricula' => $data_cuadricula,
        'id_zona' => $Qid_zona,
        'seleccion' => $Qseleccion,
        'empieza_min' => $QEmpiezaMin,
        'empieza_max' => $QEmpiezaMax,
    ];
}
