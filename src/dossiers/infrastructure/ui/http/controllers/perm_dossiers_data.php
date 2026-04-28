<?php

use frontend\shared\web\ContestarJson;
use src\dossiers\application\PermDossiersListaData;
use src\dossiers\infrastructure\ui\http\SignPublicFrontendLink;

$Qtipo = (string)($_POST['tipo'] ?? '');

$tipo = $Qtipo === '' ? 'p' : $Qtipo;
$data = PermDossiersListaData::build($tipo);
foreach ($data['a_filas'] as $i => $fila) {
    if (!empty($fila['pagina_link_spec']) && is_array($fila['pagina_link_spec'])) {
        $data['a_filas'][$i]['pagina'] = SignPublicFrontendLink::fromSpec($fila['pagina_link_spec']);
        unset($data['a_filas'][$i]['pagina_link_spec']);
    }
}
ContestarJson::enviar('', $data);
