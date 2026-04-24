<?php
/**
 * Endpoint backend: datos del listado de actividades (titulo + html_tabla).
 *
 * Responde JSON via web\ContestarJson para consumo desde el controlador
 * frontend/actividades/controller/lista_activ.php (patron refactor.md).
 *
 * @package    delegacion
 * @subpackage    actividades
 */

use src\shared\config\ConfigGlobal;
use src\actividades\application\ListaActivTabla;
use web\ContestarJson;

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
    'c_activ' => (array)filter_input(INPUT_POST, 'c_activ', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'asist' => (array)filter_input(INPUT_POST, 'asist', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'seccion' => (array)filter_input(INPUT_POST, 'seccion', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY),
    'ssfsv' => (string)filter_input(INPUT_POST, 'ssfsv'),
    'sasistentes' => (string)filter_input(INPUT_POST, 'sasistentes'),
    'sactividad' => (string)filter_input(INPUT_POST, 'sactividad'),
    'snom_tipo' => (string)filter_input(INPUT_POST, 'snom_tipo'),
    'titulo' => (string)filter_input(INPUT_POST, 'titulo'),
];

$oPerm = $_SESSION['oPerm'];
$opts = [
    'mi_sfsv' => ConfigGlobal::mi_sfsv(),
    'perm_vcsd' => $oPerm->have_perm_oficina('vcsd'),
    'perm_des' => $oPerm->have_perm_oficina('des'),
    'perm_sg' => $oPerm->have_perm_oficina('sg'),
    'perm_admin' => $oPerm->have_perm_oficina('admin'),
    'is_dmz' => ConfigGlobal::is_dmz(),
];

$useCase = new ListaActivTabla();
$data = $useCase->execute($input, $opts);

ContestarJson::enviar('', [
    'titulo' => $data['titulo'],
    'html_tabla' => $data['html_tabla'],
]);
