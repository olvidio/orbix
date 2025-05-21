<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_grupo = (int)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');
$Qid_lugar = (int)filter_input(INPUT_POST, 'id_lugar');
$Qid_item_egm = (int)filter_input(INPUT_POST, 'id_item_egm');

if (!empty($Qid_lugar)) {
    $url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
        . '/src/inventario/controller/lista_docs_de_lugar.php'
    );
}
if (!empty($Qid_item_egm)) {
    $url_lista_backend = Hash::cmd(ConfigGlobal::getWeb()
        . '/src/inventario/controller/lista_docs_de_egm.php'
    );
}
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden(['id_lugar' => $Qid_lugar, 'id_item_egm' => $Qid_item_egm]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_valores = $data['a_valores'];
$nombre_valija = $data['nombre_valija'];

$a_cabeceras[] = ucfirst(_("sigla"));
$a_cabeceras[] = ucfirst(_("identificador"));
//$a_cabeceras[] = ucfirst(_("num reg"));

$oLista = new Lista();
$oLista->setId_tabla('docs_' . $Qid_grupo);
$oLista->setCabeceras($a_cabeceras);
$oLista->setDatos($a_valores);

$oHashGrupo = new Hash();
$oHashGrupo->setArrayCamposHidden([
    'id_grupo' => $Qid_grupo,
    'id_equipaje' => $Qid_equipaje,
    'id_item_egm' => $Qid_item_egm,
]);

$a_campos = [
    'id_grupo' => $Qid_grupo,
    'id_lugar' => $Qid_lugar,
    'nom_lugar' => $nombre_valija,
    'oLista' => $oLista,
    'oHashGrupo' => $oHashGrupo,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_doc_maleta.phtml', $a_campos);