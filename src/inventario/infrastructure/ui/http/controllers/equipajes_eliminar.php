<?php

use src\inventario\application\EquipajeEliminar;
use src\shared\web\ContestarJson;

$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');

// eliminar el equipaje y sus docs
// los grupos en egm deberían eliminarse por la base de datos, al tener una foreign key.
// los docs en whereis deberían eliminarse por la base de datos, al tener una foreign key.

$error_txt = EquipajeEliminar::execute($Qid_equipaje);

ContestarJson::enviar($error_txt, 'ok');

