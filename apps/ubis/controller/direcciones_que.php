<?php
use ubis\model\entity as ubis;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oUbi = ubis\Ubi::newUbi($_POST['id_ubi']);
$nombre_ubi=$oUbi->getNombre_ubi();
$dl=$oUbi->getDl();
$region=$oUbi->getRegion();
$tipo_ubi=$oUbi->getTipo_ubi();

$tituloGros=ucfirst(_("introduzca un valor para buscar una direccion existente"));

$oHash = new web\Hash();
$oHash->setcamposForm('c_p!ciudad!id_ubi!obj_dir!pais');
$a_camposHidden = array(
		'obj_dir'=>$_POST['obj_dir'],
		'id_ubi'=>$_POST['id_ubi']
		);
$oHash->setArraycamposHidden($a_camposHidden);
?>
<form id="frm_dir_que" name="frm_dir_que" action="apps/ubis/controller/direcciones_tabla.php" method="POST" onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>
<table>
<thead><th class=titulo_inv colspan=4><?= $tituloGros; ?></th></thead>
<tfoot>
<tr>
	<th colspan=4><input type="button" id="ok" name="ok" onclick="fnjs_enviar_formulario('#frm_dir_que','#ficha');" value="<?= ucfirst(_("buscar")); ?>" class="btn_ok">
	<input TYPE="reset" value="<?= ucfirst(_("borrar")); ?>"></th>
</tr>
</tfoot>
<tbody>
<tr>
	<td class=etiqueta><?= ucfirst(_("código Postal")); ?></td>
	<td><input class=contenido id=c_p name=c_p ></td>	 
</tr>
<tr>
	<td class=etiqueta><?= ucfirst(_("población")); ?></td>
	<td><input class=contenido id=ciudad name=ciudad ></td>	
</tr>
<?php if ($tipo_ubi=="ctrex" or $tipo_ubi=="cdcex") { ?>
<tr>
	<td class=etiqueta><?= ucfirst(_("país")); ?></td>
	<td colspan="2"><input class=contenido id=pais name=pais ></td></tr>
<?php } else { ?>
	<input type=hidden id=pais name=pais ></td></tr>
<?php } ?>
</tbody>
</table>
</form>
