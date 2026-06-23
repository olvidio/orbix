<?php
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
use function src\shared\domain\helpers\input_string_list;

$input = [
    'que' => (string)filter_post('que'),
    'status' => filter_post('status'),
    'id_tipo_activ' => (string)filter_post('id_tipo_activ'),
    'filtro_lugar' => (string)filter_post('filtro_lugar'),
    'id_ubi' => (int)filter_post('id_ubi'),
    'periodo' => (string)filter_post('periodo'),
    'year' => (string)filter_post('year'),
    'dl_org' => (string)filter_post('dl_org'),
    'empiezamin' => (string)filter_post('empiezamin'),
    'empiezamax' => (string)filter_post('empiezamax'),
    'c_activ' => input_string_list(['c_activ' => filter_post('c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'c_activ'),
    'asist' => input_string_list(['asist' => filter_post('asist', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'asist'),
    'seccion' => input_string_list(['seccion' => filter_post('seccion', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'seccion'),
    'ssfsv' => (string)filter_post('ssfsv'),
    'sasistentes' => (string)filter_post('sasistentes'),
    'sactividad' => (string)filter_post('sactividad'),
    'snom_tipo' => (string)filter_post('snom_tipo'),
    'titulo' => (string)filter_post('titulo'),
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
