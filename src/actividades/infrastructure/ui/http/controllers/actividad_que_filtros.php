<?php
/*
 * Endpoint backend que devuelve el HTML del bloque "filtros extra"
 * (filtro_lugar + lugar + organiza + publicada) de la pantalla
 * `actividad_que`. Incluye la comprobacion de permiso `perm_ctr`:
 * si el usuario no tiene el permiso, devuelve cadena vacia.
 *
 * Responde JSON via src\shared\web\ContestarJson::enviar con la clave `html`.
 */

use src\shared\config\ConfigGlobal;
use src\actividades\application\ActividadQueFiltrosBloque;
use src\shared\web\ContestarJson;

$sfsv = (int)filter_input(INPUT_POST, 'sfsv');
if ($sfsv === 0) {
    $sfsv = (int)ConfigGlobal::mi_sfsv();
}
$modo = (string)filter_input(INPUT_POST, 'modo');
if ($modo === '') {
    $modo = 'buscar';
}
$dl_org = (string)filter_input(INPUT_POST, 'dl_org');
$filtro_lugar = (string)filter_input(INPUT_POST, 'filtro_lugar');
$id_ubi = (int)filter_input(INPUT_POST, 'id_ubi');
$publicado = (int)filter_input(INPUT_POST, 'publicado');

$proceso_installed = ConfigGlobal::is_app_installed('procesos');

$useCase = new ActividadQueFiltrosBloque();
$html = $useCase->ejecutar($sfsv, $modo, $dl_org, $filtro_lugar, $id_ubi, $publicado, $proceso_installed);

ContestarJson::enviar('', ['html' => $html]);
