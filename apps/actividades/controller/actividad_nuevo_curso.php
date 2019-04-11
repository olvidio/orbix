<?php
/**
* Esta página crea las actividades para el nuevo curso, copiando las del actual.
*
* Se toman sólo las que organiza la dl.
* El periodo del curso es de octubre a octubre: 1.X.año al 1.X.año+1.
* 	Ahora por años naturales: 1.1.año+1 al 1.XII.año+1.
*	Se distinguen dos periodos (navidad y verano) donde lo que pervalece es la fecha.
*      15.XII al 10.I y 30.VI al 1.XI
*   En el resto, pervalece el dia de la semana. Se consigue sumando 364 dias a las fechas.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		9/2/07.
*		
*/

use actividades\model\entity\GestorActividadDl;
use actividades\model\ActividadNuevoCurso;

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qok = (integer)  \filter_input(INPUT_POST, 'ok');

if ($Qok == 1) {
    $Qyear = (integer)  \filter_input(INPUT_POST, 'year');
    $oNuevoCurso = new ActividadNuevoCurso();
	// eliminar las que hay.
	$inicio_iso = $Qyear.'-1-1';
	$fin_iso = $Qyear.'-12-31';
	
	$txt_borrar = $oNuevoCurso->borrar_actividades_periodo($inicio_iso,$fin_iso);
	
	$year_org = $Qyear-1;
	$inicio_org = $year_org.'-1-1';
	$fin_org = $year_org.'-12-31';
	$GesActividades = new GestorActividadDl();
	$aWhere = [];
	$aOperador = [];
	$aWhere['dl_org'] = core\ConfigGlobal::mi_dele();
	// No las de proyecto(1) ni borrables(4) >> 2 y 3
	$aWhere['status'] = "2,3";
	$aOperador['status'] = 'IN';
	$aWhere['f_ini'] =  "'$inicio_org','$fin_org'";
	$aOperador['f_ini'] =  'BETWEEN';
	$aWhere['_ordre'] =  'f_ini';
	$cActividades = $GesActividades->getActividades($aWhere,$aOperador);

	$txt_crear = '';
	$i = 0;
	foreach ($cActividades as $oActividadOrg) {
		$rta = $oNuevoCurso->crear_actividad($oActividadOrg);
		if (empty($rta)) { $i++; }
		$txt_crear .= $rta;
		
	}
	$txt_solapes = $oNuevoCurso->comprobar_solapes($inicio_iso,$fin_iso);
	
	echo "<h3>".sprintf(_("%s actividades copiadas"),$i) ."</h3>";
	
	if (!empty($txt_borrar)) {
	    echo "<h3>"._("incidencias al borrar") ."</h3>";
	    echo $txt_borrar;
	}
	if (!empty($txt_crear)) {
	    echo "<h3>"._("errores al crear") ."</h3>";
	    echo $txt_crear;
	}
	if (!empty($txt_solapes)) {
	    echo "<h3>"._("solapes") ."</h3>";
	    echo $txt_solapes;
	}
	
} else {
    $oHash = new web\Hash();
    $a_camposHidden = array(
        'ok' => 1,
    );
    $camposForm = 'year';
    $oHash->setcamposForm($camposForm);
    $oHash->setArraycamposHidden($a_camposHidden);
    
	$any=date("Y");
	$year1=$any+1;
	$year2=$any+2;
	
	$txt_borrar = sprintf(_("Este progama eliminará todas las actividades para el nuevo curso (%s) en estado proyecto"),$year1); 
	$txt_crear = sprintf(_("Este progama creará las actividades para el nuevo curso (%s) tomando como base las de este curso"),$year1); 
	$txt_estado = _("Las actividades nuevas creadas, quedarán en el estado: proyecto");
	$txt_ctr = _("Se copiarán los centros encargados de las actividades");
	$txt_fases = _("Se crean las fases de cada actividad");
	
	$txt = "<h1>"._("atención").":</h1>";
	$txt .= "<p>$txt_borrar.";
	$txt .= "<p>$txt_crear.";
	$txt .= "<p>$txt_estado.";
	if(core\ConfigGlobal::is_app_installed('ctrEncargados')) {
		$txt .= "<p>$txt_ctr.";
	}
	if(core\ConfigGlobal::is_app_installed('procesos')) {
		$txt .= "<p>$txt_fases.";
	}
	?>
	<?= $txt ?>
	<br>
	<br>
	<form id="frm_sin_nombre" name="frm_sin_nombre" action="apps/actividades/controller/actividad_nuevo_curso.php">
    <?= $oHash->getCamposHtml(); ?>
	<?= _("año:") ?> 
	<select name=year>
	<option value=<?= $year1 ?> selected><?= $year1 ?></option>
	<option value=<?= $year2 ?>><?= $year2 ?></option>
	</select>
	<input type=button onclick=fnjs_enviar_formulario('#frm_sin_nombre') value="<?= _("generar nuevo curso") ?>">
	</form>
<?php
}
