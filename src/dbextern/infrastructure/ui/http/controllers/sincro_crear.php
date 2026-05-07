<?php

use frontend\shared\web\ContestarJson;
use src\dbextern\application\CrearPersonaDesdeListasUseCase;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;

$id_nom_listas = (int)filter_input(INPUT_POST, 'id_nom_listas');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

$personaBDURepository = $GLOBALS['container']->get(PersonaBDURepositoryInterface::class);
$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
$useCase = new CrearPersonaDesdeListasUseCase($personaBDURepository, $idMatchRepository);
$error_txt = $useCase($id_nom_listas, $tipo_persona);

// Actualizar sesión: eliminar la persona de la lista de navegación
$id = (int)filter_input(INPUT_POST, 'id');
if ($id > 0 && isset($_SESSION['DBListas'][$id])) {
    session_start();
    unset($_SESSION['DBListas'][$id]);
    $_SESSION['DBListas'] = array_values(array_filter($_SESSION['DBListas']));
    session_write_close();
}

ContestarJson::enviar($error_txt, 'ok');
