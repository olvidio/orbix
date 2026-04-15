<?php

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use web\ContestarJson;

$AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
$data = [
    'aOpciones' => $AsignaturaRepository->getArrayAsignaturasConSeparador(),
];
$jsondata = ContestarJson::respuestaPhp('', $data);
ContestarJson::send($jsondata);
