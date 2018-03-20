<?php 
use asignaturas\model as asignaturas;
use actividadestudios\model as actividadestudios;
use notas\model as notas;
use personas\model as personas;
/**
* Esta página muestra un formulario para modificar los datos de un acta.
*
*
*@package	delegacion
*@subpackage	est
*@author	Daniel Serrabou
*@since		14/10/03.
*		
*/

/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$ult_acta = '';
$acta = '';
$f_acta = '';
$libro = '';
$pagina = '';
$linea = '';
$lugar = '';
$observ = '';
//$notas = empty($_POST['notas'])? '': $_POST['notas'];
$notas = empty($notas)? '': $notas;

//últimos
$GesActas = new notas\GestorActa();
$ult_lib = $GesActas->getUltimoLibro();
$ult_pag = $GesActas->getUltimaPagina($ult_lib);
$ult_lin = $GesActas->getUltimaLinea($ult_lib);

$obj = 'notas\\model\\ActaDl';

if (!empty($_POST['sel']) && empty($notas)) { //vengo de un checkbox y no estoy en la página de acta_notas ($notas).
	$notas = '';
	$acta=urldecode(strtok($_POST['sel'][0],"#"));
} else { // vengo de un link 
	if (empty($acta) && !empty($_POST['acta'])) $acta=urldecode($_POST['acta']); // si estoy  en la página de acta_notas ya tengo el acta.
}
if (empty($_POST['nuevo']) && !empty($acta))  { //significa que no es nuevo
	if (!empty($_POST['acta']) && !empty($notas)) { // vengo de actualizar esta pág.
		// estoy actualizando la página
		//empty($_POST["acta"])? $acta="" : $acta=$_POST["acta"];
		$id_asignatura_actual = empty($_POST["id_asignatura"])? "" : $_POST["id_asignatura"];
		$id_actividad = empty($_POST["id_actividad"])? "" : $_POST["id_actividad"];
		$f_acta = empty($_POST["f_acta"])? "" : $_POST["f_acta"];
		$libro = empty($_POST["libro"])? "" : $_POST["libro"];
		$pagina = empty($_POST["pagina"])? "" : $_POST["pagina"];
		$linea = empty($_POST["linea"])? "" : $_POST["linea"];
		$lugar = empty($_POST["lugar"])? "" : $_POST["lugar"];
		$observ = empty($_POST["observ"])? "" : $_POST["observ"];
	} else {
		$oActa = new notas\Acta($acta);
		extract($oActa->getTot());
		$id_asignatura_actual=$id_asignatura;
	}
} else {
	/*
	//busco la última acta (para ayudar)
	$any=date("y");
	$query_acta="SELECT position ('/' in acta) as pos, substring(acta from 4 for position ('/' in acta)-4) as num 
					FROM e_actas where acta ~ 'dlb .+/$any' 
					ORDER BY pos DESC,num DESC limit 1 ";
	//echo "aa: $query_acta<br>";
	$ult_acta=$oDB->query($query_acta)->fetchColumn(1);
	$ult_acta= "dlb ".$ult_acta."/".$any;
	*/
	if ($notas=="nuevo") { //vengo de un ca
		$id_asignatura_actual=$id_asignatura;
		// Busco al profesor como examinador principal.
		$oActividadAsignatura= new actividadestudios\ActividadAsignaturaDl();
		$oActividadAsignatura->setId_activ($id_activ);
		$oActividadAsignatura->setId_asignatura($id_asignatura_actual);
		$oActividadAsignatura->DBCarregar();
		$id_profesor=$oActividadAsignatura->getId_profesor();
		$oPersonaDl = new personas\PersonaDl($id_profesor);
		$ap_nom = $oPersonaDl->getTituloNombreLatin();
		$examinador = $ap_nom;
		$json_examinadores = '[{name: "'.htmlspecialchars($examinador).'"}]';
	} else { // estoy actualizando la página
		if (!empty($_POST['sel']) && !empty($notas)) { //vengo de un checkbox y estoy en la página de acta_notas ($notas).
			$id_activ = strtok($_POST['sel'][0],'#');
			$id_asignatura = strtok('#');
			$cActas = $GesActas->getActas(array('id_activ'=>$id_activ,'id_asignatura'=>$id_asignatura));
			$oActa = $cActas[0];
			extract($oActa->getTot());
			$id_asignatura_actual=$id_asignatura;
		} else {
			$id_asignatura_actual='';
		}
	}
}

if (!empty($ult_lib)) { $ult_lib=sprintf(_("(último= %s)"),$ult_lib); }
if (!empty($ult_pag)) { $ult_pag=sprintf(_("(última= %s)"),$ult_pag); }
if (!empty($ult_lin)) { $ult_lin=sprintf(_("(última= %s)"),$ult_lin); }
if (!empty($ult_acta)) { $ult_acta=sprintf(_("(última= %s)"),$ult_acta); }

if (!empty($acta)) {
	$GesTribunal = new notas\GestorActaTribunalDl();
	$cTribunal = $GesTribunal->getActasTribunales(array('acta'=>$acta,'_ordre'=>'orden')); 
} else {
	$cTribunal = array();
}

$GesAsignaturas = new asignaturas\GestorAsignatura();
$oDesplAsignaturas = $GesAsignaturas->getListaAsignaturas();
if (!empty($id_asignatura_actual)) {
	$jsonTodas = $GesAsignaturas->getJsonAsignaturas(array('id'=>$id_asignatura_actual));
	$json_asignaturas = 'prePopulate: '.$jsonTodas.',';
} else {
	$json_asignaturas = '';
}



$oHash = new web\Hash();
//$sCamposForm = 'libro!linea!pagina!lugar!observ!id_asignatura!examinador_mas!examinador_num';
$sCamposForm = 'libro!linea!pagina!lugar!observ!id_asignatura!f_acta';
if (!empty($_POST['nuevo']) || $notas=="nuevo") { 
	$sCamposForm .= '!acta';
	$sCamposForm .= '!f_acta';
}
if(!empty($cTribunal)) {
	//$sCamposForm .= '!item';
	$sCamposForm .= '!examinadores';
}
$oHash->setcamposForm($sCamposForm);
//$oHash->setCamposNo('go_to!item!examinador');
$oHash->setCamposNo('go_to!examinadores');
$a_camposHidden = array();
if ($notas=="nuevo" || !empty($_POST['nuevo']) ) {
	$a_camposHidden['nuevo'] = 1;
	if (empty($id_activ)) {
		echo _('No se guardará el ca/cv donde se cursó la asignatura');
	} else {
		$a_camposHidden['id_activ'] = $id_activ;
	}
} else {
	$a_camposHidden['acta'] = $acta;
}
$oHash->setArraycamposHidden($a_camposHidden);

$titulo=strtoupper(_("datos del acta"));
?>
<link rel="stylesheet" type="text/css" href="<?php echo core\ConfigGlobal::$web_scripts.'/jquery-tokeninput/styles/token-input.css' ?>" />

<script>
$(function() { $( "#f_acta" ).datepicker(); });

fnjs_guardar_acta=function(){
	var rr=fnjs_comprobar_campos('#modifica','<?= addslashes($obj) ?>');
	if (rr=='ok') {
		$('#modifica').attr('action','apps/notas/controller/acta_update.php');
	<?php if (!empty($notas)) { ?>
		$('#modifica').submit(function() {
			$.ajax({
				data: $(this).serialize(),
				url: $(this).attr('action'),
				type: 'post',
				complete: function (rta) {
					rta_txt=rta.responseText;
					if (rta_txt != '' && rta_txt != '\n') {
						alert (rta_txt);
					}
				}
			});
			return false;
		});
		$('#modifica').submit();
		$('#modifica').off();
	<?php } else { ?>
		$('#modifica').submit(function() {
			$.ajax({
				data: $(this).serialize(),
				url: $(this).attr('action'),
				type: 'post',
				complete: function (rta) {
					rta_txt=rta.responseText;
					if (rta_txt != '' && rta_txt != '\n') {
						alert (rta_txt);
					} else {
						<?php
						$oPosicion = new web\Posicion();
//						$oPosicion->setId_div('ir_a');
						echo $oPosicion->js_atras();
						?>
					}
				}
			});
			return false;
		});
		$('#modifica').submit();
		$('#modifica').off();

		//fnjs_enviar_formulario('#modifica');
	<?php } ?>
	}
}

fnjs_actualizar=function(){  
	$('#modifica').attr('action','apps/notas/controller/acta_ver.php');
	fnjs_enviar_formulario('#modifica');
}
</script>
<?php
include(core\ConfigGlobal::$directorio.'/scripts/mas_opciones.js.php');
?>
<form id="modifica" name="modifica" action="" method="POST" >
<?= $oHash->getCamposHtml(); ?>
<table>
<tr><th colspan='3' class=titulo_inv><?= $titulo ?></Th>
<tr>
	  <td class=etiqueta><?php echo ucfirst(_("acta")); ?>:
	  </td>
	  <?php if (!empty($_POST['nuevo']) || $notas=="nuevo") { 
	  	echo "<td colspan=8><input class=contenido size='25' id='acta' name='acta' value=\"$acta\">"; 
	  	echo "&nbsp;&nbsp;$ult_acta";
	} else {
		echo "<td colspan=8 class=contenido >$acta"; 
	}?>
	  </td></tr>
<tr>
	  <td class=etiqueta><?php echo ucfirst(_("fecha acta")); ?>: </td>
	  <td><input class="fecha" size="11" id="f_acta" name="f_acta" value="<?php echo $f_acta; ?>">
</td></tr>
<tr>
</tr><tr>
<td class=etiqueta>
<?php echo ucfirst(_("libro")); ?>:</td><td colspan=2><input class=contenido size="30" id="libro" name="libro" value="<?php echo $libro ?>">&nbsp;&nbsp;
<?php echo $ult_lib; ?></td>
</tr><tr>
<td class=etiqueta>
<?php echo ucfirst(_("página")); ?>:</td><td colspan=2><input class=contenido size="30" id="pagina" name="pagina" value="<?php echo $pagina ?>">&nbsp;&nbsp;
<?php echo $ult_pag; ?></td>
</tr><tr>
<td class=etiqueta>
<?php echo ucfirst(_("línea")); ?>:</td><td colspan=2><input class=contenido size="30" id="linea" name="linea" value="<?php echo $linea ?>">&nbsp;&nbsp;
<?php echo $ult_lin; ?></td>
</tr><tr>
<td class=etiqueta>
<?php echo ucfirst(_("lugar")); ?>:</td><td colspan=2><input class=contenido size="30" id="lugar" name="lugar" value="<?= htmlspecialchars($lugar) ?>">
</td> 
</tr><tr>
<td class=etiqueta>
<?php echo ucfirst(_("observaciones")); ?>:</td><td colspan=2><input class=contenido size="60" id="observ" name="observ" value="<?= htmlspecialchars($observ) ?>">
</td>
</tr>
<tr><td class=etiqueta><?= ucfirst(_("asignatura")) ?>:</td><td>
<?php
$oDesplAsignaturas->setNombre('id2_asignatura');
$oDesplAsignaturas->setOpcion_sel($id_asignatura_actual);
//echo $oDesplAsignaturas->desplegable(); 


echo "<input type='text'  size='60' id='id_asignatura' name='id_asignatura' value='' >";
?>
</td></tr>
</tr>

</table>
<br>
<!--  --------------- TRIBUNAL --------------- -->
<table><tr><th class=titulo_inv colspan=4><?php echo ucfirst(_("tribunal")); ?></th></tr>
<tr><td class=subtitulo valign='TOP'><?= ucfirst(_("examinador")) ?>:</td>
<td colspan=8 id="col_examinador"><span id="examinador_span" >
<?php
	$e = 0;
	$json_examinadores = '';
	if (!empty($cTribunal)) { 
		$json_examinadores = 'prePopulate: [';
		foreach ($cTribunal as $oActaTribunal) {
			$id_item=$oActaTribunal->getId_item();
			$examinador=$oActaTribunal->getExaminador();
			$orden=$oActaTribunal->getOrden();
			$json_examinadores .= ($e > 0)? ',' : '';
			$json_examinadores .= '{name: "'.htmlspecialchars($examinador).'"}';
			?>
			<!--
			<input type='hidden' id='item[<?= $e ?>]' name='item[<?= $e ?>]' value='<?= $id_item ?>'>
			<input type='text'  size='60' id='examinador_<?= $e ?>' name='examinador[<?= $e ?>]' value="<?= htmlspecialchars($examinador) ?>" onchange="fnjs_comprobar_input('examinador_<?= $e ?>','#g1');">
			-->
			<?php
			$e++;
		}
		$json_examinadores .= '],';
	}
	echo "<input type='text'  size='60' id='examinadores' name='examinadores' value='' >";
	echo "</span>";
	// para que me salga una opción más en blanco
	/*
	echo "<input type='text' size='60' tabindex='89' id='examinador_mas' name='examinador_mas' class=contenido 
	 onchange=\"fnjs_mas_inputs(event,'examinador','#g1',[['item','hidden',1],['examinador','text',60,'x']]);\" />";
	echo "</td></tr>";
	echo "<input type=hidden name='examinador_num' id='examinador_num' value=$e>";
	*/
?>
</td></tr>
</table>
<?php
$acta=urlencode($acta);
if ($notas=="acta") { 
	$el_var = '&';
	foreach ($_POST['sel'] as $key => $value) {
		$el_var .= "&sel[$key]=$value";
	}
	$el_var = substr($el_var, 1);

	$_POST['go_to']="acta_notas.php?mod=".$_POST['mod']."&pau=".$_POST['pau']."&id_pau=".$_POST['id_pau']."&id_dossier=".$_POST['id_dossier']."&permiso=".$_POST['permiso']."&go_to=".$_POST['go_to'].$el_var;

}
if (empty($_POST['go_to'])) $_POST['go_to']="acta_ver.php?acta=$acta";
?>
<input type=hidden id=go_to name=go_to value="<?= $_POST['go_to'] ?>">
<br>
<input id="g1" TYPE="button" VALUE="<?php echo ucfirst(_("guardar cambios acta")); ?>"  onclick="fnjs_guardar_acta()">
</form>
<?php
if ($notas=="nuevo") {
	echo _("Primero debe guardar los valores del acta. Después las notas.");
}

$url = core\ConfigGlobal::getWeb().'/apps/notas/controller/acta_ajax.php';
$oHash = new web\Hash();
$oHash->setUrl($url);
$oHash->setCamposForm('que!q'); 
$h = $oHash->linkSinVal();

$location = $url."?que=examinadores&$h&PHPSESSID=".session_id();
$loc_asig = $url."?que=asignaturas&$h&PHPSESSID=".session_id();

?>
<script type="text/javascript">
$(document).ready(function () {
    $("#id_asignatura").tokenInput("<?= $loc_asig ?>", {
		method: "POST",
		propertyToSearch: "name",
		tokenValue: "id",
		tokenLimit: 1,
		<?= $json_asignaturas ?>
		hintText: "<?= _("escriba parte del nombre para buscar") ?>",
		noResultsText: "<?= _("no hay resultados") ?>",
		searchingText: "<?= _("buscando...") ?>"
	});
    $("#examinadores").tokenInput("<?= $location ?>", {
		method: "POST",
		propertyToSearch: "name",
		tokenValue: "name",
		tokenLimit: 4,
		tokenDelimiter: '#',
		hintText: "<?= _("escriba un texto para buscar") ?>",
		noResultsText: "<?= _("no hay resultados") ?>",
		searchingText: "<?= _("buscando...") ?>",
		<?= $json_examinadores ?>
		allowCustomEntry: true,
		allowFreeTagging: true
	});
});
</script>
