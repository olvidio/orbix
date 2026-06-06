<?php

use src\actividadestudios\application\FormAsignaturasDeUnaActividadData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\ubis\domain\RegionStgrAviso;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$error = '';
$data = [];
try {
    $input = [
        'pau' => input_string($_POST, 'pau'),
        'id_pau' => input_int($_POST, 'id_pau'),
        'id_activ' => input_int($_POST, 'id_activ'),
        'id_asignatura' => input_int($_POST, 'id_asignatura'),
        'sel' => isset($_POST['sel']) && is_array($_POST['sel']) ? $_POST['sel'] : null,
    ];
    /** @var FormAsignaturasDeUnaActividadData $useCase */
    $useCase = DependencyResolver::get(FormAsignaturasDeUnaActividadData::class);
    $data = $useCase->execute($input);
} catch (\Throwable $e) {
    $dlError = RegionStgrAviso::esDlSinRegion($e) ? $e : $e->getPrevious();
    if ($dlError instanceof \Throwable && RegionStgrAviso::esDlSinRegion($dlError)) {
        $problemas = [];
        RegionStgrAviso::registrar($problemas, $dlError);
        $aviso = RegionStgrAviso::formatear($problemas);
        $error = ($e !== $dlError) ? $e->getMessage() . '<br>' . $aviso : $aviso;
    } else {
        $error = $e->getMessage();
    }
}

ContestarJson::enviar($error, $data);
