<?php

use src\dossiers\application\DossiersListaFichasData;
use frontend\shared\web\ContestarJson;

$data = DossiersListaFichasData::build(
    (string)($_POST['pau'] ?? ''),
    (int)($_POST['id_pau'] ?? 0),
    (string)($_POST['obj_pau'] ?? '')
);
ContestarJson::enviar('', $data);
