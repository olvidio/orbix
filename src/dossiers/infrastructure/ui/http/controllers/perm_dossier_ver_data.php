<?php

use src\dossiers\application\PermDossierVerFormData;
use frontend\shared\web\ContestarJson;

$Qid_tipo_dossier = (int)($_POST['id_tipo_dossier'] ?? 0);
$Qtipo = (string)($_POST['tipo'] ?? '');

$data = PermDossierVerFormData::build($Qid_tipo_dossier, $Qtipo);
ContestarJson::enviar('', $data);
