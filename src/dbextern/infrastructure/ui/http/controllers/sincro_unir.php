<?php

use frontend\shared\web\ContestarJson;
use src\dbextern\application\UnirPersonaUseCase;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;

$id_nom_listas = (int)filter_input(INPUT_POST, 'id_nom_listas');
$id_orbix = (int)filter_input(INPUT_POST, 'id_orbix');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
$useCase = new UnirPersonaUseCase($idMatchRepository);
$error_txt = $useCase($id_nom_listas, $id_orbix, $tipo_persona);

// Actualizar sesión: eliminar la persona de la lista de navegación
$id = (int)filter_input(INPUT_POST, 'id');
if ($id > 0 && isset($_SESSION['DBListas'][$id])) {
    session_start();
    unset($_SESSION['DBListas'][$id]);
    $_SESSION['DBListas'] = array_values(array_filter($_SESSION['DBListas']));
    session_write_close();
}

ContestarJson::enviar($error_txt, 'ok');
