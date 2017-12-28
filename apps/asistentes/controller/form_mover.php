<?php
use actividades\model as actividades;
use asistentes\model as asistentes;
use actividadestudios\model as actividadestudios;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosiblesCa = new actividadestudios\PosiblesCa(); 

$id_activ_old = empty($_POST['id_activ'])? '' : $_POST['id_activ'];
$id_nom = empty($_POST['id_pau'])? '' : $_POST['id_pau'];

$go_to = 'no';

$gesDelegacion = new ubis\model\GestorDelegacion();
$gesActividadPlazas = new \actividadplazas\model\GestorActividadPlazas();
$gesAsistentes = new \asistentes\model\GestorAsistente();
$mi_dele = core\ConfigGlobal::mi_dele();
$cDelegaciones = $gesDelegacion->getDelegaciones(array('dl'=> $mi_dele));
$oDelegacion = $cDelegaciones[0];
$id_dl = $oDelegacion->getId_dl();

//borrar el actual y poner la nueva
$propietario = '';
if (!empty($id_activ_old) && !empty($id_nom)) {
	$mod="mover";
	
	//del mismo tipo que la anterior
	$oActividad = new actividades\Actividad(array('id_activ'=>$id_activ_old));
	$id_tipo = $oActividad->getId_tipo_activ();

	// IMPORTANT: Propietario del a plaza
	// si es de la sf quito la 'f'
	$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
	$propietario = "$dl>$mi_dele";

	$oTipoActiv= new web\TiposActividades($id_tipo);
	$ssfsv = $oTipoActiv->getSfsvText();
	$sasistentes = $oTipoActiv->getAsistentesText();
	$sactividad = $oTipoActiv->getActividadText();
	
	//periodo
	switch ($sactividad) {
		case 'ca':
		case 'cv':
			$any=  core\ConfigGlobal::any_final_curs('est');
			$inicurs=core\curso_est("inicio",$any,"est");
			$fincurs=core\curso_est("fin",$any,"est");
			break;
		case 'crt':
			$any=  core\ConfigGlobal::any_final_curs('crt');
			$inicurs=core\curso_est("inicio",$any,"crt");
			$fincurs=core\curso_est("fin",$any,"crt");
			break;
	}

	//Actividades a las que afecta
	$aWhere['f_ini'] = "'$inicurs','$fincurs'";
	$aOperador['f_ini'] = 'BETWEEN';

	$aWhere['id_tipo_activ'] = '^'.$id_tipo;
	$aOperador['id_tipo_activ']='~';
	$aWhere['status']=2;
	$aWhere['_ordre']='f_ini';

	// todas las posibles.
	$oGesActividades = new actividades\GestorActividad();
	$cActividades = $oGesActividades->getActividades($aWhere,$aOperador); 
	
	if (core\configGlobal::is_app_installed('actividadplazas')) {
		//primero las que se han pedido
		$cActividadesPreferidas = array();
		//Miro los actuales
		$gesPlazasPeticion = new \actividadplazas\model\GestorPlazaPeticion();
		$cPlazasPeticion = $gesPlazasPeticion->getPlazasPeticion(array('id_nom'=>$id_nom,'tipo'=>$sactividad,'_ordre'=>'orden'));
		$sid_activ = '';
		foreach ($cPlazasPeticion as $oPlazaPeticion) {
			$id_activ = $oPlazaPeticion->getId_activ();
			$oActividad = new actividades\Actividad($id_activ);
			$cActividadesPreferidas[] = $oActividad;
		}

		if (!empty($cActividadesPreferidas)){
			$cActividades = array_merge($cActividadesPreferidas,array('--------'),$cActividades);
		}
	}
	
	
	$propio="t"; //valor por defecto
	$falta="f"; //valor por defecto
	$est_ok="f"; //valor por defecto
	$observ=""; //valor por defecto
}

$oHash = new web\Hash();
$camposForm = 'observ!id_activ';
$oHash->setCamposNo('falta!est_ok');
$a_camposHidden = array(
		'id_nom' => $id_nom,
		'id_activ_old' => $id_activ_old,
		'mod' => $mod,
		'propio' => $propio,
		'plaza' => asistentes\Asistente::PLAZA_ASIGNADA,
		'propietario' => $propietario,
		'go_to'=> $go_to
		);
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<form id="frm_mover" name="frm_mover" action="apps/asistentes/controller/update_3101.php" method="POST">
<?= $oHash->getCamposHtml(); ?>
	<table style="width: 400px">
<?php
echo "<tr><td class=etiqueta>".ucfirst(_("mover a")).":</td><td><select class=contenido id='id_activ' name='id_activ'>";
$i=0;
foreach ($cActividades as $oActividad) {
	$i++;
	$id_activ = 0;
	$nom_activ = '--------------';
	$txt_plazas = '';
	// para el separador '-------'
	if (is_object($oActividad)) {
		$id_activ=$oActividad->getId_activ();
		$nom_activ=$oActividad->getNom_activ();
		$dl_org=$oActividad->getDl_org();
		// plazas libres
		if (core\configGlobal::is_app_installed('actividadplazas')) {
			$concedidas = 0;
			$cActividadPlazas = $gesActividadPlazas->getActividadesPlazas(array('id_dl'=>$id_dl,'id_activ'=>$id_activ));
			foreach ($cActividadPlazas as $oActividadPlazas) {
				$dl_tabla = $oActividadPlazas->getDl_tabla();
				if ($dl_org == $dl_tabla) {
					$concedidas = $oActividadPlazas->getPlazas();
				}
			}
			$ocupadas = $gesAsistentes->getPlazasOcupadasPorDl($id_activ,$mi_dele);
			if ($ocupadas < 0) { // No se sabe
				$libres = '-';
			} else {
				$libres = $concedidas - $ocupadas;
			}
			if (!empty($concedidas)) {
				$txt_plazas = sprintf(_("plazas libres/concedidas: %s/%s"),$libres,$concedidas);
			}
		}
		$txt_creditos = '';
		// creditos
		// por cada ca creo un array con las asignaturas y los créditos.
		$GesActividadAsignaturas = new actividadestudios\GestorActividadAsignaturaDl();
		$asignaturas = $GesActividadAsignaturas->getAsignaturasCa($id_activ);
		$creditos=$oPosiblesCa->contar_creditos($id_nom,$asignaturas);
		if (!empty($creditos)) {
			$txt_creditos = sprintf(_("creditos: %s"),$creditos);
		}
	}
	//$id_activ==$id_pau ? $chk="selected": $chk=""; 
	echo "<option value=$id_activ>$nom_activ $txt_plazas  $txt_creditos</option>";

}
echo "</select></td></tr>";

?>	
<tr><td class=etiqueta><?php echo ucfirst(_("observaciones")); ?></td><td class=contenido>
<textarea id="observ" name="observ" cols="40" rows="5"><?= htmlspecialchars($observ) ?></textarea></td></tr>
</table>
<br><input type="button" id="guardar" name="guardar" onclick="fnjs_guardar('#frm_mover');" value="<?php echo ucfirst(_("guardar")); ?>" align="MIDDLE">
<input type='button' value='<?= _('cancel') ?>' onclick='fnjs_cerrar();' >