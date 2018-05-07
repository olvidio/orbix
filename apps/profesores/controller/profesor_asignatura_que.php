<?php
use asignaturas\model\entity as asignaturas;
use profesores\model\entity as profesores;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$GesAsignaturas = new asignaturas\GestorAsignatura();
$oDesplAsignaturas = $GesAsignaturas->getListaAsignaturas();
$oDesplAsignaturas->setNombre('id_asignatura');
$oDesplAsignaturas->setAction("fnjs_profes()");

/*
$aGoBack = array (
				'loc'=>$loc,
				'que_lista'=>$que_lista,
				 );
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();
*/
	
$oHash = new web\Hash();
$oHash->setUrl('apps/profesores/controller/profesor_asignatura_ajax.php');
$oHash->setCamposForm('id_asignatura');
$h = $oHash->linkSinVal();
?>


<!-- =========================== html =============================  -->
<script>
fnjs_profes=function(){
	var url='<?= core\ConfigGlobal::getWeb().'/apps/profesores/controller/profesor_asignatura_ajax.php' ?>';
	id_asignatura = $("#id_asignatura").val();
	var parametros='id_asignatura='+id_asignatura+'<?= $h ?>&PHPSESSID=<?= session_id(); ?>';
	$.ajax({
		data: parametros,
		url: url,
		type: 'post',
		dataType: 'html',
		complete: function (rta) {
			rta_txt=rta.responseText;
			$('#resultados').html(rta_txt);
		}
	});
};
</script>
<table>
<tr class=tab><th class=titulo_inv colspan=5><?= ucfirst(_("profesores que pueden impartir una asignatura")); ?></th></tr>
<tr><td class=etiqueta><?= ucfirst(_("asignatura")) ?></td>
	<td><?= $oDesplAsignaturas->desplegable(); ?></td>
</tr>
</tbody>
</table>
<div id="resultados">
</div>
