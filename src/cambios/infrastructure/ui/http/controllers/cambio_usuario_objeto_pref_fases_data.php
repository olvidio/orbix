<?php
/**
 * Endpoint JSON: lista de fases para el tipo de actividad indicado.
 *
 * Sucesor de la rama `av_fases` de `apps/cambios/controller/usuario_avisos_pref_ajax.php`.
 */

use src\cambios\application\CambioUsuarioObjetoPrefFasesData;
use web\ContestarJson;

require_once 'frontend/shared/global_header_front.inc';

$input = [
    'objeto' => (string)filter_input(INPUT_POST, 'objeto'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'dl_propia' => (string)filter_input(INPUT_POST, 'dl_propia'),
];

$result = CambioUsuarioObjetoPrefFasesData::execute($input);
$error = (string)$result['error'];
unset($result['error']);

ContestarJson::enviar($error, $result);
