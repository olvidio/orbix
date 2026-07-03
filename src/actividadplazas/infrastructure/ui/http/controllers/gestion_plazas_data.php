<?php

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Endpoint backend: devuelve los datos del cuadro de gestion de
 * plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo)
 * para que el controller frontend monte el `frontend\shared\web\TablaEditable`.
 */

use src\actividadplazas\application\GestionPlazasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_tipo_activ' => FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
    'year' => FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => FuncTablasSupport::inputString($_POST, 'empiezamax'),
    'sasistentes' => FuncTablasSupport::inputString($_POST, 'sasistentes'),
    'sactividad' => FuncTablasSupport::inputString($_POST, 'sactividad'),
    'sactividad2' => FuncTablasSupport::inputString($_POST, 'sactividad2'),
];

/** @var GestionPlazasData $useCase */
$useCase = DependencyResolver::get(GestionPlazasData::class);
ContestarJson::enviar('', $useCase->execute($input));
