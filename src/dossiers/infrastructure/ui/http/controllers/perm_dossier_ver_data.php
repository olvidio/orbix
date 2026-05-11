<?php

use src\shared\web\ContestarJson;
use src\dossiers\application\PermDossierVerFormData;

$Qid_tipo_dossier = (int)($_POST['id_tipo_dossier'] ?? 0);
$Qtipo = (string)($_POST['tipo'] ?? '');

$data = PermDossierVerFormData::build($Qid_tipo_dossier, $Qtipo);
// Backend sólo expone `go_to_link_spec` y `hash_config`; el frontend firma con HashFront
// (ver frontend/dossiers/controller/perm_dossier_ver.php).
ContestarJson::enviar('', $data);
