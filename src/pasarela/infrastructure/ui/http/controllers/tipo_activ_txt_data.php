<?php
use src\shared\infrastructure\DependencyResolver;

use src\shared\web\ContestarJson;
use src\pasarela\application\TipoActivTxtData;

$id_tipo_activ = (string)filter_post('id_tipo_activ');

/** @var TipoActivTxtData $useCase */
$useCase = DependencyResolver::get(TipoActivTxtData::class);

$data = $useCase->execute($id_tipo_activ);
ContestarJson::enviar('', $data);
