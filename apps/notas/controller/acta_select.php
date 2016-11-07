<?php
use asignaturas\model as asignaturas;
use notas\model as notas;
/**
* Esta página muestra una tabla con las actas.
*
* Es llamado desde que_actas.php
*
*@package	delegacion
*@subpackage	estudios
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

$mi_dele = core\ConfigGlobal::mi_dele();
$mi_dele .= (core\ConfigGlobal::mi_sfsv() == 2)? 'f' : '';

$go_to='atras';

//Si vengo de vuelta de un go_to:
if (!empty($_POST['atras'])) {
	if ($_POST['atras'] == 2) $oPosicion->go(); //vengo de continuar y debo hacer la búsqueda anterior.
	$Qtitulo = $oPosicion->getParametro('titulo');
	$Qacta = $oPosicion->getParametro('acta');

} else { //si no vengo por goto.
	$Qtitulo = empty($_POST['titulo'])? '' : $_POST['titulo'];
	$Qacta = empty($_POST['acta'])? '' : $_POST['acta'];
}

/*miro las condiciones. Si es la primera vez muestro las de este año */
$aWhere = array();
$aOperador = array();
if (!empty($Qacta)) {
	$dl_acta = strtok($Qacta,' ');

	if ($dl_acta == $mi_dele || $dl_acta == "?") {
		if ($dl_acta == "?") $Qacta = "\?";
		$GesActas = new notas\GestorActaDl();
	} else {
		// si es número busca en la dl.
		preg_match ("/^(\d*)(\/)?(\d*)/", $Qacta, $matches);
		if (!empty($matches[1])) {
			$Qacta = empty($matches[3])? "$mi_dele ".$matches[1].'/'.date("y") : "$mi_dele $Qacta";
			$GesActas = new notas\GestorActaDl();
		} else {
		// Ojo si la dl ya existe no deberia hacerse
			$GesActas = new notas\GestorActaEx();
		}
	}

	$aWhere['acta'] = $Qacta;
	$aOperador['acta'] = '~';
	$titulo = $Qtitulo;
} else {
	$mes=date('m');
	if ($mes>9) { $any=date('Y')+1; } else { $any=date("Y"); }
	$inicurs_ca=core\curso_est("inicio",$any);
	$fincurs_ca=core\curso_est("fin",$any);
	
	$aWhere['f_acta'] = "'$inicurs_ca','$fincurs_ca'";
	$aOperador['f_acta'] = 'BETWEEN';
	
	$titulo=ucfirst(sprintf(_("lista de actas del curso %s"),$txt_curso));
	$GesActas = new notas\GestorActaDl();
}

$cActas = $GesActas->getActas($aWhere,$aOperador);

/*
* Defino un array con los datos actuales, para saber volver después de navegar un rato
*/
$aGoBack = array (
				'titulo'=>$Qtitulo,
				'acta'=>$Qacta );
$oPosicion->setParametros($aGoBack);
$oPosicion->recordar();

$a_botones=array( array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(\"#seleccionados\")" ) ,
				array( 'txt' => _('imprimir'), 'click' =>"fnjs_imprimir(\"#seleccionados\")" ) 
				);

$a_cabeceras=array( array('name'=>ucfirst(_("acta")),'formatter'=>'clickFormatter'), 
		array('name'=>ucfirst(_("fecha")),'class'=>'fecha'),
		_("asignatura"));

$i=0;
$a_valores = array();
foreach ($cActas as $oActa) {
	$i++;
	$acta=$oActa->getActa();
	$f_acta=$oActa->getF_acta();
	$id_asignatura=$oActa->getId_asignatura();

	$oAsignatura = new asignaturas\Asignatura($id_asignatura);
	$nombre_corto = $oAsignatura->getNombre_corto();

	$acta_2=urlencode($acta);
	//$pagina="apps/notas/controller/acta_ver.php?acta=$acta_2";
	$pagina=web\Hash::link('apps/notas/controller/acta_ver.php?'.http_build_query(array('acta'=>$acta)));
	$a_valores[$i]['sel']=$acta_2;
	$a_valores[$i][1]=array( 'ira'=>$pagina, 'valor'=>$acta);
	$a_valores[$i][2]=$f_acta;
	$a_valores[$i][3]=$nombre_corto;
}

$oHash = new web\Hash();
$oHash->setcamposForm('acta');

$oHash1 = new web\Hash();
$oHash1->setcamposForm('sel!nuevo');
$oHash1->setCamposNo('sel!nuevo');
$a_camposHidden1 = array(
		'go_to' => $go_to
		);
$oHash1->setArraycamposHidden($a_camposHidden1);


/* ---------------------------------- html --------------------------------------- */
?>
<script>
fnjs_imprimir=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/notas/controller/acta_imprimir.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
fnjs_nuevo=function(formulario){
	$('#nuevo').val("1");
	$(formulario).attr('action',"apps/notas/controller/acta_ver.php");
  	fnjs_enviar_formulario(formulario,'#main');
}
fnjs_modificar=function(formulario){
	rta=fnjs_solo_uno(formulario);
	if (rta==1) {
  		$(formulario).attr('action',"apps/notas/controller/acta_ver.php");
  		fnjs_enviar_formulario(formulario);
  	}
}
</script>
<form id="frm_sin_nombre" name="frm_sin_nombre" action="apps/notas/controller/acta_select.php" method="post" onkeypress="fnjs_enviar(event,this);" >
<?= $oHash->getCamposHtml(); ?>
<table>
<th class=titulo_inv colspan=4><?php echo ucfirst(_("buscar un acta")); ?>
&nbsp;&nbsp;&nbsp;<input class=contenido id="acta" name="acta" size="25"></th>
<th colspan=4><input type="button" id="ok" name="ok" onclick="fnjs_enviar_formulario('#frm_sin_nombre');" value="<?php echo ucfirst(_("buscar")); ?>" class="btn_ok"></th>
</table>
</form>

<h3 class=subtitulo><?= $titulo ?></h3>
<form id='seleccionados' id='seleccionados' name='seleccionados' action='' method='post'>
<?= $oHash1->getCamposHtml(); ?>
<input type='Hidden' id='nuevo' name='nuevo' value=''>
<?php
$oTabla = new web\Lista();
$oTabla->setId_tabla('acta_select');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);
echo $oTabla->mostrar_tabla();
?>
</form>
<br><table><tr class=botones>
<td class=botones><span class=link_inv onclick="fnjs_nuevo('#seleccionados');" ><?= _("añadir acta") ?></span></td>
