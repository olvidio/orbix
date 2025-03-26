<?php

use core\ConfigGlobal;
use frontend\inventario\domain\ListaAgrupar;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');


$html = '';
if (empty($Qid_equipaje)) {
    exit (_("debe seleccionar un equipaje"));
}

//-------- Textos cabecera y pie -----------------------------------
$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/inventario/controller/cabecera_pie_txt.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$cabecera = $data['cabecera'];
$cabeceraB = $data['cabeceraB'];
$firma = $data['firma'];
$pie = $data['pie'];

$pencil = ConfigGlobal::getWeb_icons() . '/pencil.png';

//-------- nombres de las actividades que deben comprobar el equipaje -----------------------------------
$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/inventario/controller/equipajes_lista_activ_equipaje.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([
    'id_equipaje' => $Qid_equipaje,
]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_actividades = $data['a_actividades'];
$html_actividades = '';
$html_actividades_firma = '';
$a = 0;
foreach ($a_actividades as $nom_activ) {
    $a++;
    if ($a > 1) {
        $html_actividades .= '<br />';
    }
    $html_actividades .= $nom_activ;
    $html_actividades_firma .= '<p style="text-align:center">' . $nom_activ . "</p>";
    $html_actividades_firma .= '<br>' . $firma;
}


//-------- docs en la casa -----------------------------------
$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/inventario/controller/equipajes_doc_casa.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([
    'id_equipaje' => $Qid_equipaje,
]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];
$nombre_ubi = $data['nombre_ubi'];
$id_ubi = $data['id_ubi'];

$html_ubi = '';
$html_ubi .= "<span id='ubi_$id_ubi'>";
$html_ubi .= "<h2>" . _("documentos en") . " $nombre_ubi</h2>";
$html_ubi .= "<span id='docs_ubi_$id_ubi'>";

$html_ubi .= (new ListaAgrupar)->listaAgrupar($a_valores);

//-------- equipajes para la actividad -----------------------------------
$url_lista_backend = Hash::link(ConfigGlobal::getWeb()
    . '/apps/inventario/controller/equipajes_egm.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([
    'id_equipaje' => $Qid_equipaje,
]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_egm = $data['a_egm'];
$html_g = '';
foreach ($a_egm as $aEgm) {
    $id_grupo = $aEgm['id_grupo'];
    $id_lugar = $aEgm['id_lugar'];
    $nom_lugar = $aEgm['nom_lugar'];
    $id_item_egm = $aEgm['id_item_egm'];
    $texto = $aEgm['texto'] ?? '';

    $a_valores = $aEgm['a_valores'];

    $html_g .= "<span id='grupo_$id_grupo'>";
    $html_g .= "<h3>";
    $html_g .= _("valija") . $id_grupo . ":  ";
    $html_g .= $nom_lugar;
    $html_g .= "</h3>";
    $html_g .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
        title='". _("modificar texto") ."''
        alt='". _("modificar texto") ."'
       onClick=\"fnjs_mod_texto_equipaje('docs_grupo_$id_grupo')\" >";
    $html_g .= "<span id='docs_grupo_$id_grupo' >";

    $oLista = new ListaAgrupar();
    $oLista->setTexto($texto);
    $html_g .= $oLista->listaAgrupar($a_valores);
}

$html .= "<h1>$nombre_ubi: " . _("Inventario de publicaciones internas");
$html .= "</h1>";
$html .= "<p>$html_actividades</p>";
$html .= "<br />";
$html .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
        title='". _("modificar texto") ."''
        alt='". _("modificar texto") ."'
       onClick=\"fnjs_mod_texto_equipaje('cabecera')\" >";
$html .= "<span id='cabecera'>";
$html .= "$cabecera";
$html .= "</span>";
//$html .= "<br>";
if (!empty($html_ubi)) {
    $html .= "<br /><h3>";
    $html .= _("A) Documentación que pertenece a la casa y se queda siempre allí.");
    $html .= "</h3>";
    $html .= $html_ubi;
    $html .= "<br />";
}
if (!empty($html_ubi) && !empty($html_g)) {
    $html .= "<br /><h3>";
    $html .= _("B) Documentación que envía la Delegación.");
    $html .= "</h3>";
    $html .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
        title='". _("modificar texto") ."''
        alt='". _("modificar texto") ."'
       onClick=\"fnjs_mod_texto_equipaje('cabeceraB')\" >";
    $html .= "<span id='cabeceraB'>";
    $html .= "$cabeceraB";
    $html .= "</span>";
    $html .= "<br />";
}
//$html .= "<docs>$html_g</docs>";
$html .= "$html_g";
$html .= "<hr>";
$html .= "<img class=\"no_print\" style=\"float: left; margin-right: 10px; height:22px;\" src=\"$pencil\" 
        title='". _("modificar texto") ."''
        alt='". _("modificar texto") ."'
       onClick=\"fnjs_mod_texto_equipaje('pie')\" >";
$html .= "<span class='salta_pag' id='pie' >";
$html .= "<p>$pie</p>";
$html .= "</span>";
$html .= "<hr>";
$html .= "<span id='firma'>";
$html .= "<p>$html_actividades_firma</p>";
$html .= "</span>";

echo $html;
