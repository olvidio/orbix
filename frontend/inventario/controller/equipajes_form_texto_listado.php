<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qtexto = (string)filter_input(INPUT_POST, 'texto');
$Qloc = (string)filter_input(INPUT_POST, 'loc');
$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');

$titulo = '';
$texto = htmlspecialchars_decode($Qtexto);
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

        $url_backend = '/src/inventario/infrastructure/controllers/texto_de_egm.php';
        $a_campos = [
            'id_equipaje' => $Qid_equipaje,
            'id_grupo' => $id_grupo];
        $data = PostRequest::getDataFromUrl($url_backend, $a_campos);
        if (!empty($data['error'])) {
            exit ($data['error']);
        }
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

$oView = new ViewNewPhtml('frontend\inventario\controller');
$oView->renderizar('equipajes_form_texto_listado.phtml', $a_campos);