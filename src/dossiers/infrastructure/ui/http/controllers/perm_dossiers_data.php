<?php

use frontend\shared\web\ContestarJson;
use src\dossiers\application\PermDossiersListaData;

$Qtipo = (string)($_POST['tipo'] ?? '');

$tipo = $Qtipo === '' ? 'p' : $Qtipo;
$data = PermDossiersListaData::build($tipo);
// Backend sólo expone `pagina_link_spec`; el frontend (perm_dossiers.php) firma con HashFront.
ContestarJson::enviar('', $data);
