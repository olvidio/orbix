<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\PostRequest;
use frontend\shared\web\Lista;
use frontend\shared\FrontBootstrap;
use frontend\inventario\helpers\InventarioPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$a_sel = ListNavSupport::selFromPost();
$navState = ListNavSupport::buildReturnParametrosFromPost();
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $a_sel !== [] ? ['sel' => $a_sel] : [],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildReturnParametrosFromPost());

$dl = (bool)filter_input(INPUT_POST, 'dl');
$sel_json = json_encode($a_sel);

$url_backend = '/src/inventario/inventario_ctr';
$a_campos_backend = [
    'sel' => $sel_json,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
$payload = InventarioPayload::postPayload($data);
$a_ubi_valores = InventarioPayload::ubiValoresMap($payload['a_valores'] ?? []);

$a_cabeceras = [];
$a_cabeceras[] = '';
$a_cabeceras[] = ucfirst(_('documento'));
$a_cabeceras[] = ucfirst(_('observaciones'));
if ($dl) {
    $a_cabeceras[] = ucfirst(_('observaciones dl'));
}

$a_botones = [['txt' => _('seleccionar'), 'click' => 'fnjs_ver_equipaje()']];

$data_css = PostRequest::getDataFromUrl('/src/inventario/inventario_css_inline_data', []);
$cssPayload = InventarioPayload::postPayload($data_css);
$css = \frontend\shared\helpers\PayloadCoercion::string($cssPayload['css'] ?? '');
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

echo $oPosicion->mostrarNavAtras(1);
echo $html_total;
