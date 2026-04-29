<?php

use frontend\shared\web\ContestarJson;
use src\dossiers\application\DossiersListaFichasData;

$data = DossiersListaFichasData::build(
    (string)($_POST['pau'] ?? ''),
    (int)($_POST['id_pau'] ?? 0),
    (string)($_POST['obj_pau'] ?? '')
);
// El backend sólo devuelve `*_link_spec` por fila; la firma con HashFront se realiza
// en el frontend (ver frontend/dossiers/controller/lista_dossiers.php).
ContestarJson::enviar('', $data);
