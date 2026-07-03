<?php

use src\certificados\domain\CertificadoEmitidoDelete;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

/** @var CertificadoEmitidoDelete $useCase */
$useCase = DependencyResolver::get(CertificadoEmitidoDelete::class);

$a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');
if ($a_sel !== []) {
    $Qid_item = (int) strtok($a_sel[0], '#');
} else {
    $Qid_item = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item');
}

$error_txt = $useCase->delete($Qid_item);

ContestarJson::enviar($error_txt, 'ok');
