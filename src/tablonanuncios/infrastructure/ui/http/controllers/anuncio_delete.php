<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\tablonanuncios\application\AnuncioDelete;

/** @var AnuncioDelete $useCase */
$useCase = DependencyResolver::get(AnuncioDelete::class);

$a_sel = \src\shared\domain\helpers\FuncTablasSupport::inputStringList($_POST, 'sel');
if ($a_sel !== []) {
    $Quuid_item = (string) strtok($a_sel[0], '#');
} else {
    $Quuid_item = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'uuid_item');
}

$error_txt = $useCase->execute($Quuid_item);

ContestarJson::enviar($error_txt, 'ok');
