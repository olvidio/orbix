<?php
use core\ConfigGlobal;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");

// FIN de  Cabecera global de URL de controlador ********************************


//corrijo el dato que está en config, porque este programa se usará para el próximo curso:
$inicurs_des= date("d/m/Y", mktime(0,0,0,9,1,ConfigGlobal::any_final_curs())) ;

$Qconfirm = (string) \filter_input(INPUT_POST, 'confirm');

if ( $Qconfirm == 'yes') {
	// selecciono las actividades de sg y sr 
	$sql="CREATE TEMP TABLE activ_ctr AS SELECT a.id_activ,d.id_ubi FROM a_actividades a JOIN d_encargados_activ d USING (id_activ) 
		WHERE a.id_tipo_activ::text ~ '.(4|5|7)....' AND a.f_ini>'$inicurs_des' AND a.status=2 AND d.num_orden=0";
	$oDBSt_q=$oDB->query($sql);

	// crear tabla temporal con los sacd de los ctr
	$sql_create="CREATE TEMP TABLE sacd_ctr AS SELECT id_ubi,id_nom 
			FROM t_encargos t JOIN d_tareas_sacd d USING (id_enc) WHERE t.id_tipo_enc::text ~ '^1' AND d.f_fin is null AND d.modo ~ '2|3'
			";
	$oDBSt_q=$oDB->query($sql_create);

	// busco las que no tienen asignado sacd y se lo asigno
	$sql="SELECT id_activ FROM activ_ctr EXCEPT SELECT a.id_activ FROM activ_ctr a LEFT JOIN d_cargos_activ d USING (id_activ) WHERE id_cargo=35";
	$oDBSt_q=$oDB->query($sql);

	// asigno los cargos:
	$i=0;
	$asig=0;
	foreach ($oDBSt_q->fetchAll() as $row) {
		$i++;
		extract($row);
		$sql_sacd="SELECT id_nom FROM activ_ctr JOIN sacd_ctr USING (id_ubi) WHERE id_activ=$id_activ";
		$oDBSt_q_sacd=$oDB->query($sql_sacd);
		if ($oDBSt_q_sacd->rowCount()) {
			$id_nom=$oDBSt_q_sacd->fetchColumn();
			$sql_ins="INSERT INTO d_cargos_activ (id_activ,id_cargo,id_nom,observ) VALUES ($id_activ,35,$id_nom,'auto') ";
			$oDBSt_q_ins=$oDB->query($sql_ins);
			$asig++;
		}
	}
	$sin_asig=$i-$asig;
	echo sprintf(_("Ya está. Se ha asignado %s actividades. Quedan %s por asignar (el centro)."),$asig,$sin_asig);
} else {
    
    $oHash = new Hash();
    $a_camposHidden = array(
        'confirm' => 'yes',
    );
    $oHash->setArraycamposHidden($a_camposHidden);
?>
	<p>Esto asignará el sacd titular del centro a las actividades que tengan un centro encargado.</p>
	<p>Limitado a las actividades de sr y sg a partir de <?= $inicurs_des ?> y marcadas como actuales.</p>
	<p>En el campo observaciones aparece la palabra "auto" para indicar la asignación automática</p>
	<form id="confirm" name="confirm" action="apps/actividadessacd/controller/asignar_sacd_activ.php">
	<?= $oHash->getCamposHtml() ?>
	<input type=button onclick=fnjs_enviar_formulario(this.form) value="continuar">
	</form>
<?php
}
