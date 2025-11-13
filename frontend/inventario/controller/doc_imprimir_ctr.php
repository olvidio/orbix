<?php

use core\ConfigGlobal;
use frontend\shared\PostRequest;
use web\Hash;
use web\Lista;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$oPosicion->recordar();

// muestra los ctr que tienen el documento.
$url_backend = '/src/inventario/infrastructure/controllers/inventario_ctr.php';
$a_campos = [
    'sel' => $sel_json,
];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos);
if (!empty($data['error'])) {
	exit ($data['error']);
}
$a_ubi_valores = $data['a_valores'];
$a_ubi_llave = $data['a_llave'];
$a_ubi_tipo = $data['a_tipo'];
$a_ubi_lugar = $data['a_lugar'];
$a_ubi_nom_coleccion = $data['a_nom_coleccion'];

$a_cabeceras[] = "";
$a_cabeceras[] = ucfirst(_("documento"));
$a_cabeceras[] = ucfirst(_("observaciones"));

$a_botones[] = array('txt' => _('seleccionar'), 'click' => "fnjs_ver_equipaje()");


$css = file_get_contents(ConfigGlobal::$dir_estilos . '/inventario.css.php');
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
				 <th style=\"width:100%\">" . _("Inventario de documentos de") . " $nombre_ubi</th> 
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
						<div class=pie>" . _("(*) Se guarda bajo llave") . ".</div>
					</td>
				  </tr> 
				</table> 
			  </tfoot>";

    $html_lista = $oLista->Lista();
    //echo $html;
    $html_body = "<tbody> 
				<tr> 
					<td> 
						$html_lista
					  </td> 
				   </tr>
				 </tbody> 
				</table>";

    //	echo $html_header;
    $html = "<div class=seccion>";
    $html .= $html_header;
    $html .= $html_footer;
    $html .= $html_body;
    $html .= "<div class=\"pageFooter\">" . _("Total de páginas") . ": </div>";
    $html .= _("Conforme (firma)");
    $html .= "</div>";

    $html_total .= $html;
}

echo $oPosicion->mostrar_left_slide(1);
echo $html_total;
