<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

use src\certificados\domain\CertificadoEmitidoEnviar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoEmitidoEnviar $useCase */
$useCase = DependencyResolver::get(CertificadoEmitidoEnviar::class);

$a_sel = input_string_list($_POST, 'sel');
if ($a_sel !== []) {
    $Qid_item = (int) strtok($a_sel[0], '#');
} else {
    $Qid_item = input_int($_POST, 'id_item');
}

$error_txt = $useCase->execute($Qid_item);

ContestarJson::enviar($error_txt, 'ok');
