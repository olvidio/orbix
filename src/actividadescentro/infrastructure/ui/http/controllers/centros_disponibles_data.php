<?php
/**
 * Endpoint backend: devuelve los centros disponibles (candidatos) para
 * asignar como encargado de una actividad, filtrados por `tipo`
 * (sg / sr / nagd / sssc / sfsg / sfsr / sfnagd).
 *
 * Para tipo=sg se incluye por cada centro `num_actividades_periodo` y
 * `dif_dias` respecto a `f_ini_act`.
 */

use src\actividadescentro\application\CentrosDisponiblesData;
use frontend\shared\web\ContestarJson;

$input = [
    'tipo' => (string)filter_input(INPUT_POST, 'tipo'),
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
    'inicio' => (string)filter_input(INPUT_POST, 'inicio'),
    'fin' => (string)filter_input(INPUT_POST, 'fin'),
    'f_ini_act' => (string)filter_input(INPUT_POST, 'f_ini_act'),
];

$data = CentrosDisponiblesData::execute($input);
ContestarJson::enviar('', $data);
