<?php

use frontend\shared\web\ContestarJson;
use src\dbextern\application\SincroPersonas;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

$region = (string)filter_input(INPUT_POST, 'region');
$dl_listas = (string)filter_input(INPUT_POST, 'dl_listas');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');

$idMatchRepository = $GLOBALS['container']->get(IdMatchPersonaRepositoryInterface::class);
$centroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);

$useCase = new SincroPersonas($idMatchRepository, $centroDlRepository);
$result = $useCase($region, $dl_listas, $tipo_persona);

$error_txt = '';
if (!empty($result['msg'])) {
    $msg = $result['msg'];
} else {
    $msg = sprintf(_("OK. %s personas sincronizadas"), $result['count']);
}

ContestarJson::enviar($error_txt, ['mensaje' => $msg]);
