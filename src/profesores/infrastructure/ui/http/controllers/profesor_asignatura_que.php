<?php

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use frontend\shared\web\ContestarJson;

$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
$data = [
    'aOpciones' => $AsignaturaRepository->getArrayAsignaturasConSeparador(),
];
ContestarJson::enviar('', $data);
