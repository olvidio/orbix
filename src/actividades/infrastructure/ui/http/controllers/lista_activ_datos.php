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
    'que' => (string)filter_input(INPUT_POST, 'que'),
    'status' => filter_input(INPUT_POST, 'status'),
    'id_tipo_activ' => (string)filter_input(INPUT_POST, 'id_tipo_activ'),
    'filtro_lugar' => (string)filter_input(INPUT_POST, 'filtro_lugar'),
    'id_ubi' => (int)filter_input(INPUT_POST, 'id_ubi'),
    'periodo' => (string)filter_input(INPUT_POST, 'periodo'),
    'year' => (string)filter_input(INPUT_POST, 'year'),
    'dl_org' => (string)filter_input(INPUT_POST, 'dl_org'),
    'empiezamin' => (string)filter_input(INPUT_POST, 'empiezamin'),
    'empiezamax' => (string)filter_input(INPUT_POST, 'empiezamax'),
    'c_activ' => input_string_list(['c_activ' => filter_input(INPUT_POST, 'c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'c_activ'),
    'asist' => input_string_list(['asist' => filter_input(INPUT_POST, 'asist', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'asist'),
    'seccion' => input_string_list(['seccion' => filter_input(INPUT_POST, 'seccion', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?: []], 'seccion'),
    'ssfsv' => (string)filter_input(INPUT_POST, 'ssfsv'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'snom_tipo' => (string)filter_input(INPUT_POST, 'snom_tipo'),
    'titulo' => (string)filter_input(INPUT_POST, 'titulo'),
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
