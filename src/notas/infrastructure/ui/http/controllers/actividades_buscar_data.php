<?php

use src\notas\application\ActividadesBuscarData;
use web\ContestarJson;

/**
 * Datos (delegaciones + actividades) para el dialogo "buscar actividad"
 * que abre `frontend/notas/controller/actividad_buscar_form.php` desde
 * `form_notas_de_una_persona.phtml` al modificar una nota asociada a una actividad.
 */
$data = ActividadesBuscarData::execute($_POST);
ContestarJson::enviar('', $data);
