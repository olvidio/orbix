<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\DesplegableEncargosData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)\src\shared\domain\helpers\FilterPostGet::post('id_zona');
$id_enc_raw = \src\shared\domain\helpers\FilterPostGet::post('id_enc');
$Qid_enc = ($id_enc_raw === null || $id_enc_raw === '') ? null : (int)$id_enc_raw;

/** @var DesplegableEncargosData $useCase */
$useCase = DependencyResolver::get(DesplegableEncargosData::class);
$result = $useCase->getData($Qid_zona, $Qid_enc);
ContestarJson::enviar('', $result);
