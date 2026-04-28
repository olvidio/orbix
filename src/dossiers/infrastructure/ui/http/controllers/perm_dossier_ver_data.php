<?php

use frontend\shared\web\ContestarJson;
use src\dossiers\application\PermDossierVerFormData;
use src\dossiers\infrastructure\ui\http\SignPublicFrontendLink;

$Qid_tipo_dossier = (int)($_POST['id_tipo_dossier'] ?? 0);
$Qtipo = (string)($_POST['tipo'] ?? '');

$signedGoTo = SignPublicFrontendLink::fromSpec(PermDossierVerFormData::listaPermLinkSpec($Qtipo));
$data = PermDossierVerFormData::build($Qid_tipo_dossier, $Qtipo, $signedGoTo);
ContestarJson::enviar('', $data);
