<?php
use personas\model as personas;
use ubis\model as ubis;

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
	$id_pau=strtok($_POST['sel'][0],"#");
	$id_tabla=strtok("#");
	$go_to="atras";
	$pau = empty($_POST['pau'])? "" : $_POST['pau'];
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
} else {
	$pau = empty($_POST['pau'])? "" : $_POST['pau'];
	$id_pau = empty($_POST['id_pau'])? "" : $_POST['id_pau'];
	$go_to = empty($_POST['go_to'])? "" : $_POST['go_to'];
}

$oPersona = personas\Persona::newPersona($id_pau);
$titulo = $oPersona->getNombreApellidos();
$top="top_personas";

if (get_class($oPersona) == 'personas\model\PersonaEx') {
	exit(_("Con las personas de paso no tiene sentido."));
}
//si viene de la página de dossiers, no hace falta la cabecera
// ======================== cabecera =============================
if (empty($_POST['cabecera']) || $_POST['cabecera']!="no") {
	$godossiers=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/dossiers/controller/dossiers_ver.php?'.http_build_query(array('pau'=>$pau,'id_pau'=>$id_pau,'obj_pau'=>$_POST['obj_pau'])));
	$alt=_("ver dossiers");
	$dos=_("dossiers");

	echo $oPosicion->atras();
	?>
	<div id=<?= $top ?>>
	<table><tr>
	<td><span class=link onclick=fnjs_update_div('#main','<?= $godossiers ?>')><img src=<?= core\ConfigGlobal::$web_icons ?>/dossiers.gif border=0 width=40 height=40 alt='<?= $alt ?>'>(<?= $dos ?>)</span></td>
	<td class=titulo><?= $titulo ?></td>
	</table>
	</div>
	<?php
} //fin if cabecera

$aWhere ['tipo_ctr'] = '^[(cgi)|(igl)]';
$aOperador ['tipo_ctr'] =  '!~';
$aWhere ['_ordre'] = 'substr(tipo_ctr,1,1),nombre_ubi';
$gesCentroDl = new ubis\GestorCentroDl();
$cCentrosDl = $gesCentroDl->getCentros($aWhere,$aOperador);

$gesDl = new ubis\GestorDelegacion();
$oDesplDlyR = $gesDl->getListaRegDele();
$oDesplDlyR->setNombre('new_dl');

$gesSituacion = new personas\GestorSituacion();
$cSituacion = $gesSituacion->getSituaciones(array('situacion'=>'[A|D|E|L|T|X]','_ordre'=>'situacion'),array('situacion'=>'~'));


$id_ctr = $oPersona->getId_ctr();
$oUbi = new ubis\CentroDl($id_ctr);
$nombre_ctr = $oUbi->getNombre_ubi();
//$dl = $oUbi->getDl();
$dl = $oPersona->getDl();


$hoy=date("d/m/Y");

$oHash = new web\Hash();
$oHash->setcamposForm('new_ctr!f_ctr!new_dl!f_dl!situacion');
$a_camposHidden = array(
		'id_pau' => $id_pau,
		'id_ctr_o' => $id_ctr,
		'ctr_o' => $nombre_ctr,
		'dl' => $dl,
		'go_to' => $go_to
		);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<script>
$(function() { $( "#f_ctr" ).datepicker(); });
$(function() { $( "#f_dl" ).datepicker(); });
</script>
<form id="frm_sin_nombre" name="frm_sin_nombre" action="apps/personas/controller/traslado_update.php">
	<?= $oHash->getCamposHtml(); ?>
<table border=1>
<tr> 
   <th><?php echo ucfirst(_("tipo"));?></th>
   <th><?php echo ucfirst(_("origen")); ?></th>
   <th><?php echo ucfirst(_("destino")); ?></th>
   <th><?php echo ucfirst(_("fecha")); ?></th>
   </tr>
   <tr> 
	 <td class=etiqueta><?php echo ucfirst(_("centro")); ?></td>    
	 <td><?= $nombre_ctr; ?>&nbsp;</td>
	 <td><select class=contenido id="new_ctr" name="new_ctr">
		<option value="" selected></option>
		<?php
		$i=0;
		foreach ($cCentrosDl as $oCentroDl) {
		$id_ubi = $oCentroDl->getId_ubi();	
		$nombre_ubi = $oCentroDl->getNombre_ubi();	
			echo "<option value=\"".$id_ubi."#".$nombre_ubi."\">".$nombre_ubi."</option>";
			$i++;
		}
		?>
	 </select></td>
	 <td width="15"><input class="fecha" type="Text" id="f_ctr" name="f_ctr" size="10" value="<?= $hoy ?>" >&nbsp;</td>
</tr>
<tr>     
	 <td class=etiqueta><?php echo ucfirst(_("delegación")); ?></td>
	 <td><?= $dl; ?>&nbsp;</td>
	 <td><?= $oDesplDlyR->desplegable(); ?></td>
	 <td><input class="fecha" type="Text" id="f_dl" name="f_dl" size="10" value="<?= $hoy ?>" >&nbsp;</td>
</tr>
<tr>
 	<td class=etiqueta colspan="2" align="RIGHT"><?php echo ucfirst(_("situacion")); ?>:</td>
	<td colspan=2><select class=contenido id="situacion" name="situacion">
	<?php
	foreach ($cSituacion as $oSituacion) {
		$situacion = $oSituacion->getSituacion();
		$nombre_situacion = $oSituacion->getNombre_situacion();
		echo "<option value=\"".$situacion."\">".$situacion." (".$nombre_situacion.")</option>";
	}
	?>
	</select></td>
</tr>
</table>
<br><br>
<input type="button" onclick="fnjs_enviar_formulario('#frm_sin_nombre');" value="<?php echo ucfirst(_("actualizar cambios")); ?>" align="ABSBOTTOM">
