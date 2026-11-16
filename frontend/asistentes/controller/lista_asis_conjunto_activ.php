<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListaDesdeDatos;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
$campos = array_merge($_GET, $_POST);
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_asis_conjunto_activ_data', $campos));

// Un bloque para las actividades de la dl/r y otro para las de otras dl/r.
foreach (is_array($payload['bloques'] ?? null) ? $payload['bloques'] : [] as $bloque) {
    if (!is_array($bloque)) {
        continue;
    }
    echo '<h3>' . htmlspecialchars((string) ($bloque['titulo'] ?? ''), ENT_QUOTES, 'UTF-8') . '</h3>';
    echo ListaDesdeDatos::lista(is_array($bloque['lista'] ?? null) ? $bloque['lista'] : [])->listaPaginada();
}
