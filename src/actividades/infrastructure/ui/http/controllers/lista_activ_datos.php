<?php

use src\shared\domain\helpers\FilterPostGet;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * JSON del listado `lista_activ`: filtros POST → {@see ListaActivTabla}.
 * Sin `HashFront` ni `Lista` aquí: celdas pueden traer `link_spec`; el front firma y
 * renderiza en {@see frontend\actividades\controller\lista_activ}.
 */

use src\shared\web\ContestarJson;
use src\actividades\application\ListaActivTabla;
use src\permisos\domain\XPermisos;
use src\shared\infrastructure\DependencyResolver;
use src\shared\config\ConfigGlobal;
$input = [
    'que' => (string)FilterPostGet::post('que'),
    'status' => FilterPostGet::post('status'),
    'id_tipo_activ' => (string)FilterPostGet::post('id_tipo_activ'),
    'filtro_lugar' => (string)FilterPostGet::post('filtro_lugar'),
    'id_ubi' => (int)FilterPostGet::post('id_ubi'),
    'periodo' => (string)FilterPostGet::post('periodo'),
    'year' => (string)FilterPostGet::post('year'),
    'dl_org' => (string)FilterPostGet::post('dl_org'),
    'empiezamin' => (string)FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)FilterPostGet::post('empiezamax'),
    'c_activ' => FuncTablasSupport::inputStringList(['c_activ' => FilterPostGet::post('c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'c_activ'),
    'asist' => FuncTablasSupport::inputStringList(['asist' => FilterPostGet::post('asist', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'asist'),
    'seccion' => FuncTablasSupport::inputStringList(['seccion' => FilterPostGet::post('seccion', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'seccion'),
    'ssfsv' => (string)FilterPostGet::post('ssfsv'),
    'sasistentes' => (string)FilterPostGet::post('sasistentes'),
    'sactividad' => (string)FilterPostGet::post('sactividad'),
    'snom_tipo' => (string)FilterPostGet::post('snom_tipo'),
    'titulo' => (string)FilterPostGet::post('titulo'),
];

$oPerm = $_SESSION['oPerm'] ?? null;
$opts = [
    'mi_sfsv' => ConfigGlobal::mi_sfsv(),
    'perm_vcsd' => $oPerm instanceof XPermisos && $oPerm->have_perm_oficina('vcsd'),
    'perm_des' => $oPerm instanceof XPermisos && $oPerm->have_perm_oficina('des'),
    'perm_sg' => $oPerm instanceof XPermisos && $oPerm->have_perm_oficina('sg'),
    'perm_admin' => $oPerm instanceof XPermisos && $oPerm->have_perm_oficina('admin'),
    'is_dmz' => ConfigGlobal::is_dmz(),
];

$useCase = DependencyResolver::get(ListaActivTabla::class);
$data = $useCase->execute($input, $opts);

ContestarJson::enviar('', $data);
