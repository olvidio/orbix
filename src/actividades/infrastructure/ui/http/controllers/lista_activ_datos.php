<?php

use src\shared\domain\helpers\FilterPostGet;

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
    'que' => (string)\src\shared\domain\helpers\FilterPostGet::post('que'),
    'status' => \src\shared\domain\helpers\FilterPostGet::post('status'),
    'id_tipo_activ' => (string)\src\shared\domain\helpers\FilterPostGet::post('id_tipo_activ'),
    'filtro_lugar' => (string)\src\shared\domain\helpers\FilterPostGet::post('filtro_lugar'),
    'id_ubi' => (int)\src\shared\domain\helpers\FilterPostGet::post('id_ubi'),
    'periodo' => (string)\src\shared\domain\helpers\FilterPostGet::post('periodo'),
    'year' => (string)\src\shared\domain\helpers\FilterPostGet::post('year'),
    'dl_org' => (string)\src\shared\domain\helpers\FilterPostGet::post('dl_org'),
    'empiezamin' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamin'),
    'empiezamax' => (string)\src\shared\domain\helpers\FilterPostGet::post('empiezamax'),
    'c_activ' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList(['c_activ' => \src\shared\domain\helpers\FilterPostGet::post('c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'c_activ'),
    'asist' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList(['asist' => \src\shared\domain\helpers\FilterPostGet::post('asist', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'asist'),
    'seccion' => \src\shared\domain\helpers\FuncTablasSupport::inputStringList(['seccion' => \src\shared\domain\helpers\FilterPostGet::post('seccion', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'seccion'),
    'ssfsv' => (string)\src\shared\domain\helpers\FilterPostGet::post('ssfsv'),
    'sasistentes' => (string)\src\shared\domain\helpers\FilterPostGet::post('sasistentes'),
    'sactividad' => (string)\src\shared\domain\helpers\FilterPostGet::post('sactividad'),
    'snom_tipo' => (string)\src\shared\domain\helpers\FilterPostGet::post('snom_tipo'),
    'titulo' => (string)\src\shared\domain\helpers\FilterPostGet::post('titulo'),
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
