<?php

use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
require_once __DIR__ . '/../helpers/inventario_support.php';
$oPosicion = FrontBootstrap::boot();

list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$dl = (bool)filter_input(INPUT_POST, 'dl');
$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$sel_json = json_encode($a_sel);

$url_backend = '/src/inventario/inventario_ctr';
$a_campos_backend = [
    'sel' => $sel_json,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = inventario_post_payload($data);
$a_ubi_valores = inventario_ubi_valores_map($payload['a_valores'] ?? []);

$a_cabeceras = [];
$a_cabeceras[] = '';
$a_cabeceras[] = ucfirst(_('documento'));
$a_cabeceras[] = ucfirst(_('observaciones'));
if ($dl) {
    $a_cabeceras[] = ucfirst(_('observaciones dl'));
}

$a_botones = [['txt' => _('seleccionar'), 'click' => 'fnjs_ver_equipaje()']];

$data_css = PostRequest::getDataFromUrl('/src/inventario/inventario_css_inline_data', []);
$cssPayload = inventario_post_payload($data_css);
$css = tessera_imprimir_string($cssPayload['css'] ?? '');
$html_total = $css;
foreach ($a_ubi_valores as $nombre_ubi => $a_valores) {
    $html = '';
    $oLista = new Lista();
    $oLista->setId_tabla('doc_activ_tabla');
    $oLista->setCabeceras($a_cabeceras);
    $oLista->setDatos($a_valores);
    $oLista->setBotones($a_botones);

    $html_header = "<table> 
			   <thead> 
				<tr> 
				 <th style=\"width:100%\">" . _('Inventario de documentos de') . " $nombre_ubi</th> 
			   </tr> 
			   <tr> 
				<th><hr style=\"color:#000080\"/></th> 
			   </tr> 
			  </thead>";

    $html_footer = "<tfoot id=pageFooter>
			   <tr> 
				<td> 
				 <table> 
				   <tr> 
					 <td colspan=\"4\">
						<div class=pie>" . _('(*) Se guarda bajo llave') . ".</div>
					</td>
				  </tr> 
				</table> 
			  </tfoot>";

    $html_lista = $oLista->Lista();
    $html_body = "<tbody> 
				<tr> 
					<td> 
						$html_lista
					  </td> 
				   </tr>
				 </tbody> 
				</table>";

    $html = '<div class=seccion>';
    $html .= $html_header;
    $html .= $html_footer;
    $html .= $html_body;
    $html .= '<div class="pageFooter">' . _('Total de páginas') . ': </div>';
    $html .= _('Conforme (firma)');
    $html .= '</div>';

    $html_total .= $html;
}

echo $oPosicion->mostrar_left_slide(1);
echo $html_total;
