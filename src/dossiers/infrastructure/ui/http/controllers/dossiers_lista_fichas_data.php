<?php

use frontend\shared\web\ContestarJson;
use src\dossiers\application\DossiersListaFichasData;
use src\dossiers\infrastructure\ui\http\SignPublicFrontendLink;

$data = DossiersListaFichasData::build(
    (string)($_POST['pau'] ?? ''),
    (int)($_POST['id_pau'] ?? 0),
    (string)($_POST['obj_pau'] ?? '')
);
$data['a_filas'] = SignPublicFrontendLink::resolveDossiersListaFichasFilas($data['a_filas']);
ContestarJson::enviar('', $data);
