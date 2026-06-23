<?php
/**
 * Nivel STGR por defecto según id_tipo_activ ({@see ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad}).
 */

use src\shared\web\ContestarJson;
use src\actividades\application\ActividadVerDatos;

$idTipo = (string)filter_post('id_tipo_activ');

$nivel = ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad($idTipo);

ContestarJson::enviar('', ['nivel_stgr_default' => $nivel]);
