<?php

/**
 * Funcion global que importa una plantilla a otra. Vive fuera de cualquier
 * `namespace` para que el fragmento procedural herede el espacio global y los
 * `use` que tenia el controlador legacy sigan resolviendo.
 */

use Ramsey\Uuid\Uuid as RamseyUuid;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use src\misas\domain\value_objects\EncargoDiaTend;
use src\misas\domain\value_objects\EncargoDiaTstart;
use src\misas\domain\value_objects\PlantillaConfig;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * @see \src\misas\application\ImportarPlantillaData::build()
 */
function misas_importar_plantilla_build(array $in): array
{
    $Qid_zona = (int)($in['id_zona'] ?? 0);
    $QTipoPlantillaOrigen = (string)($in['tipo_plantilla_origen'] ?? '');
    $QTipoPlantillaDestino = (string)($in['tipo_plantilla_destino'] ?? '');

    $error_txt = '';

    try {
        require __DIR__ . '/_importar_plantilla_data_fragment.php';
    } catch (\RuntimeException $e) {
        return [
            'error' => $e->getMessage(),
            'success' => false,
        ];
    }

    return [
        'error' => $error_txt,
        'success' => $error_txt === '',
    ];
}
