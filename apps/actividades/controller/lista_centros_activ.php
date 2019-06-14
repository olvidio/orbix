<?php 

/**
*
*@package	delegacion
*@subpackage actividades
*@author	Daniel Serrabou	
*@since		15/3/09.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
use actividadescentro\model\entity\GestorCentroEncargado;
use ubis\model\entity\GestorCentroDl;
use web\DateTimeLocal;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qid_ctr_num = (integer) \filter_input(INPUT_POST, 'id_ctr_num');
$Qa_id_ctr = (array)  \filter_input(INPUT_POST, 'id_ctr', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qinicio = (string) \filter_input(INPUT_POST, 'inicio');
$Qfin = (string) \filter_input(INPUT_POST, 'fin');
$Qyear = (string) \filter_input(INPUT_POST, 'year');
$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');


// valores por defeccto
// desde 40 dias antes de hoy:
if (empty($Qempiezamin)) {
    $QempiezaminIso = date('Y-m-d',mktime(0, 0, 0, date('m'), date('d')-40, date('Y')));
} else {
    $oEmpiezamin = DateTimeLocal::createFromLocal($Qempiezamin);
    $QempiezaminIso = $oEmpiezamin->getIso();
}
// hasta dentro de 9 meses desde hoy.
if (empty($Qempiezamax)) {
    $QempiezamaxIso = date('Y-m-d',mktime(0, 0, 0, date('m')+9, 0, date('Y')));
} else {
    $oEmpiezamax = DateTimeLocal::createFromLocal($Qempiezamax);
    $QempiezamaxIso = $oEmpiezamax->getIso();
}

// periodo.
if (empty($Qperiodo) || $Qperiodo == 'otro') {
    $Qinicio = empty($Qinicio)? $QempiezaminIso : $Qinicio;
    $Qfin = empty($Qfin)? $QempiezamaxIso : $Qfin;
} else {
    $oPeriodo = new web\Periodo();
    $any=empty($Qyear)? date('Y')+1 : $Qyear;
    $oPeriodo->setAny($any);
    $oPeriodo->setPeriodo($Qperiodo);
    $Qinicio = $oPeriodo->getF_ini_iso();
    $Qfin = $oPeriodo->getF_fin_iso();
}
if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
    $aWhereA['f_fin'] = "'$Qinicio','$Qfin'";
    $aOperadorA['f_fin'] = 'BETWEEN';
} else {
    $condicion_periodo = "f_ini BETWEEN '$Qinicio' AND '$Qfin'";
}


$GesCentros = new GestorCentroDl();
if (empty($Qid_ctr_num)) {
	// Todos los ctr de sg
	$aWhere = ['tipo_ctr' => '^s[jm]', '_ordre' => 'nombre_ubi'];
	$aOperador = ['tipo_ctr' => '~'];
	$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
} else {
	// una lista de ctrs.
    $Qa_id_ctr = array_filter($Qa_id_ctr); // para quitar los elementos vacios.
	$aWhere['id_ubi'] = implode(',',$Qa_id_ctr);
	$aOperador['id_ubi'] = 'IN';
	// puede ser que este todo vacio.
	if (empty($Qa_id_ctr)) {
        // Todos los ctr de sg
        $aWhere = ['tipo_ctr' => '^s[jm]', '_ordre' => 'nombre_ubi'];
        $aOperador = ['tipo_ctr' => '~'];
        $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	} else {
        $cCentros = $GesCentros->getCentros($aWhere,$aOperador);
	}
}

$c=0;
$a_centros = [];
$a_actividades=array();
$GesEncargados = new GestorCentroEncargado();
foreach ($cCentros as $oCentro) {
	$c++;
	$id_ubi=$oCentro->getId_ubi();
	$a_centros[$c]=$oCentro->getNombre_ubi();
	// actividades encargadas al centro en el periodo
	$cActividades = $GesEncargados->getActividadesDeCentros($id_ubi,$condicion_periodo);
	// para cada actividad, los otros centros encargados		
	$a=0;
	foreach ($cActividades as $oActividad) {
		$a++;
		$id_activ= $oActividad->getId_activ();
		//$a_actividades[$c][$a]['f_ini']=$oActividad->getF_ini();
		//$a_actividades[$c][$a]['f_fin']=$oActividad->getF_fin();
		$a_actividades[$c][$a]['nom_activ']=$oActividad->getNom_activ();
		$cEncargados = $GesEncargados->getCentrosEncargadosActividad($id_activ);
		$i=0;
		$txt_ctr="";
		foreach ($cEncargados as $oUbi) {
			$i++;
			$id_ctr=$oUbi->getId_ubi();
			$ctr=$oUbi->getNombre_ubi();
			//$num_orden=$oUbi->getNum_orden();
			// no pongo el propio centro
			if ($id_ctr!=$id_ubi) {
				if ($i==1) { $clase="class='responsable'"; } else { $clase=""; }
				$txt_ctr.="<span $clase> $ctr;</span>";
			}
			$a_actividades[$c][$a]["mas_ctr"]=$txt_ctr;
		}
	}
}
	
// ----------------------------- html -----------------------------------
?>
<style>
.responsable {
	text-decoration: underline;
}
</style>
<?php 
$num_ctr=count($a_centros);
for ($c=1;$c<=$num_ctr;$c++) {
	$centro=$a_centros[$c];
	echo "<h3>$centro</h3>";
	echo "<table>";
	if (!empty($a_actividades[$c]) && is_array($a_actividades[$c])) {
		foreach ($a_actividades[$c] as $actividad) {
		/*
		   Ahora (1/5/2012) sin fechas...
		<tr><td><?= $actividad['f_ini'] ?></td><td><?= $actividad['f_fin'] ?></td><td><?= $actividad['nom_activ'] ?></td><td><?= $actividad['mas_ctr'] ?></td></tr>
		*/
		?>
		<tr><td><?= $actividad['nom_activ'] ?></td><td><?= $actividad['mas_ctr'] ?></td></tr>
		<?php
		}
	}
	echo "</table>";
}
?>
