<?php
/*
 * Endpoint backend que devuelve los datos del bloque "filtros extra"
 * (filtro_lugar + lugar + organiza + publicada) de la pantalla
 * `actividad_que`. Incluye la comprobacion de permiso `perm_ctr`:
 * si el usuario no tiene el permiso, devuelve `visible: false`.
 *
 * Responde JSON via src\shared\web\ContestarJson::enviar.
 * El frontend monta el HTML a partir de los payloads de desplegable.
 */

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\DependencyResolver;
use src\actividades\application\ActividadQueFiltrosBloque;
use src\shared\web\ContestarJson;

$sfsv = (int)filter_post('sfsv');
if ($sfsv === 0) {
    $sfsv = (int)ConfigGlobal::mi_sfsv();
}
$modo = (string)filter_post('modo');
if ($modo === '') {
    $modo = 'buscar';
}
$dl_org = (string)filter_post('dl_org');
$filtro_lugar = (string)filter_post('filtro_lugar');
$id_ubi = (int)filter_post('id_ubi');
$publicado = (int)filter_post('publicado');

$proceso_installed = ConfigGlobal::is_app_installed('procesos');

/** @var ActividadQueFiltrosBloque $useCase */
$useCase = DependencyResolver::get(ActividadQueFiltrosBloque::class);
$data = $useCase->ejecutar($sfsv, $modo, $dl_org, $filtro_lugar, $id_ubi, $publicado, $proceso_installed);

ContestarJson::enviar('', $data);
