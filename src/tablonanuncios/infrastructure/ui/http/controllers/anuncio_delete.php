<?php

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_string_list;

use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\tablonanuncios\application\AnuncioDelete;

/** @var AnuncioDelete $useCase */
$useCase = DependencyResolver::get(AnuncioDelete::class);

$a_sel = input_string_list($_POST, 'sel');
if ($a_sel !== []) {
    $Quuid_item = (string) strtok($a_sel[0], '#');
} else {
    $Quuid_item = input_string($_POST, 'uuid_item');
}

$error_txt = $useCase->execute($Quuid_item);

ContestarJson::enviar($error_txt, 'ok');
