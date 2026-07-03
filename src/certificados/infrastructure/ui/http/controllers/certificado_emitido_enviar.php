<?php

use src\certificados\domain\CertificadoEmitidoEnviar;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

/** @var CertificadoEmitidoEnviar $useCase */
$useCase = DependencyResolver::get(CertificadoEmitidoEnviar::class);

$a_sel = FuncTablasSupport::inputStringList($_POST, 'sel');
if ($a_sel !== []) {
    $Qid_item = (int) strtok($a_sel[0], '#');
} else {
    $Qid_item = FuncTablasSupport::inputInt($_POST, 'id_item');
}

$error_txt = $useCase->execute($Qid_item);

ContestarJson::enviar($error_txt, 'ok');
