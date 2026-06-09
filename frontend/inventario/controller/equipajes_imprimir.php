<?php

use frontend\inventario\domain\ListaAgrupar;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\PostRequest;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
FrontBootstrap::boot();

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');

$html = '';
if ($Qid_equipaje === 0) {
    exit(_('debe seleccionar un equipaje'));
}

$url_backend = '/src/inventario/cabecera_pie_txt';
$a_campos_backend = [
    'id_equipaje' => $Qid_equipaje,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$cabeceraPie = inventario_cabecera_pie_from_payload(inventario_post_payload($data));
$cabecera = $cabeceraPie['cabecera'];
$cabeceraB = $cabeceraPie['cabeceraB'];
$firma = $cabeceraPie['firma'];
$pie = $cabeceraPie['pie'];

$pencil = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/') . '/images/pencil.png';

$url_backend = '/src/inventario/equipajes_lista_activ_equipaje';
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$a_actividades = inventario_actividades_nombres(inventario_post_payload($data)['a_actividades'] ?? []);
$html_actividades = '';
$html_actividades_firma = '';
$a = 0;
foreach ($a_actividades as $nom_activ) {
    $a++;
    if ($a > 1) {
        $html_actividades .= '<br />';
    }
    $html_actividades .= $nom_activ;
    $html_actividades_firma .= '<p style="text-align:center">' . $nom_activ . '</p>';
    $html_actividades_firma .= '<br>' . $firma;
}

$url_backend = '/src/inventario/equipajes_doc_casa';
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$docCasa = inventario_equipajes_doc_casa_from_payload(inventario_post_payload($data));
$a_valores = inventario_valor_agrupar_rows($docCasa['a_valores']);
$nombre_ubi = $docCasa['nombre_ubi'];
$id_ubi = $docCasa['id_ubi'];

$html_docs_ubi = (new ListaAgrupar())->listaAgrupar($a_valores);

$url_backend = '/src/inventario/equipajes_egm';
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$a_egm = inventario_egm_rows(inventario_post_payload($data)['a_egm'] ?? []);
$html_g = '';
foreach ($a_egm as $aEgm) {
    $id_grupo = $aEgm['id_grupo'];
    $nom_lugar = $aEgm['nom_lugar'];
    $texto = $aEgm['texto'];
    $docs_valores = inventario_valor_agrupar_rows($aEgm['a_valores']);

    $html_g .= "<span id='grupo_$id_grupo'>";
    $html_g .= '<h3>';
    $html_g .= _('valija') . $id_grupo . ':  ';
    $html_g .= $nom_lugar;
    $html_g .= '</h3>';

    $oLista = new ListaAgrupar();
    $oLista->setTexto($texto);
    $html_g .= $oLista->listaAgrupar($docs_valores, $id_grupo);
    $html_g .= '</span>';
}

$html .= "<h1>$nombre_ubi: " . _('Inventario de publicaciones internas');
$html .= '</h1>';
$html .= "<p>$html_actividades</p>";
$html .= '<br />';
$html .= '<div class="no_print" style="margin-bottom: 10px">';
$html .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
        title='" . _('modificar texto') . "''
        alt='" . _('modificar texto') . "'
       onClick=\"fnjs_mod_texto_equipaje('cabecera')\" >";
$html .= $cabecera === '' ? _('introducir texto') : '';
$html .= '</div>';
$html .= "<span id='cabecera'>";
$html .= $cabecera;
$html .= '</span>';
if ($html_docs_ubi !== '') {
    $html .= '<br /><h3>';
    $html .= _('A) Documentación que pertenece a la casa y se queda siempre allí.');
    $html .= '</h3>';

    $html .= "<span id='ubi_$id_ubi'>";
    $html .= '<h2>' . _('documentos en') . " $nombre_ubi</h2>";
    $html .= "<span id='docs_ubi_$id_ubi'>";
    $html .= $html_docs_ubi;
    $html .= '</span>';
    $html .= '</span>';
    $html .= '<br />';
}
if ($html_docs_ubi !== '' && $html_g !== '') {
    $html .= '<br /><h3>';
    $html .= _('B) Documentación que envía la Delegación.');
    $html .= '</h3>';
    $html .= '<div class="no_print" style="margin-bottom: 10px">';
    $html .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
        title='" . _('modificar texto') . "''
        alt='" . _('modificar texto') . "'
       onClick=\"fnjs_mod_texto_equipaje('cabeceraB')\" >";
    $html .= $cabeceraB === '' ? _('introducir texto') : '';
    $html .= '</div>';
    $html .= "<span id='cabeceraB'>";
    $html .= $cabeceraB;
    $html .= '</span>';
    $html .= '<br />';
}
$html .= $html_g;
$html .= '<hr>';
$html .= '<div class="no_print" style="margin-bottom: 10px">';
$html .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
        title='" . _('modificar texto') . "''
        alt='" . _('modificar texto') . "'
       onClick=\"fnjs_mod_texto_equipaje('pie')\" >";
$html .= $pie === '' ? _('introducir texto') : '';
$html .= '</div>';
$html .= "<span class='salta_pag' id='pie' >";
$html .= "<p>$pie</p>";
$html .= '</span>';
$html .= '<hr>';
$html .= "<span id='firma'>";
$html .= "<p>$html_actividades_firma</p>";
$html .= '</span>';

$html .= '<script>fnjs_left_side_hide();</script>';
echo $html;
