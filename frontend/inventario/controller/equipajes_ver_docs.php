<?php

use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_grupo = (int)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');
$Qnom_grupo = (string)filter_input(INPUT_POST, 'nom_grupo');

$Qid_lugar = (int)filter_input(INPUT_POST, $Qnom_grupo);


$url_backend = '/src/inventario/lista_docs_de_lugar';
$a_campos_backend = [ 'id_lugar' => $Qid_lugar];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$a_valores = actividades_lista_datos($payload['a_valores'] ?? []);

$a_cabeceras[] = ucfirst(_("sigla"));
$a_cabeceras[] = ucfirst(_("identificador"));
$a_cabeceras[] = ucfirst(_("num reg"));

$a_botones[] = array('txt' => _('seleccionar'), 'click' => "fnjs_update_grupo($Qid_grupo)");

$oLista = new Lista();
$oLista->setId_tabla('docs_' . $Qid_grupo);
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);
$oLista->setBotones($a_botones);


$oHash = new HashFront();
$oHash->setCamposForm('sel');
$oHash->setCamposNo('id_item_egm');
$oHash->setArrayCamposHidden([
    'id_grupo' => $Qid_grupo,
    'id_equipaje' => $Qid_equipaje,
    'id_lugar' => $Qid_lugar,
    'id_item_egm' => '',
]);


echo "<form id='form_$Qid_grupo'>";
echo $oHash->getCamposHtml();
if ($Qid_lugar === 1) { // nuevo.
    echo "<br>";
    echo "<input type='button' name=crear value=\"" . _('crear valija') . "\" onclick='fnjs_update_grupo($Qid_grupo)'>";
    echo "<br>";
}
echo $oLista->mostrar_tabla_html();
echo "</form>";
