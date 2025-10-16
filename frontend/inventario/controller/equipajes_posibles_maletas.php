<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (string)filter_input(INPUT_POST, 'id_equipaje');

$url_backend = '/src/inventario/infrastructure/controllers/lista_equipajes_posibles_maletas.php';
$a_campos = [ 'id_equipaje' => $Qid_equipaje];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos);

$a_opciones = $data['a_opciones'];
$new_id_grupo = $data['new_id_grupo'];


// generar maletas
$nom_grupo = "sel_grupo_" . $new_id_grupo;
$nom_form = "form_ver_$new_id_grupo";

$oDespl = new Desplegable();
$oDespl->setOpciones($a_opciones);
$oDespl->setNombre($nom_grupo);
$oDespl->setBlanco(true);
$oDespl->setAction("fnjs_ver_docs('$new_id_grupo')");

$oHash = new Hash();
$oHash->setCamposForm($nom_grupo);
$oHash->setArrayCamposHidden([
    'id_grupo' => $new_id_grupo,
    'id_equipaje' => $Qid_equipaje,
    'nom_grupo' => $nom_grupo
]);


echo "<span id='grupo_$new_id_grupo'>";
echo "<form id='$nom_form'>";
echo $oHash->getCamposHtml();
echo "<br>";
echo _("valija") . $new_id_grupo;
echo $oDespl->desplegable();
echo "</form>";
echo "<span id='docs_grupo_$new_id_grupo'>";
echo "</span>";
echo "</span>";

