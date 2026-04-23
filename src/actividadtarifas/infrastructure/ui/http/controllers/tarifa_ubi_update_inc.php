<?php
/**
 * Endpoint backend: actualiza en lote las cantidades de varias
 * `TarifaUbi` desde el estudio economico de casa.
 *
 * Consumido por `apps/casas/controller/calendario_ubi_resumen_ajax.php`
 * y el form `frm_tarifas` dentro de `ubi_resumen.html.twig`.
 */

use src\actividadtarifas\application\TarifaUbiUpdateInc;
use web\ContestarJson;

$inc_cantidad = filter_input(INPUT_POST, 'inc_cantidad', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$input = [
    'inc_cantidad' => is_array($inc_cantidad) ? $inc_cantidad : [],
];

$error = TarifaUbiUpdateInc::execute($input);
ContestarJson::enviar($error, 'ok');
