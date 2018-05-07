<?php
/**
* Esta página muestra un formulario con las opciones para escoger a una persona.
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*@ajax		8/2007.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
//Si vengo de vuelta y le paso la referecia del stack donde está la información.
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$Qid_sel=$oPosicion2->getParametro('id_sel');
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
}
$Qmodo = empty($_POST['modo'])? '' : $_POST['modo'];

$Qes_sacd = empty($_POST['es_sacd'])? '' : $_POST['es_sacd'];
$Qna = empty($_POST['na'])? '' : $_POST['na'];
$Qbreve = empty($_POST['breve'])? '' : $_POST['breve'];
$Qque = empty($_POST['que'])? '' : $_POST['que'];
$Qtipo = empty($_POST['tipo'])? '' : $_POST['tipo'];
$Qtabla = empty($_POST['tabla'])? '' : $_POST['tabla'];

$Qexacto = empty($_POST['exacto'])? '' : $_POST['exacto'];
$Qcmb = empty($_POST['cmb'])? '' : $_POST['cmb'];
$Qnombre = empty($_POST['nombre'])? '' : $_POST['nombre'];
$Qapellido1 = empty($_POST['apellido1'])? '' : $_POST['apellido1'];
$Qapellido2 = empty($_POST['apellido2'])? '' : $_POST['apellido2'];
$Qcentro = empty($_POST['centro'])? '' : $_POST['centro'];

if (!empty($Qtabla)) {
	$nom_tabla=substr($Qtabla,2);
	if ($nom_tabla=="de_paso") $nom_tabla=$Qna." ".$nom_tabla;
	if (!empty($Qes_sacd)) $nom_tabla="sacd ".$nom_tabla;
} else {
	$Qtabla="personas";
	$nom_tabla=ucfirst(_("todos"));
}
if ($Qque=="telf") {
	//$action= web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select_telf.php');
	$action= core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select_telf.php';
} else {
	//$action= web\Hash::link(core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select.php');
	$action= core\ConfigGlobal::getWeb().'/apps/personas/controller/personas_select.php';
}
$oHash = new web\Hash();
$oHash->setcamposForm('nombre!apellido1!apellido2!centro!exacto!cmb');
$oHash->setcamposNo('exacto!cmb');
$a_camposHidden = array(
		'tipo' => $Qtipo,
		'tabla' => $Qtabla,
		'na' => $Qna,
		'breve' => $Qbreve,
		'es_sacd' => $Qes_sacd,
		'que' => $Qque
		);
$oHash->setArraycamposHidden($a_camposHidden);

$chk_cmb = empty($Qcmb)? '' : 'checked="checked"';
$chk_exacto_0 = empty($Qexacto)? 'checked' : 0;
$chk_exacto_1 = empty($Qexacto)? '' : 'checked';
?>
<form id="frm_personas_que" action="<?= $action ?>" method="post" onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>

<table>
<thead><th class=titulo_inv colspan=4><?= ucfirst(_("búsqueda de personas")); ?> (<?= $nom_tabla ?>)</th></thead>
<tfoot>
<tr>
	<td class=etiqueta colspan=2><?= _("realizar una búsqueda exacta:"); ?>
	<input type="Radio" name="exacto" value=0 <?= $chk_exacto_0 ?>><?= _("no"); ?>
	<input type="Radio" name="exacto" value=1 <?= $chk_exacto_1 ?>><?= _("sí"); ?></td>
<td class=etiqueta colspan="2" align="RIGHT"><input type="checkbox" name="cmb" <?= $chk_cmb?>><?= _("buscar en fichero cmb"); ?></td></tr>
<tr>
	<th colspan=4><input type="button" id="ok" name="ok" onclick="fnjs_enviar_formulario('#frm_personas_que')" value="<?= ucfirst(_("buscar")); ?>" class="btn_ok">
	<input TYPE="reset" onclick="fnjs_update_div('#main','<?= web\Hash::link('apps/personas/controller/personas_que.php?'.http_build_query(array('tabla'=>$Qtabla,'tipo'=>$Qtipo))) ?>')" value="<?= ucfirst(_("borrar")); ?>"></th>
</tr>
</tfoot>
<tbody>
<tr>
<td class=etiqueta><?= ucfirst(_("nombre")); ?></td> 
<td><input class=contenido id="nombre" name="nombre" size="30" value="<?= $Qnombre ?>"></td></tr>
<tr>
<td class=etiqueta><?= ucfirst(_("primer apellido")); ?></td>
<td><input class=contenido id="apellido1" name="apellido1" size="40" value="<?= $Qapellido1 ?>"></td></tr>
<tr> 
<td class=etiqueta><?= ucfirst(_("segundo apellido")); ?></td>
<td><input class=contenido id="apellido2" name="apellido2" size="40" value="<?= $Qapellido2 ?>"></td></tr>
<tr>
<td class=etiqueta>
<?php 
if (($Qtabla=='p_cp_amigos') OR ($Qtabla=='p_cp_ae_sssc')) {
echo ucfirst(_("centro del que depende")); 
} else {
echo ucfirst(_("centro")); 
}
?>
</b></td>
<td><input class=contenido id="centro" name="centro" value="<?= $Qcentro ?>"></td>
</tr>
</tbody>
</table>
</form>
