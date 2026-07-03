<?php
use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FilterPostGet;

use src\misas\application\DesplegableCentrosZonaData;
use src\shared\web\ContestarJson;

$Qid_zona = (int)\src\shared\domain\helpers\FilterPostGet::post('id_zona');
$id_ubi_raw = \src\shared\domain\helpers\FilterPostGet::post('id_ubi');
$Qid_ubi = ($id_ubi_raw === null || $id_ubi_raw === '') ? null : (int)$id_ubi_raw;

/** @var DesplegableCentrosZonaData $useCase */
$useCase = DependencyResolver::get(DesplegableCentrosZonaData::class);
$result = $useCase->getData($Qid_zona, $Qid_ubi);
ContestarJson::enviar('', $result);
