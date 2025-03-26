<?php

use core\ConfigGlobal;
use core\ViewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qtexto = (string)filter_input(INPUT_POST, 'texto');
$Qloc = (string)filter_input(INPUT_POST, 'loc');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');

$titulo = '';
$texto = $Qtexto;
switch ($Qloc) {
    case 'cabecera':
        $titulo = _("cabecera");
        break;
    case 'cabeceraB':
        $titulo = _("cabecera B)");
        break;
    case 'pie':
        $titulo = _("pie");
        break;
    default:
        $titulo = _("texto a añadir en las maletas");
        // Buscar el texto por defecto
        preg_match('/docs_grupo_(.*)/', $Qloc, $matches);
        $id_grupo = $matches[1];

        $url_lista_backend = Hash::link(ConfigGlobal::getWeb()
            . '/apps/inventario/controller/texto_de_egm.php'
        );
        $oHash = new Hash();
        $oHash->setUrl($url_lista_backend);
        $oHash->setArrayCamposHidden(['id_equipaje' => $Qid_equipaje, 'id_grupo' => $id_grupo]);
        $hash_params = $oHash->getArrayCampos();

        $data = PostRequest::getData($url_lista_backend, $hash_params);

        $texto = $data['texto'];
        break;
}

$oHashForm = new Hash();
$oHashForm->setCamposForm('texto');
$oHashForm->setArrayCamposHidden([
    'id_equipaje' => $Qid_equipaje,
    'loc' => $Qloc,
]);

$a_campos = [
    'oHashForm' => $oHashForm,
    'titulo' => $titulo,
    'texto' => $texto,
];

$oView = new ViewPhtml('../frontend/inventario/controller');
$oView->renderizar('equipajes_form_texto_listado.phtml', $a_campos);