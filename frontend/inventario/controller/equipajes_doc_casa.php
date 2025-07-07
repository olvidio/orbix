<?php

// Crea los objetos de uso global **********************************************
use core\ConfigGlobal;
use frontend\shared\PostRequest;
use src\shared\ViewSrcPhtml;
use web\Hash;
use web\Lista;

require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');


$html = '';
if (empty($Qid_equipaje)) {
    exit (_("debe seleccionar un equipaje"));
}

//-------- docs en la casa -----------------------------------
$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/inventario/infrastructure/controllers/equipajes_doc_casa.php'
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

$a_cabeceras[] = ucfirst(_("sigla"));
$a_cabeceras[] = ucfirst(_("identificador"));
$a_cabeceras[] = ucfirst(_("lugar"));

$a_botones[] = array('txt' => _('seleccionar'), 'click' => "fnjs_ver_equipaje()");

$oListaDocsCasa = new Lista();
$oListaDocsCasa->setId_tabla('doc_activ_tabla');
$oListaDocsCasa->setCabeceras($a_cabeceras);
$oListaDocsCasa->setDatos($a_valores);
$oListaDocsCasa->setBotones($a_botones);


//-------- equipajes para la actividad -----------------------------------
$url_lista_backend = Hash::cmdSinParametros(ConfigGlobal::getWeb()
    . '/src/inventario/infrastructure/controllers/equipajes_egm.php'
);
$oHash = new Hash();
$oHash->setUrl($url_lista_backend);
$oHash->setArrayCamposHidden([
    'id_equipaje' => $Qid_equipaje,
]);
$hash_params = $oHash->getArrayCampos();

$data = PostRequest::getData($url_lista_backend, $hash_params);

$a_egm = $data['a_egm'];

$oHash = new Hash();
$oHash->setCamposNo('id_grupo');
$id_grupo = 33;
$oHash->setArrayCamposHidden(['id_grupo' => $id_grupo, 'id_equipaje' => $Qid_equipaje]);

$a_campos = [
    'oHash' => $oHash,
    'nombre_ubi' => $nombre_ubi,
    'oListaDocsCasa' => $oListaDocsCasa,
];

$oView = new ViewSrcPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_doc_casa.phtml', $a_campos);
echo "<div id='grupos'>";

$a_cabeceras = [ucfirst(_("sigla")),
    ucfirst(_("identificador")),
];
foreach ($a_egm as $aEgm) {
    $id_grupo = $aEgm['id_grupo'];
    $id_lugar = $aEgm['id_lugar'];
    $nom_lugar = $aEgm['nom_lugar'];
    $id_item_egm = $aEgm['id_item_egm'];

    $a_valores = $aEgm['a_valores'];

    $oLista = new Lista();
    $oLista->setId_tabla('docs_' . $id_grupo);
    $oLista->setCabeceras($a_cabeceras);
    $oLista->setDatos($a_valores);

    $oHashGrupo = new Hash();
    $oHashGrupo->setArrayCamposHidden([
        'id_grupo' => $id_grupo,
        'id_equipaje' => $Qid_equipaje,
        'id_item_egm' => $id_item_egm,
    ]);

    $a_campos = [
        'id_grupo' => $id_grupo,
        'id_lugar' => $id_lugar,
        'nom_lugar' => $nom_lugar,
        'oLista' => $oLista,
        'oHashGrupo' => $oHashGrupo,
    ];

    $oView = new ViewSrcPhtml('frontend\inventario\controller');
    $oView->renderizar('equipajes_doc_maleta.phtml', $a_campos);
}
echo "</div>"; // id='grupos'
