<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use src\shared\config\ConfigGlobal;

require_once 'frontend/shared/global_header_front.inc';

$mov = '';
$region = (string)filter_input(INPUT_POST, 'region');
$dl = (string)filter_input(INPUT_POST, 'dl');
$tipo_persona = (string)filter_input(INPUT_POST, 'tipo_persona');
$id = (string)filter_input(INPUT_POST, 'id');
$mov = (string)filter_input(INPUT_POST, 'mov');

$cont_sync = 0;
$first_load = empty($id);

if ($first_load) {
    $id = 1;
    // Pedir la lista completa al backend
    $data = PostRequest::getDataFromUrl('/src/dbextern/ver_listas_datos', [
        'region' => $region,
        'dl' => $dl,
        'tipo_persona' => $tipo_persona,
        'first_load' => $first_load ? '1' : '',
    ]);
    $a_lista = $data['lista'] ?? [];
    $cont_sync = $data['cont_sync'] ?? 0;

    session_start();
    $_SESSION['DBListas'] = $a_lista;
    session_write_close();
}

// Navegación por sesión
function otro_listas($id, $mov, $max)
{
    switch ($mov) {
        case '-':
            $id--;
            if ($id < 1) return 1;
            break;
        case '+':
            $id++;
            if ($id > $max) return $max;
            break;
        default:
            $id = 1;
    }
    if (isset($_SESSION['DBListas'][$id])) {
        return $id;
    }
    return otro_listas($id, $mov, $max);
}

$max = count($_SESSION['DBListas']);
$a_lista_orbix = [];
$persona_listas = [];
$a_lista_orbix_otradl = [];
$new_id = 0;
$id_nom_bdu = '';

if (!empty($max)) {
    $new_id = otro_listas($id, $mov, $max);
}

if (!empty($new_id) && isset($_SESSION['DBListas'][$new_id])) {
    $persona_listas = $_SESSION['DBListas'][$new_id];
    $id_nom_bdu = $persona_listas['id_nom_listas'];

    // Pedir posibles matches al backend
    $matches = PostRequest::getDataFromUrl('/src/dbextern/ver_listas_datos', [
        'region' => $region,
        'dl' => $dl,
        'tipo_persona' => $tipo_persona,
        'id_nom_bdu' => $id_nom_bdu,
    ]);
    $a_lista_orbix = $matches['posibles_misma_dl'] ?? [];
    $a_lista_orbix_otradl = $matches['posibles_otra_dl'] ?? [];
}

// Hash para el formulario de navegación
$url_sincro_ver = ConfigGlobal::getWeb() . '/frontend/dbextern/controller/ver_listas.php';
$oHash = new HashFront();
$oHash->setUrl($url_sincro_ver);
$oHash->setcamposNo('mov');
$a_camposHidden = [
    'region' => $region,
    'dl' => $dl,
    'tipo_persona' => $tipo_persona,
    'id' => $new_id,
];
$oHash->setArraycamposHidden($a_camposHidden);

// Hash para AJAX crear/unir/crear_todos
$url_sincro_crear = ConfigGlobal::getWeb() . '/src/dbextern/sincro_crear';
$oHash1 = new HashFront();
$oHash1->setUrl($url_sincro_crear);
$oHash1->setCamposForm('id_nom_listas!id_orbix!region!dl!id!tipo_persona');
$h_crear = $oHash1->linkSinValParams();

$url_sincro_unir = ConfigGlobal::getWeb() . '/src/dbextern/sincro_unir';
$oHash2 = new HashFront();
$oHash2->setUrl($url_sincro_unir);
$oHash2->setCamposForm('id_nom_listas!id_orbix!region!dl!id!tipo_persona');
$h_unir = $oHash2->linkSinValParams();

$url_sincro_crear_todos = ConfigGlobal::getWeb() . '/src/dbextern/sincro_crear_todos';
$oHash3 = new HashFront();
$oHash3->setUrl($url_sincro_crear_todos);
$oHash3->setCamposForm('region!dl!tipo_persona');
$h_crear_todos = $oHash3->linkSinValParams();

$html_reg = sprintf(_("registro %s de %s"), $new_id, $max);

$a_campos = [
    'region' => $region,
    'dl' => $dl,
    'tipo_persona' => $tipo_persona,
    'id_nom_bdu' => $id_nom_bdu,
    'new_id' => $new_id,
    'max' => $max,
    'mov' => $mov,
    'cont_sync' => $cont_sync,
    'first_load' => $first_load,
    'persona_listas' => $persona_listas,
    'a_lista_orbix' => $a_lista_orbix,
    'a_lista_orbix_otradl' => $a_lista_orbix_otradl,
    'html_reg' => $html_reg,
    'oHash' => $oHash,
    'url_sincro_ver' => $url_sincro_ver,
    'url_sincro_crear' => $url_sincro_crear,
    'url_sincro_unir' => $url_sincro_unir,
    'url_sincro_crear_todos' => $url_sincro_crear_todos,
    'h_crear' => $h_crear,
    'h_unir' => $h_unir,
    'h_crear_todos' => $h_crear_todos,
];

$oView = new \frontend\shared\model\ViewNewPhtml();
$oView->renderizar(__FILE__, $a_campos);
