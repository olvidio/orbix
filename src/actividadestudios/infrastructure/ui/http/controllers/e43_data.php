<?php

use src\actividadestudios\application\E43CertificadoData;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;
$error = '';
$data = [];
try {
    $idNom = FuncTablasSupport::inputInt($_POST, 'id_nom');
    $idActiv = FuncTablasSupport::inputInt($_POST, 'id_activ');
    /** @var E43CertificadoData $useCase */
    $useCase = DependencyResolver::get(E43CertificadoData::class);
    $data = $useCase->execute(['id_nom' => $idNom, 'id_activ' => $idActiv, 'append_blank_footer' => false]);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
