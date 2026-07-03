<?php


/**
 * Endpoint backend: devuelve los datos del cuadro de gestion de
 * plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo)
 * para que el controller frontend monte el `frontend\shared\web\TablaEditable`.
 */

use src\actividadplazas\application\GestionPlazasData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
$input = [
    'id_tipo_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'id_tipo_activ'),
    'year' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'year'),
    'periodo' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo'),
    'empiezamin' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin'),
    'empiezamax' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax'),
    'sasistentes' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'sasistentes'),
    'sactividad' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'sactividad'),
    'sactividad2' => \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'sactividad2'),
];

/** @var GestionPlazasData $useCase */
$useCase = DependencyResolver::get(GestionPlazasData::class);
ContestarJson::enviar('', $useCase->execute($input));
