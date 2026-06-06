<?php
/**
 * Endpoint backend: devuelve los datos del cuadro de gestion de
 * plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo)
 * para que el controller frontend monte el `frontend\shared\web\TablaEditable`.
 */

use src\actividadplazas\application\GestionPlazasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use function src\shared\domain\helpers\input_string;

$input = [
    'id_tipo_activ' => input_string($_POST, 'id_tipo_activ'),
    'year' => input_string($_POST, 'year'),
    'periodo' => input_string($_POST, 'periodo'),
    'empiezamin' => input_string($_POST, 'empiezamin'),
    'empiezamax' => input_string($_POST, 'empiezamax'),
    'sasistentes' => input_string($_POST, 'sasistentes'),
    'sactividad' => input_string($_POST, 'sactividad'),
    'sactividad2' => input_string($_POST, 'sactividad2'),
];

/** @var GestionPlazasData $useCase */
$useCase = DependencyResolver::get(GestionPlazasData::class);
ContestarJson::enviar('', $useCase->execute($input));
