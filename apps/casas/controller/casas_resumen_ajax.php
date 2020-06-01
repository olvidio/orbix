<?php
/**
* Esta página tiene la misión de realizar la llamada a calendario php;
* y lo hace con distintos valores, en función de las páginas anteriores
* 
*@param string $tipo planning-> de un grupo de personas n o agd.
*					p_de_paso-> de un grupo de personas de paso.
*					ctr-> de las personas de un ctr.
*					planning_ctr->  de las personas de un ctr.
*					planning_cdc-> actividades que se realizan en una casa del a dl.
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************

use actividades\model\entity\GestorActividad;
use casas\model\entity\GestorUbiGasto;
use casas\model\entity\Ingreso;
use ubis\model\entity\GestorCasaDl;
use ubis\model\entity\GestorCasaPeriodo;
use web\DateTimeLocal;
use web\Periodo;
use ubis\model\entity\GestorCentroEllas;
use casas\model\entity\GestorGrupoCasa;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qtipo = (string) \filter_input(INPUT_POST, 'tipo');
$Qsfsv = (string) \filter_input(INPUT_POST, 'sfsv');
$Qque = (string) \filter_input(INPUT_POST, 'que');

/**
 * devuelve el dia anterior a las 20:00 de una fecha.
 * 
 * @param string $iso_ini
 * @param integer $delta_h horas a quitar a la fecha
 * @return string
 */
function inicio_periodo($iso_ini,$delta_h=4) {
    $oInicio = DateTimeLocal::createFromFormat('Ymd', $iso_ini);
    // dia anterior a las 20:00h
    $interval = 'PT'.$delta_h.'H';
    $oInicio->sub(new DateInterval($interval));
    return $oInicio->getIsoTime();
}
/**
 * devuelve el dia siguiente a las 10:00 de una fecha.
 * 
 * @param string $iso_fin
 * @param integer $delta_h  horas a añadir a la fecha
 * @return string
 */
function fin_periodo($iso_fin,$delta_h=10) {
    $oFin = DateTimeLocal::createFromFormat('Ymd', $iso_fin);
    // dia siguiente a las 10:00h
    $interval = 'PT1DT'.$delta_h.'H';
    $oFin->add(new DateInterval($interval));
    return $oFin->getIsoTime();
}
/**
 * Función para calcular los dias que ocupa cada dl según los
 * los periodos definidos para sf y sv.
 *
 * param array $aPeriodos array con las fechas de los periodos: f_inicio,f_fin,seccion.
 * return array $aOcupacion con dos claves 1=>dias de ocupacion sv, 2=>sf.
 */
function reparto($aPeriodos) {
    $aOcupacion = [];
	$aOcupacion[1] = 0;
	$aOcupacion[2] = 0;
	$aOcupacion[3] = 0;
	foreach ($aPeriodos as $row) {
		$oInicio = DateTime::createFromFormat('Ymd', $row['iso_ini']);
		$oFin = DateTime::createFromFormat('Ymd', $row['iso_fin']);
		$interval = $oInicio->diff($oFin);
		$num_dias = $interval->format('%a');
		$sfsv = $row['sfsv'];
		$aOcupacion[$sfsv]+=$num_dias;
	}
	return $aOcupacion;
}

/**
 * Función para calcular los dias que ocupa una actividad correspondientes a 
 * los periodos definidos para sf y sv.
 *
 * param array $aPeriodos array con las fechas de los periodos: f_inicio,f_fin,seccion.
 * 					considero que el periodo empieza a las 20:00h del dia anterior a la fecha de inicio.
 * 					considero que el periodo termina a las 10:00h de la fecha de fin.
 * param object $oActividad el objeto actividad del que queremos medir la ocupacion.
 * param date	$inicio fecha de inicio del intervalo que queremos medir (para las actividades que están en la frontera).
 * param date	$fin fecha de fin del intervalo que queremos medir (para las actividades que están en la frontera).
 * return array $aOcupacion con dos claves 1=>dias de ocupacion sv, 2=>sf.
 */
function dias_ocupacion($aPeriodos,$oActividad,$oIniTot,$oFinTot) {
	$oF_ini = $oActividad->getF_ini();
	$oF_fin = $oActividad->getF_fin();
	$h_ini = $oActividad->getH_ini();
	$h_fin = $oActividad->getH_fin();
	$nom_activ = $oActividad->getNom_activ();
	
	$num_dias = $oActividad->getDuracionReal();

	// si la actividad empieza antes del inicio, cojo como valor del inicio de la actividad, el valor de inicio del periodo.
	$oIniTot->setTime(00,00,00);
	$oFinTot->setTime(23,59,59);

	if (empty($h_ini)) {
	    $ini_h = 21;
	    $ini_m = 0;
	    $ini_s = 0;
	} else {
	    list($ini_h, $ini_m, $ini_s) = explode(":", $h_ini);
	}
	if (empty($h_fin)) {
	    $fin_h = 21;
	    $fin_m = 0;
	    $fin_s = 0;
	} else {
	    list($fin_h, $fin_m, $fin_s) = explode(":", $h_fin);
	}
	$oF_ini->setTime($ini_h, $ini_m, $ini_s);
	$oF_fin->setTime($fin_h, $fin_m, $fin_s);

	if ($oF_ini < $oIniTot) {
		$isoActivIni = $oIniTot->format('YmdHis');
		$oLocal = new DateTimeLocal();
		$oLocal->setDateTime($oIniTot);
		$num_dias = $oLocal->duracion($oF_fin);
	} else {
		$isoActivIni = $oF_ini->format('YmdHis');
	}
	// lo mismo para el final:
	if ($oF_fin > $oFinTot) {
		$isoActivFin = $oFinTot->format('YmdHis');
		$oLocal = new DateTimeLocal();
		$oLocal->setDateTime($oF_ini);
		$num_dias = $oLocal->duracion($oFinTot);
	}	else {
		$isoActivFin = $oF_fin->format('YmdHis');
	}
	//echo "<br>A: $nom_activ [$isoActivIni][$isoActivFin]<br>";
	// miro si la actividad empieza y termina en el mismo periodo.
	$p=0;
	$aOcupacion = [];
	$aOcupacion[1] = 0;
	$aOcupacion[2] = 0;
	foreach ($aPeriodos as $row) {
		$iniPeriodo = inicio_periodo($row['iso_ini']);
		$finPeriodo = fin_periodo($row['iso_fin']);
		if ($isoActivIni <= $finPeriodo && $isoActivIni >= $iniPeriodo) {
			if ($isoActivFin >= $iniPeriodo && $isoActivFin <= $finPeriodo) { 
				//empieza y termina en el periodo.
				$sfsv = $aPeriodos[$p]['sfsv'];
				$aOcupacion[$sfsv]=$num_dias;
				break;
			} else {
				if ($isoActivFin > $finPeriodo) { //salta de periodo
					$iniPeriodoNext = inicio_periodo($aPeriodos[$p+1]['iso_ini']);
					$finPeriodoNext = fin_periodo($aPeriodos[$p+1]['iso_fin']);
					if ($isoActivFin > $finPeriodoNext) {  //salta de periodo
						echo '<br>'.sprintf(_("OJO: %s ocupa más de 2 periodos. Lo calcula bien"),$nom_activ);
						$iniPeriodoNext = inicio_periodo($aPeriodos[$p+2]['iso_ini']);
						$finPeriodoNext = fin_periodo($aPeriodos[$p+2]['iso_fin']);
						if ($isoActivFin > $finPeriodoNext) {  //salta de periodo
							echo '<br>'.sprintf(_("OJO: %s ocupa más de 3 periodos. No calcula bien"),$nom_activ);
							break;
						}
						// días hasta aquí:
						$finPeriodoLimit = fin_periodo($aPeriodos[$p+1]['iso_fin'],0);
						$oFinPer = new DateTimeLocal($finPeriodoLimit);
						//$oLocal = new DateTimeLocal();
						//$oLocal->setDateTime($oF_ini);
						$num_dias = $oF_ini->duracion($oFinPer);
						$sfsv = $aPeriodos[$p+1]['sfsv'];
						$aOcupacion[$sfsv]=$num_dias;
						// días siguientes:
						$iniPeriodoNextLimit = inicio_periodo($aPeriodos[$p+2]['iso_ini'],0);
						$oIniPer = new DateTimeLocal($iniPeriodoNextLimit);
						//$oLocal = new DateTimeLocal();
						//$oLocal->setDateTime($oIniPer);
						$num_dias = $oIniPer->duracion($oF_fin);
						$sfsv = $aPeriodos[$p+2]['sfsv'];
						$aOcupacion[$sfsv]=$num_dias;
						//break; // debe seguir contando los dias del periodo anterior.
					}
					// días hasta aquí:
                    $finPeriodoLimit = fin_periodo($aPeriodos[$p]['iso_fin'],0);
					$oFinPer = new DateTimeLocal($finPeriodoLimit);
					//$oLocal = new DateTimeLocal();
				    //$oLocal->setDateTime($oF_ini);
					$num_dias = $oF_ini->duracion($oFinPer);
					$sfsv = $aPeriodos[$p]['sfsv'];
					$aOcupacion[$sfsv]=$num_dias;
					// días siguientes:
                    $iniPeriodoNextLimit = inicio_periodo($aPeriodos[$p+1]['iso_ini'],0);
					$oIniPer = new DateTimeLocal($iniPeriodoNextLimit);
					//$oLocal = new DateTimeLocal();
					//$oLocal->setDateTime($oIniPer);
					$num_dias = $oIniPer->duracion($oF_fin);
					$sfsv = $aPeriodos[$p+1]['sfsv'];
					$aOcupacion[$sfsv]=$num_dias;
				}
				break;
			}
		} else {
			$p++;
			continue;
		}
	}
	//echo "ocpuacion: ";
	//print_r($aOcupacion);
	return $aOcupacion;
}

$aWhere=array();
$aOperador=array();
switch ($_POST['cdc_sel']) {
	case 1:
		$aWhere['sv']='t';
		$aWhere['sf']='t';
		break;
	case 2:
		$aWhere['sv']='f';
		$aWhere['sf']='t';
		break;
	case 3: // casas comunes: cdr + dlb + sf +sv
		$aWhere['sv']='t';
		$aWhere['sf']='t';
		$aWhere['tipo_ubi']='cdcdl';
		$aWhere['tipo_casa']='cdc|cdr';
		$aOperador['tipo_casa']='~';
		break;
	case 4:
		$aWhere['sv']='t';
		break;
	case 5:
		$aWhere['sf']='t';
		break;
	case 6:
		$aWhere['sf']='t';
		// también los centros qeu son como cdc
		$GesCentrosSf = new GestorCentroEllas();
		$cCentrosSf = $GesCentrosSf->getCentros(array('cdc'=>'t','_ordre'=>'nombre_ubi')); 
		break;
	case 9:
		// posible selección múltiple de casas
		if (!empty($_POST['id_cdc'])) {
			$aWhere['id_ubi'] = '^'. implode('$|^',$_POST['id_cdc']) .'$';
			$aOperador['id_ubi'] = '~';
		}
		break;
}
$aWhere['_ordre']='nombre_ubi';
$GesCasaDl = new GestorCasaDl();
$cCasasDl = $GesCasaDl->getCasas($aWhere,$aOperador);

if ($_POST['cdc_sel']==6) { //añado los ctr de sf
	foreach ($cCentrosSf as $oCentroSf) {
		array_push($cCasasDl, $oCentroSf);
	}	
}

if (empty($Qque)) {
    $Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
    $Qyear = (string) \filter_input(INPUT_POST, 'year');
    $Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
    $Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
    
    // periodo.
    $oPeriodo = new Periodo();
    $oPeriodo->setDefaultAny('next');
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);
    
    $oInicio = $oPeriodo->getF_ini();
    $oFin = $oPeriodo->getF_fin();
    $inicioIso = $oPeriodo->getF_ini_iso();
    $finIso = $oPeriodo->getF_fin_iso();
    

    $tot = [];
    $tot[1]['dias'] = 0; 
    $tot[2]['dias'] = 0;
    $tot[1]['asist_prev'] = 0;
    $tot[2]['asist_prev'] = 0;
    $tot[1]['asist'] = 0;
    $tot[2]['asist'] = 0;
    $tot[1]['in_prev_acu'] = 0;
    $tot[2]['in_prev_acu'] = 0;
    $tot[1]['in_acu'] = 0;
    $tot[2]['in_acu'] = 0;
    $tot[0]['gasto'] = 0;
    $tot[1]['gasto'] = 0;
    $tot[2]['gasto'] = 0;
    $tot[1]['aportacion'] = 0;
    $tot[2]['aportacion'] = 0;
    $tot[1]['superavit'] = 0;
    $tot[2]['superavit'] = 0;

    $a_resumen = [];
    foreach ($cCasasDl as $oCasaDl) {
        $out = [];
        $in = [];
        $id_ubi=$oCasaDl->getId_ubi();
        $nombre_ubi=$oCasaDl->getNombre_ubi();
        // Caso especial para Grupos: Los gastos son comunes a todas las casas.
        $gesGrupoCasas = new GestorGrupoCasa();
        $nom_tabla = $gesGrupoCasas->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_ubi_padre = $id_ubi OR id_ubi_hijo= $id_ubi"; 
        $cGrupoCasas = $gesGrupoCasas->getGrupoCasasQuery($sQuery);
        $id_ubi_hijo = 0;
        $id_ubi_padre = 0;
        foreach($cGrupoCasas as $oGrupoCasa) {
            $id_ubi_padre = $oGrupoCasa->getId_ubi_padre();
            $id_ubi_hijo = $oGrupoCasa->getId_ubi_hijo();
            
        }

        $GesPeriodos = new GestorCasaPeriodo();
        $aPeriodos = $GesPeriodos->getArrayCasaPeriodos($id_ubi,$oInicio,$oFin);

        $a_resumen[$id_ubi][0]['nom'] = $nombre_ubi;

        $a_resumen[$id_ubi][1]['dias'] = 0;
        $a_resumen[$id_ubi][2]['dias'] = 0;
        $a_resumen[$id_ubi][1]['asist_prev'] = 0;
        $a_resumen[$id_ubi][2]['asist_prev'] = 0;
        $a_resumen[$id_ubi][1]['asist'] = 0;
        $a_resumen[$id_ubi][2]['asist'] = 0;
        $a_resumen[$id_ubi][1]['in_prev_acu'] = 0; 
        $a_resumen[$id_ubi][2]['in_prev_acu'] = 0; 
        $a_resumen[$id_ubi][1]['in_acu'] = 0; 
        $a_resumen[$id_ubi][2]['in_acu'] = 0; 
        $a_resumen[$id_ubi][1]['gasto'] = 0; 
        $a_resumen[$id_ubi][2]['gasto'] = 0; 

        $a=0;
        $aWhere=array();
        $aOperador=array();
        $aWhere['f_ini']=$finIso;
        $aOperador['f_ini']='<=';
        $aWhere['f_fin']=$inicioIso;
        $aOperador['f_fin']='>=';
        $aWhere['id_ubi']=$id_ubi;
        $aWhere['status']=4;
        $aOperador['status']='<';
        $aWhere['_ordre']='f_ini';
        $oGesActividades = new GestorActividad();
        $cActividades = $oGesActividades->getActividades($aWhere,$aOperador);
        foreach ($cActividades as $oActividad) {
            $in = [];
            $id_activ = $oActividad->getId_activ();
            $oInicio = $oActividad->getF_ini();
            $oFin = $oActividad->getF_fin();
            $nom_activ = $oActividad->getNom_activ();
            
            $num_dias_act = $oActividad->getDuracion();
            $num_dias = $oActividad->getDuracionEnPeriodo($oInicio,$oFin);
            $num_dias_real = $oActividad->getDuracionReal();
            $factor_dias = ($num_dias/$num_dias_real);

            // saber a que periodo pertenece (sf/sv).
            $a_ocupacion= dias_ocupacion($aPeriodos,$oActividad,$oInicio,$oFin);
            $factor = ($num_dias_act-$num_dias_real)/$num_dias_real;

            $a_ocupacion[1] = round($a_ocupacion[1]*(1+$factor),1);
            $a_ocupacion[2] = round($a_ocupacion[2]*(1+$factor),1);

            $a_resumen[$id_ubi][1]['dias'] += $a_ocupacion[1]; // sv
            $a_resumen[$id_ubi][2]['dias'] += $a_ocupacion[2]; // sf

            $oIngreso = new Ingreso(array('id_activ'=>$id_activ));
            $num_asistentes_previstos=$oIngreso->getNum_asistentes_previstos();
            $num_asistentes=$oIngreso->getNum_asistentes();
            $ingresos_previstos=$factor_dias*$oIngreso->getIngresos_previstos();
            $ingresos=$factor_dias*$oIngreso->getIngresos();

            $a_resumen[$id_ubi][1]['asist_prev']+=$num_asistentes_previstos*$a_ocupacion[1]; // sv
            $a_resumen[$id_ubi][2]['asist_prev']+=$num_asistentes_previstos*$a_ocupacion[2]; // sf

            $a_resumen[$id_ubi][1]['asist']+=$num_asistentes*$a_ocupacion[1]; // sv
            $a_resumen[$id_ubi][2]['asist']+=$num_asistentes*$a_ocupacion[2]; // sf

            if (($a_ocupacion[1]+$a_ocupacion[2])>0) {
                $in[1] = round(($ingresos/($a_ocupacion[1]+$a_ocupacion[2]))*$a_ocupacion[1],2); // sv
                $in[2] = round(($ingresos/($a_ocupacion[1]+$a_ocupacion[2]))*$a_ocupacion[2],2); // sf
                $a_resumen[$id_ubi][1]['in_prev_acu']+=round(($ingresos_previstos/($a_ocupacion[1]+$a_ocupacion[2]))*$a_ocupacion[1],2); // sv
                $a_resumen[$id_ubi][2]['in_prev_acu']+=round(($ingresos_previstos/($a_ocupacion[1]+$a_ocupacion[2]))*$a_ocupacion[2],2); // sf
                $a_resumen[$id_ubi][1]['in_acu']+=$in[1];
                $a_resumen[$id_ubi][2]['in_acu']+=$in[2];
            } else {
                $in[1] = '';
                $in[2] = '';
            }
            $a_resumen[$id_ubi][0]['detalles'][]="<tr><td>$nom_activ</td><td>$a_ocupacion[1]</td><td>$a_ocupacion[2]</td><td>$in[1]</td><td>$in[2]</td></tr>";

            $a++;
        } 
        // Si no hay actividades:
        if ($a < 1) {
            $a_resumen[$id_ubi][0]['detalles'][0]="<tr><td>"._("no tiene actividades")."</td></tr>";
        }
        if (($var = $a_resumen[$id_ubi][1]['dias']+$a_resumen[$id_ubi][2]['dias']) > 0) {
            $a_resumen[$id_ubi][1]['dias%'] = round($a_resumen[$id_ubi][1]['dias']/$var*100,2);
            $a_resumen[$id_ubi][2]['dias%'] = round($a_resumen[$id_ubi][2]['dias']/$var*100,2);
        } else {
            $a_resumen[$id_ubi][1]['dias%'] = '-';
            $a_resumen[$id_ubi][2]['dias%'] = '-';
        }
        if (($var = $a_resumen[$id_ubi][1]['asist_prev']+$a_resumen[$id_ubi][2]['asist_prev']) > 0) {
            $a_resumen[$id_ubi][1]['asist_prev%'] = round($a_resumen[$id_ubi][1]['asist_prev']/$var*100,2);
            $a_resumen[$id_ubi][2]['asist_prev%'] = round($a_resumen[$id_ubi][2]['asist_prev']/$var*100,2);
        } else {
            $a_resumen[$id_ubi][1]['asist_prev%'] = '-';
            $a_resumen[$id_ubi][2]['asist_prev%'] = '-';
        }
        if (($var = $a_resumen[$id_ubi][1]['asist']+$a_resumen[$id_ubi][2]['asist']) > 0) {
            $a_resumen[$id_ubi][1]['asist%'] = round($a_resumen[$id_ubi][1]['asist']/$var*100,2);
            $a_resumen[$id_ubi][2]['asist%'] = round($a_resumen[$id_ubi][2]['asist']/$var*100,2);
        } else {
            $a_resumen[$id_ubi][1]['asist%'] = '-';
            $a_resumen[$id_ubi][2]['asist%'] = '-';
        }
        if (($var = $a_resumen[$id_ubi][1]['in_prev_acu']+$a_resumen[$id_ubi][2]['in_prev_acu']) > 0) {
            $a_resumen[$id_ubi][1]['in_prev_acu%'] = round($a_resumen[$id_ubi][1]['in_prev_acu']/$var*100,2);
            $a_resumen[$id_ubi][2]['in_prev_acu%'] = round($a_resumen[$id_ubi][2]['in_prev_acu']/$var*100,2);
        } else {
            $a_resumen[$id_ubi][1]['in_prev_acu%'] = '-';
            $a_resumen[$id_ubi][2]['in_prev_acu%'] = '-';
        }
        if (($var = $a_resumen[$id_ubi][1]['in_acu']+$a_resumen[$id_ubi][2]['in_acu']) > 0) {
            $a_resumen[$id_ubi][1]['in_acu%'] = round($a_resumen[$id_ubi][1]['in_acu']/$var*100,2);
            $a_resumen[$id_ubi][2]['in_acu%'] = round($a_resumen[$id_ubi][2]['in_acu']/$var*100,2);
        } else {
            $a_resumen[$id_ubi][1]['in_acu%'] = '-';
            $a_resumen[$id_ubi][2]['in_acu%'] = '-';
        }

        // Gastos ------
        $GesGastos = new GestorUbiGasto();
        // tipo: 1=sv, 2=sf, 3=gastos
        $a_resumen[$id_ubi][1]['aportacion']=$GesGastos->getSumaGastos($id_ubi,1,$oInicio,$oFin);
        $a_resumen[$id_ubi][2]['aportacion']=$GesGastos->getSumaGastos($id_ubi,2,$oInicio,$oFin);
        $a_resumen[$id_ubi][0]['gasto']=$GesGastos->getSumaGastos($id_ubi,3,$oInicio,$oFin);

        $a_repartoGastos = reparto($aPeriodos);
        if (($var = $a_repartoGastos[1]+$a_repartoGastos[2]) > 0) {
            $a_resumen[$id_ubi][1]['gasto%'] = round($a_repartoGastos[1]/$var*100,2);
            $a_resumen[$id_ubi][2]['gasto%'] = round($a_repartoGastos[2]/$var*100,2);
            $a_resumen[$id_ubi][1]['gasto'] = round($a_resumen[$id_ubi][0]['gasto']*$a_resumen[$id_ubi][1]['gasto%']/100,2);
            $a_resumen[$id_ubi][2]['gasto'] = round($a_resumen[$id_ubi][0]['gasto']*$a_resumen[$id_ubi][2]['gasto%']/100,2);

            if ($id_ubi==$id_ubi_hijo && array_key_exists($id_ubi_padre,$a_resumen)) {
                $in[1] = ($a_resumen[$id_ubi_padre][1]['aportacion']+$a_resumen[$id_ubi_padre][1]['in_acu']+$a_resumen[$id_ubi][1]['in_acu']);
                $out[1] = round($a_resumen[$id_ubi_padre][1]['gasto%']*$a_resumen[$id_ubi_padre][0]['gasto']/100,2);
                $in[2] = ($a_resumen[$id_ubi_padre][2]['aportacion']+$a_resumen[$id_ubi_padre][2]['in_acu']+$a_resumen[$id_ubi][2]['in_acu']);
                $out[2] = round($a_resumen[$id_ubi_padre][2]['gasto%']*$a_resumen[$id_ubi_padre][0]['gasto']/100,2);
                $a_resumen[$id_ubi_padre][1]['superavit'] =  $in[1] - $out[1] ;
                $a_resumen[$id_ubi_padre][2]['superavit'] =  $in[2] - $out[2] ;
                $a_resumen[$id_ubi][1]['superavit'] = '';
                $a_resumen[$id_ubi][2]['superavit'] = '';
            } else {
                $in[1] = ($a_resumen[$id_ubi][1]['aportacion']+$a_resumen[$id_ubi][1]['in_acu']);
                $out[1] = round($a_resumen[$id_ubi][1]['gasto%']*$a_resumen[$id_ubi][0]['gasto']/100,2);
                $in[2] = ($a_resumen[$id_ubi][2]['aportacion']+$a_resumen[$id_ubi][2]['in_acu']);
                $out[2] = round($a_resumen[$id_ubi][2]['gasto%']*$a_resumen[$id_ubi][0]['gasto']/100,2);
                $a_resumen[$id_ubi][1]['superavit'] =  $in[1] - $out[1] ;
                $a_resumen[$id_ubi][2]['superavit'] =  $in[2] - $out[2] ;
            }
        } else {
            $a_resumen[$id_ubi][1]['gasto%'] = '-';
            $a_resumen[$id_ubi][2]['gasto%'] = '-';
            $a_resumen[$id_ubi][1]['superavit'] = '0';
            $a_resumen[$id_ubi][2]['superavit'] = '0';
        }

        // Sumas ---------
        $tot[1]['dias'] += $a_resumen[$id_ubi][1]['dias'];
        $tot[2]['dias'] += $a_resumen[$id_ubi][2]['dias'];
        $tot[1]['asist_prev'] += $a_resumen[$id_ubi][1]['asist_prev'];
        $tot[2]['asist_prev'] += $a_resumen[$id_ubi][2]['asist_prev'];
        $tot[1]['asist'] += $a_resumen[$id_ubi][1]['asist'];
        $tot[2]['asist'] += $a_resumen[$id_ubi][2]['asist'];
        $tot[1]['in_prev_acu'] += $a_resumen[$id_ubi][1]['in_prev_acu'];
        $tot[2]['in_prev_acu'] += $a_resumen[$id_ubi][2]['in_prev_acu'];
        $tot[1]['in_acu'] += $a_resumen[$id_ubi][1]['in_acu'];
        $tot[2]['in_acu'] += $a_resumen[$id_ubi][2]['in_acu'];
        $tot[0]['gasto'] += $a_resumen[$id_ubi][0]['gasto'];
        $tot[1]['gasto'] += $a_resumen[$id_ubi][1]['gasto'];
        $tot[2]['gasto'] += $a_resumen[$id_ubi][2]['gasto'];
        $tot[1]['aportacion'] += $a_resumen[$id_ubi][1]['aportacion'];
        $tot[2]['aportacion'] += $a_resumen[$id_ubi][2]['aportacion'];
        $tot[1]['superavit'] += $a_resumen[$id_ubi][1]['superavit'];
        $tot[2]['superavit'] += $a_resumen[$id_ubi][2]['superavit'];

    }

    // %%% Totales
    if (($var = $tot[1]['dias']+$tot[2]['dias']) > 0) {
        $tot[1]['dias%'] = round($tot[1]['dias']/$var*100,2);
        $tot[2]['dias%'] = round($tot[2]['dias']/$var*100,2);
    } else {
        $tot[1]['dias%'] = '-';
        $tot[2]['dias%'] = '-';
    }
    if (($var = $tot[1]['asist_prev']+$tot[2]['asist_prev']) > 0) {
        $tot[1]['asist_prev%'] = round($tot[1]['asist_prev']/$var*100,2);
        $tot[2]['asist_prev%'] = round($tot[2]['asist_prev']/$var*100,2);
    } else {
        $tot[1]['asist_prev%'] = '-';
        $tot[2]['asist_prev%'] = '-';
    }
    if (($var = $tot[1]['asist']+$tot[2]['asist']) > 0) {
        $tot[1]['asist%'] = round($tot[1]['asist']/$var*100,2);
        $tot[2]['asist%'] = round($tot[2]['asist']/$var*100,2);
    } else {
        $tot[1]['asist%'] = '-';
        $tot[2]['asist%'] = '-';
    }
    if (($var = $tot[1]['in_prev_acu']+$tot[2]['in_prev_acu']) > 0) {
        $tot[1]['in_prev_acu%'] = round($tot[1]['in_prev_acu']/$var*100,2);
        $tot[2]['in_prev_acu%'] = round($tot[2]['in_prev_acu']/$var*100,2);
    } else {
        $tot[1]['in_prev_acu%'] = '-';
        $tot[2]['in_prev_acu%'] = '-';
    }
    if (($var = $tot[1]['in_acu']+$tot[2]['in_acu']) > 0) {
        $tot[1]['in_acu%'] = round($tot[1]['in_acu']/$var*100,2);
        $tot[2]['in_acu%'] = round($tot[2]['in_acu']/$var*100,2);
    } else {
        $tot[1]['in_acu%'] = '-';
        $tot[2]['in_acu%'] = '-';
    }
    if (($var = $tot[1]['gasto']+$tot[2]['gasto']) > 0) {
        $tot[1]['gasto%'] = round($tot[1]['gasto']/$var*100,2);
        $tot[2]['gasto%'] = round($tot[2]['gasto']/$var*100,2);
    } else {
        $tot[1]['gasto%'] = '-';
        $tot[2]['gasto%'] = '-';
    }

    //------------- Mostrar -------------
    ?>
    <table border=1>
    <tr><th></th><th></th><th><?= _("días ocupados") ?></th><th>%</th><th><?= _("nº días x nº asistentes (previsto)") ?></th><th>%</th>
    <th><?= _("nº días x nº asistentes (real)") ?></th><th>%</th>
    <th><?= _("ingresos previstos acumulados") ?></th><th>%</th>
    <th><?= _("ingresos reales acumulados") ?></th><th>%</th>
    <th><?= _("gastos reales") ?></th><th>%</th><th><?= _("aportación real dl") ?></th><th><?= _("superávit") ?></th><tr>
    <?php
    foreach ($a_resumen as $id_ubi => $row) {
        echo "<tr id=detalles_".$id_ubi."_0 style='display: none;'><td colspan=16><table>";
        echo "<tr><th>"._("actividad")."</th><th>"._("ocupación sv")."</th><th>"._("ocupación sf")."</th><th>"._("ingresos sv")."</th><th>"._("ingresos sf")."</th></tr>";
        foreach ($a_resumen[$id_ubi][0]['detalles'] as $fila_detalle) {
            echo $fila_detalle;
        }
        echo "</table></td></tr>";
    ?>
        <tr><td><?= $row[0]['nom'] ?></td><td>dlb</td>
            <td class='derecha'><?= $row[1]['dias'] ?></td><td class='derecha'><?= $row[1]['dias%'] ?></td>
            <td class='derecha'><?= $row[1]['asist_prev'] ?></td><td class='derecha'><?= $row[1]['asist_prev%'] ?></td>
            <td class='derecha'><?= $row[1]['asist'] ?></td><td class='derecha'><?= $row[1]['asist%'] ?></td>
            <td class='derecha'><?= $row[1]['in_prev_acu'] ?><td class='derecha'><?= $row[1]['in_prev_acu%'] ?></td>
            <td class='derecha'><?= $row[1]['in_acu'] ?></td><td class='derecha'><?= $row[1]['in_acu%'] ?></td>
            <td class='derecha'><?= $row[1]['gasto'] ?></td><td class='derecha'><?= $row[1]['gasto%'] ?></td>
            <td class='derecha'><?= $row[1]['aportacion'] ?></td><td class='derecha'><?= $row[1]['superavit'] ?></td>

            </tr>
    <tr><td><span id='span_detalles_<?= $id_ubi ?>_0' class=link onclick="fnjs_detalles(<?= $id_ubi ?>,0)" >
    			<p><?= _("mostrar detalles") ?></p>
    			<p style="display: none"><?= _("ocultar detalles") ?></p>
    		</span></td>
    		<td>dlbf</td>
            <td class='derecha'><?= $row[2]['dias'] ?></td><td class='derecha'><?= $row[2]['dias%'] ?></td>
            <td class='derecha'><?= $row[2]['asist_prev'] ?></td><td class='derecha'><?= $row[2]['asist_prev%'] ?></td>
            <td class='derecha'><?= $row[2]['asist'] ?></td><td class='derecha'><?= $row[2]['asist%'] ?></td>
            <td class='derecha'><?= $row[2]['in_prev_acu'] ?></td><td class='derecha'><?= $row[2]['in_prev_acu%'] ?></td>
            <td class='derecha'><?= $row[2]['in_acu'] ?></td><td class='derecha'><?= $row[2]['in_acu%'] ?></td>
            <td class='derecha'><?= $row[2]['gasto'] ?></td><td class='derecha'><?= $row[2]['gasto%'] ?></td>
            <td class='derecha'><?= $row[2]['aportacion'] ?></td><td class='derecha'><?= $row[2]['superavit'] ?></td>

            </tr>
        <tr><td></td><td>totales</td>
            <td class='derecha'><?= $row[1]['dias'] + $row[2]['dias'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= $row[1]['asist_prev'] + $row[2]['asist_prev'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= $row[1]['asist'] + $row[2]['asist'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= round(($row[1]['in_prev_acu'] + $row[2]['in_prev_acu']),2) ?></td><td class='derecha'></td>
            <td class='derecha'><?= round(($row[1]['in_acu'] + $row[2]['in_acu']),2) ?></td><td class='derecha'></td>
            <td class='derecha'><?= $row[0]['gasto'] ?></td><td class='derecha'></td>
            <td></td><td class='derecha'><?= round(($row[1]['superavit'] + $row[2]['superavit']),2) ?></td>

            </tr>
    <?php
    }
    // ------- TOTALES ------------
    ?>
        <tr><td rowspan=3><?= _("totales") ?></td><td>dlb</td>
            <td class='derecha'><?= $tot[1]['dias'] ?></td><td class='derecha'><?= $tot[1]['dias%'] ?></td>
            <td class='derecha'><?= $tot[1]['asist_prev'] ?></td><td class='derecha'><?= $tot[1]['asist_prev%'] ?></td>
            <td class='derecha'><?= $tot[1]['asist'] ?></td><td class='derecha'><?= $tot[1]['asist%'] ?></td>
            <td class='derecha'><?= $tot[1]['in_prev_acu'] ?><td class='derecha'><?= $tot[1]['in_prev_acu%'] ?></td>
            <td class='derecha'><?= $tot[1]['in_acu'] ?></td><td class='derecha'><?= $tot[1]['in_acu%'] ?></td>
            <td class='derecha'><?= $tot[1]['gasto'] ?></td><td class='derecha'><?= $tot[1]['gasto%'] ?></td>
            <td class='derecha'><?= $tot[1]['aportacion'] ?></td><td class='derecha'><?= $tot[1]['superavit'] ?></td>

            </tr>
        <tr><td>dlbf</td>
            <td class='derecha'><?= $tot[2]['dias'] ?></td><td class='derecha'><?= $tot[2]['dias%'] ?></td>
            <td class='derecha'><?= $tot[2]['asist_prev'] ?></td><td class='derecha'><?= $tot[2]['asist_prev%'] ?></td>
            <td class='derecha'><?= $tot[2]['asist'] ?></td><td class='derecha'><?= $tot[2]['asist%'] ?></td>
            <td class='derecha'><?= $tot[2]['in_prev_acu'] ?></td><td class='derecha'><?= $tot[2]['in_prev_acu%'] ?></td>
            <td class='derecha'><?= $tot[2]['in_acu'] ?></td><td class='derecha'><?= $tot[2]['in_acu%'] ?></td>
            <td class='derecha'><?= $tot[2]['gasto'] ?></td><td class='derecha'><?= $tot[2]['gasto%'] ?></td>
            <td class='derecha'><?= $tot[2]['aportacion'] ?></td><td class='derecha'><?= $tot[2]['superavit'] ?></td>

            </tr>
        <tr><td>totales</td>
            <td class='derecha'><?= $tot[1]['dias'] + $tot[2]['dias'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= $tot[1]['asist_prev'] + $tot[2]['asist_prev'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= $tot[1]['asist'] + $tot[2]['asist'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= round(($tot[1]['in_prev_acu'] + $tot[2]['in_prev_acu']),2) ?></td><td class='derecha'></td>
            <td class='derecha'><?= round(($tot[1]['in_acu'] + $tot[2]['in_acu']),2) ?></td><td class='derecha'></td>
            <td class='derecha'><?= $tot[0]['gasto'] ?></td><td class='derecha'></td>
            <td></td><td class='derecha'><?= round(($tot[1]['superavit'] + $tot[2]['superavit']),2) ?></td>

            </tr>

    </table>
    <?php
} else {
	// sólo veo 5 años.
	$a_anys = [];
    $any_prox = date('Y') + 1;
    //$any_prox = date('Y');
    for ($i=0; $i<6; $i++) {
        $a_anys[] = $any_prox - $i;
    }

    foreach ($cCasasDl as $oCasaDl) {
        $id_ubi=$oCasaDl->getId_ubi();
        $nombre_ubi=$oCasaDl->getNombre_ubi();
        // Caso especial para Grupos: Los gastos son comunes a todas las casas.
        $gesGrupoCasas = new GestorGrupoCasa();
        $nom_tabla = $gesGrupoCasas->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_ubi_padre = $id_ubi OR id_ubi_hijo= $id_ubi"; 
        $cGrupoCasas = $gesGrupoCasas->getGrupoCasasQuery($sQuery);
        $id_ubi_padre = 0;
        $id_ubi_hijo = 0;
        foreach($cGrupoCasas as $oGrupoCasa) {
            $id_ubi_padre = $oGrupoCasa->getId_ubi_padre();
            $id_ubi_hijo = $oGrupoCasa->getId_ubi_hijo();
            
        }

        foreach ($a_anys as $any) {
            $tot[$any][1]['dias'] = 0; 
            $tot[$any][2]['dias'] = 0;
            $tot[$any][1]['asist_prev'] = 0;
            $tot[$any][2]['asist_prev'] = 0;
            $tot[$any][1]['asist'] = 0;
            $tot[$any][2]['asist'] = 0;
            $tot[$any][1]['in_prev_acu'] = 0;
            $tot[$any][2]['in_prev_acu'] = 0;
            $tot[$any][1]['in_acu'] = 0;
            $tot[$any][2]['in_acu'] = 0;
            $tot[$any][0]['gasto'] = 0;
            $tot[$any][1]['gasto'] = 0;
            $tot[$any][2]['gasto'] = 0;
            $tot[$any][1]['aportacion'] = 0;
            $tot[$any][2]['aportacion'] = 0;
            $tot[$any][1]['superavit'] = 0;
            $tot[$any][2]['superavit'] = 0;

            $inicio = "$any/1/1";
            $fin = "$any/12/31";
            $oInicio = new DateTimeLocal($inicio);
            $oFin = new DateTimeLocal($fin);


            $GesPeriodos = new GestorCasaPeriodo();
            $aPeriodos = $GesPeriodos->getArrayCasaPeriodos($id_ubi,$oInicio,$oFin);

            $a_resumen[$id_ubi][$any][0]['nom'] = $nombre_ubi;

            $a_resumen[$id_ubi][$any][1]['dias'] = 0;
            $a_resumen[$id_ubi][$any][2]['dias'] = 0;
            $a_resumen[$id_ubi][$any][1]['asist_prev'] = 0;
            $a_resumen[$id_ubi][$any][2]['asist_prev'] = 0;
            $a_resumen[$id_ubi][$any][1]['asist'] = 0;
            $a_resumen[$id_ubi][$any][2]['asist'] = 0;
            $a_resumen[$id_ubi][$any][1]['in_prev_acu'] = 0; 
            $a_resumen[$id_ubi][$any][2]['in_prev_acu'] = 0; 
            $a_resumen[$id_ubi][$any][1]['in_acu'] = 0; 
            $a_resumen[$id_ubi][$any][2]['in_acu'] = 0; 
            $a_resumen[$id_ubi][$any][1]['gasto'] = 0; 
            $a_resumen[$id_ubi][$any][2]['gasto'] = 0; 

            $a=0;
            $aWhere=array();
            $aOperador=array();
            $aWhere['f_ini']=$fin;
            $aOperador['f_ini']='<=';
            $aWhere['f_fin']=$inicio;
            $aOperador['f_fin']='>=';
            $aWhere['id_ubi']=$id_ubi;
            $aWhere['status']=4;
            $aOperador['status']='<';
            $aWhere['_ordre']='f_ini';
            $oGesActividades = new GestorActividad();
            $cActividades = $oGesActividades->getActividades($aWhere,$aOperador);
            foreach ($cActividades as $oActividad) {
                $id_activ = $oActividad->getId_activ();
                $oInicio = $oActividad->getF_ini();
                $oFin = $oActividad->getF_fin();
                $nom_activ = $oActividad->getNom_activ();
                
                $num_dias_act = $oActividad->getDuracion();
                $num_dias = $oActividad->getDuracionEnPeriodo($oInicio,$oFin);
                $num_dias_real = $oActividad->getDuracionReal();
                $factor_dias = ($num_dias/$num_dias_real);

                // saber a que periodo pertenece (sf/sv).
                $a_ocupacion= dias_ocupacion($aPeriodos,$oActividad,$oInicio,$oFin);
                $factor = ($num_dias_act-$num_dias_real)/$num_dias_real;

                $a_ocupacion[1] = round($a_ocupacion[1]*(1+$factor),1);
                $a_ocupacion[2] = round($a_ocupacion[2]*(1+$factor),1);

                $a_resumen[$id_ubi][$any][1]['dias'] += $a_ocupacion[1]; // sv
                $a_resumen[$id_ubi][$any][2]['dias'] += $a_ocupacion[2]; // sf

                $oIngreso = new Ingreso(array('id_activ'=>$id_activ));
                $num_asistentes_previstos=empty($oIngreso->getNum_asistentes_previstos())? 0 : $oIngreso->getNum_asistentes_previstos();
                $num_asistentes=empty($oIngreso->getNum_asistentes())? 0 : $oIngreso->getNum_asistentes();
                if (empty($oIngreso->getIngresos_previstos())) {
                    echo "<br>".sprintf(_("No hay ingresos previstos para la actividad %s"),$nom_activ);
                    $ingresos_previstos=0;
                    $ingresos=0;
                } else {
                    $ingresos_previstos=$factor_dias*$oIngreso->getIngresos_previstos();
                    $ingresos=$factor_dias*$oIngreso->getIngresos();
                }

                if (empty($num_asistentes_previstos)) {
                    echo "<br>".sprintf(_("No hay asistentes previstos para la actividad %s"),$nom_activ);
                }
                $a_resumen[$id_ubi][$any][1]['asist_prev']+=$num_asistentes_previstos*$a_ocupacion[1]; // sv
                $a_resumen[$id_ubi][$any][2]['asist_prev']+=$num_asistentes_previstos*$a_ocupacion[2]; // sf

                $a_resumen[$id_ubi][$any][1]['asist']+=$num_asistentes*$a_ocupacion[1]; // sv
                $a_resumen[$id_ubi][$any][2]['asist']+=$num_asistentes*$a_ocupacion[2]; // sf

                if (($a_ocupacion[1]+$a_ocupacion[2])>0) {
                    $in[1] = round(($ingresos/($a_ocupacion[1]+$a_ocupacion[2]))*$a_ocupacion[1],2); // sv
                    $in[2] = round(($ingresos/($a_ocupacion[1]+$a_ocupacion[2]))*$a_ocupacion[2],2); // sf
                    $a_resumen[$id_ubi][$any][1]['in_prev_acu']+=round(($ingresos_previstos/($a_ocupacion[1]+$a_ocupacion[2]))*$a_ocupacion[1],2); // sv
                    $a_resumen[$id_ubi][$any][2]['in_prev_acu']+=round(($ingresos_previstos/($a_ocupacion[1]+$a_ocupacion[2]))*$a_ocupacion[2],2); // sf
                    $a_resumen[$id_ubi][$any][1]['in_acu']+=$in[1];
                    $a_resumen[$id_ubi][$any][2]['in_acu']+=$in[2];
                } else {
                    $in[1] = '';
                    $in[2] = '';
                }
                $a_resumen[$id_ubi][$any][0]['detalles'][]="<tr><td>$nom_activ</td><td>$a_ocupacion[1]</td><td>$a_ocupacion[2]</td><td>$in[1]</td><td>$in[2]</td></tr>";

                $a++;
            } 
            // Si no hay actividades:
            if ($a < 1) {
                $a_resumen[$id_ubi][$any][0]['detalles'][0]="<tr><td>"._("no tiene actividades")."</td></tr>";
            }
            if (($var = $a_resumen[$id_ubi][$any][1]['dias']+$a_resumen[$id_ubi][$any][2]['dias']) > 0) {
                $a_resumen[$id_ubi][$any][1]['dias%'] = round($a_resumen[$id_ubi][$any][1]['dias']/$var*100,2);
                $a_resumen[$id_ubi][$any][2]['dias%'] = round($a_resumen[$id_ubi][$any][2]['dias']/$var*100,2);
            } else {
                $a_resumen[$id_ubi][$any][1]['dias%'] = '-';
                $a_resumen[$id_ubi][$any][2]['dias%'] = '-';
            }
            if (($var = $a_resumen[$id_ubi][$any][1]['asist_prev']+$a_resumen[$id_ubi][$any][2]['asist_prev']) > 0) {
                $a_resumen[$id_ubi][$any][1]['asist_prev%'] = round($a_resumen[$id_ubi][$any][1]['asist_prev']/$var*100,2);
                $a_resumen[$id_ubi][$any][2]['asist_prev%'] = round($a_resumen[$id_ubi][$any][2]['asist_prev']/$var*100,2);
            } else {
                $a_resumen[$id_ubi][$any][1]['asist_prev%'] = '-';
                $a_resumen[$id_ubi][$any][2]['asist_prev%'] = '-';
            }
            if (($var = $a_resumen[$id_ubi][$any][1]['asist']+$a_resumen[$id_ubi][$any][2]['asist']) > 0) {
                $a_resumen[$id_ubi][$any][1]['asist%'] = round($a_resumen[$id_ubi][$any][1]['asist']/$var*100,2);
                $a_resumen[$id_ubi][$any][2]['asist%'] = round($a_resumen[$id_ubi][$any][2]['asist']/$var*100,2);
            } else {
                $a_resumen[$id_ubi][$any][1]['asist%'] = '-';
                $a_resumen[$id_ubi][$any][2]['asist%'] = '-';
            }
            if (($var = $a_resumen[$id_ubi][$any][1]['in_prev_acu']+$a_resumen[$id_ubi][$any][2]['in_prev_acu']) > 0) {
                $a_resumen[$id_ubi][$any][1]['in_prev_acu%'] = round($a_resumen[$id_ubi][$any][1]['in_prev_acu']/$var*100,2);
                $a_resumen[$id_ubi][$any][2]['in_prev_acu%'] = round($a_resumen[$id_ubi][$any][2]['in_prev_acu']/$var*100,2);
            } else {
                $a_resumen[$id_ubi][$any][1]['in_prev_acu%'] = '-';
                $a_resumen[$id_ubi][$any][2]['in_prev_acu%'] = '-';
            }
            if (($var = $a_resumen[$id_ubi][$any][1]['in_acu']+$a_resumen[$id_ubi][$any][2]['in_acu']) > 0) {
                $a_resumen[$id_ubi][$any][1]['in_acu%'] = round($a_resumen[$id_ubi][$any][1]['in_acu']/$var*100,2);
                $a_resumen[$id_ubi][$any][2]['in_acu%'] = round($a_resumen[$id_ubi][$any][2]['in_acu']/$var*100,2);
            } else {
                $a_resumen[$id_ubi][$any][1]['in_acu%'] = '-';
                $a_resumen[$id_ubi][$any][2]['in_acu%'] = '-';
            }

            // Gastos ------
            $GesGastos = new GestorUbiGasto();
            // tipo: 1=sv, 2=sf, 3=gastos
            $a_resumen[$id_ubi][$any][1]['aportacion']=$GesGastos->getSumaGastos($id_ubi,1,$oInicio,$oFin);
            $a_resumen[$id_ubi][$any][2]['aportacion']=$GesGastos->getSumaGastos($id_ubi,2,$oInicio,$oFin);
            $a_resumen[$id_ubi][$any][0]['gasto']=$GesGastos->getSumaGastos($id_ubi,3,$oInicio,$oFin);

            $a_repartoGastos = reparto($aPeriodos);
            if (($var = $a_repartoGastos[1]+$a_repartoGastos[2]) > 0) {
                $a_resumen[$id_ubi][$any][1]['gasto%'] = round($a_repartoGastos[1]/$var*100,2);
                $a_resumen[$id_ubi][$any][2]['gasto%'] = round($a_repartoGastos[2]/$var*100,2);
                $a_resumen[$id_ubi][$any][1]['gasto'] = round($a_resumen[$id_ubi][$any][0]['gasto']*$a_resumen[$id_ubi][$any][1]['gasto%']/100,2);
                $a_resumen[$id_ubi][$any][2]['gasto'] = round($a_resumen[$id_ubi][$any][0]['gasto']*$a_resumen[$id_ubi][$any][2]['gasto%']/100,2);

                if ($id_ubi==$id_ubi_hijo && array_key_exists($id_ubi_padre,$a_resumen)) {
                    $in[1] = ($a_resumen[$id_ubi_padre][$any][1]['aportacion']+$a_resumen[$id_ubi_padre][$any][1]['in_acu']+$a_resumen[$id_ubi][$any][1]['in_acu']);
                    $out[1] = round($a_resumen[$id_ubi_padre][$any][1]['gasto%']*$a_resumen[$id_ubi_padre][$any][0]['gasto']/100,2);
                    $in[2] = ($a_resumen[$id_ubi_padre][$any][2]['aportacion']+$a_resumen[$id_ubi_padre][$any][2]['in_acu']+$a_resumen[$id_ubi][$any][2]['in_acu']);
                    $out[2] = round($a_resumen[$id_ubi_padre][$any][2]['gasto%']*$a_resumen[$id_ubi_padre][$any][0]['gasto']/100,2);
                    $a_resumen[$id_ubi_padre][$any][1]['superavit'] =  $in[1] - $out[1] ;
                    $a_resumen[$id_ubi_padre][$any][2]['superavit'] =  $in[2] - $out[2] ;
                    $a_resumen[$id_ubi][$any][1]['superavit'] = '';
                    $a_resumen[$id_ubi][$any][2]['superavit'] = '';
                } else {
                    $in[1] = ($a_resumen[$id_ubi][$any][1]['aportacion']+$a_resumen[$id_ubi][$any][1]['in_acu']);
                    $out[1] = round($a_resumen[$id_ubi][$any][1]['gasto%']*$a_resumen[$id_ubi][$any][0]['gasto']/100,2);
                    $in[2] = ($a_resumen[$id_ubi][$any][2]['aportacion']+$a_resumen[$id_ubi][$any][2]['in_acu']);
                    $out[2] = round($a_resumen[$id_ubi][$any][2]['gasto%']*$a_resumen[$id_ubi][$any][0]['gasto']/100,2);
                    $a_resumen[$id_ubi][$any][1]['superavit'] =  $in[1] - $out[1] ;
                    $a_resumen[$id_ubi][$any][2]['superavit'] =  $in[2] - $out[2] ;
                }
            } else {
                $a_resumen[$id_ubi][$any][1]['gasto%'] = '-';
                $a_resumen[$id_ubi][$any][2]['gasto%'] = '-';
                $a_resumen[$id_ubi][$any][1]['superavit'] = '0';
                $a_resumen[$id_ubi][$any][2]['superavit'] = '0';
            }

            // Sumas ---------
            $tot[$any][1]['dias'] += $a_resumen[$id_ubi][$any][1]['dias'];
            $tot[$any][2]['dias'] += $a_resumen[$id_ubi][$any][2]['dias'];
            $tot[$any][1]['asist_prev'] += $a_resumen[$id_ubi][$any][1]['asist_prev'];
            $tot[$any][2]['asist_prev'] += $a_resumen[$id_ubi][$any][2]['asist_prev'];
            $tot[$any][1]['asist'] += $a_resumen[$id_ubi][$any][1]['asist'];
            $tot[$any][2]['asist'] += $a_resumen[$id_ubi][$any][2]['asist'];
            $tot[$any][1]['in_prev_acu'] += $a_resumen[$id_ubi][$any][1]['in_prev_acu'];
            $tot[$any][2]['in_prev_acu'] += $a_resumen[$id_ubi][$any][2]['in_prev_acu'];
            $tot[$any][1]['in_acu'] += $a_resumen[$id_ubi][$any][1]['in_acu'];
            $tot[$any][2]['in_acu'] += $a_resumen[$id_ubi][$any][2]['in_acu'];
            $tot[$any][0]['gasto'] += $a_resumen[$id_ubi][$any][0]['gasto'];
            $tot[$any][1]['gasto'] += $a_resumen[$id_ubi][$any][1]['gasto'];
            $tot[$any][2]['gasto'] += $a_resumen[$id_ubi][$any][2]['gasto'];
            $tot[$any][1]['aportacion'] += $a_resumen[$id_ubi][$any][1]['aportacion'];
            $tot[$any][2]['aportacion'] += $a_resumen[$id_ubi][$any][2]['aportacion'];
            $tot[$any][1]['superavit'] += $a_resumen[$id_ubi][$any][1]['superavit'];
            $tot[$any][2]['superavit'] += $a_resumen[$id_ubi][$any][2]['superavit'];
        }

        // %%% Totales
        if (($var = $tot[$any][1]['dias']+$tot[$any][2]['dias']) > 0) {
            $tot[$any][1]['dias%'] = round($tot[$any][1]['dias']/$var*100,2);
            $tot[$any][2]['dias%'] = round($tot[$any][2]['dias']/$var*100,2);
        } else {
            $tot[$any][1]['dias%'] = '-';
            $tot[$any][2]['dias%'] = '-';
        }
        if (($var = $tot[$any][1]['asist_prev']+$tot[$any][2]['asist_prev']) > 0) {
            $tot[$any][1]['asist_prev%'] = round($tot[$any][1]['asist_prev']/$var*100,2);
            $tot[$any][2]['asist_prev%'] = round($tot[$any][2]['asist_prev']/$var*100,2);
        } else {
            $tot[$any][1]['asist_prev%'] = '-';
            $tot[$any][2]['asist_prev%'] = '-';
        }
        if (($var = $tot[$any][1]['asist']+$tot[$any][2]['asist']) > 0) {
            $tot[$any][1]['asist%'] = round($tot[$any][1]['asist']/$var*100,2);
            $tot[$any][2]['asist%'] = round($tot[$any][2]['asist']/$var*100,2);
        } else {
            $tot[$any][1]['asist%'] = '-';
            $tot[$any][2]['asist%'] = '-';
        }
        if (($var = $tot[$any][1]['in_prev_acu']+$tot[$any][2]['in_prev_acu']) > 0) {
            $tot[$any][1]['in_prev_acu%'] = round($tot[$any][1]['in_prev_acu']/$var*100,2);
            $tot[$any][2]['in_prev_acu%'] = round($tot[$any][2]['in_prev_acu']/$var*100,2);
        } else {
            $tot[$any][1]['in_prev_acu%'] = '-';
            $tot[$any][2]['in_prev_acu%'] = '-';
        }
        if (($var = $tot[$any][1]['in_acu']+$tot[$any][2]['in_acu']) > 0) {
            $tot[$any][1]['in_acu%'] = round($tot[$any][1]['in_acu']/$var*100,2);
            $tot[$any][2]['in_acu%'] = round($tot[$any][2]['in_acu']/$var*100,2);
        } else {
            $tot[$any][1]['in_acu%'] = '-';
            $tot[$any][2]['in_acu%'] = '-';
        }
        if (($var = $tot[$any][1]['gasto']+$tot[$any][2]['gasto']) > 0) {
            $tot[$any][1]['gasto%'] = round($tot[$any][1]['gasto']/$var*100,2);
            $tot[$any][2]['gasto%'] = round($tot[$any][2]['gasto']/$var*100,2);
        } else {
            $tot[$any][1]['gasto%'] = '-';
            $tot[$any][2]['gasto%'] = '-';
        }
    }
    //------------- Mostrar -------------
    foreach ($a_resumen as $id_ubi => $row_any) {
    ?>
        <table border=1>
        <tr><th><?= $row_any[$any][0]['nom'] ?></th><th></th><th><?= _("días ocupados") ?></th><th>%</th><th><?= _("nº días x nº asistentes (previsto)") ?></th><th>%</th>
        <th><?= _("nº días x nº asistentes (real)") ?></th><th>%</th>
        <th><?= _("ingresos previstos acumulados") ?></th><th>%</th>
        <th><?= _("ingresos reales acumulados") ?></th><th>%</th>
        <th><?= _("gastos reales") ?></th><th>%</th><th><?= _("aportación real dl") ?></th><th><?= _("superávit") ?></th><tr>
    <?php
    foreach ($row_any as $any => $row) {

        echo "<tr id=detalles_".$id_ubi."_".$any." style='display: none;'><td colspan=16><table>";
        echo "<tr><th>"._("actividad")."</th><th>"._("ocupación sv")."</th><th>"._("ocupación sf")."</th><th>"._("ingresos sv")."</th><th>"._("ingresos sf")."</th></tr>";
        foreach ($a_resumen[$id_ubi][$any][0]['detalles'] as $fila_detalle) {
            echo $fila_detalle;
        }
        echo "</table></td></tr>";
        ?>
        <tr><td><?= $any ?></td><td>dlb</td>
            <td class='derecha'><?= $row[1]['dias'] ?></td><td class='derecha'><?= $row[1]['dias%'] ?></td>
            <td class='derecha'><?= $row[1]['asist_prev'] ?></td><td class='derecha'><?= $row[1]['asist_prev%'] ?></td>
            <td class='derecha'><?= $row[1]['asist'] ?></td><td class='derecha'><?= $row[1]['asist%'] ?></td>
            <td class='derecha'><?= $row[1]['in_prev_acu'] ?><td class='derecha'><?= $row[1]['in_prev_acu%'] ?></td>
            <td class='derecha'><?= $row[1]['in_acu'] ?></td><td class='derecha'><?= $row[1]['in_acu%'] ?></td>
            <td class='derecha'><?= $row[1]['gasto'] ?></td><td class='derecha'><?= $row[1]['gasto%'] ?></td>
            <td class='derecha'><?= $row[1]['aportacion'] ?></td><td class='derecha'><?= $row[1]['superavit'] ?></td>

            </tr>
        <tr><td><span id='span_detalles_<?= $id_ubi ?>_<?= $any ?>' class=link onclick=fnjs_detalles(<?= $id_ubi ?>,<?= $any ?>) ><p><?= _("mostrar detalles") ?><p><p style="display: none"><?= _("ocultar detalles") ?><p></span></td><td>dlbf</td>
            <td class='derecha'><?= $row[2]['dias'] ?></td><td class='derecha'><?= $row[2]['dias%'] ?></td>
            <td class='derecha'><?= $row[2]['asist_prev'] ?></td><td class='derecha'><?= $row[2]['asist_prev%'] ?></td>
            <td class='derecha'><?= $row[2]['asist'] ?></td><td class='derecha'><?= $row[2]['asist%'] ?></td>
            <td class='derecha'><?= $row[2]['in_prev_acu'] ?></td><td class='derecha'><?= $row[2]['in_prev_acu%'] ?></td>
            <td class='derecha'><?= $row[2]['in_acu'] ?></td><td class='derecha'><?= $row[2]['in_acu%'] ?></td>
            <td class='derecha'><?= $row[2]['gasto'] ?></td><td class='derecha'><?= $row[2]['gasto%'] ?></td>
            <td class='derecha'><?= $row[2]['aportacion'] ?></td><td class='derecha'><?= $row[2]['superavit'] ?></td>

            </tr>
        <tr><td></td><td>totales</td>
            <td class='derecha'><?= $row[1]['dias'] + $row[2]['dias'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= $row[1]['asist_prev'] + $row[2]['asist_prev'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= $row[1]['asist'] + $row[2]['asist'] ?></td><td class='derecha'></td>
            <td class='derecha'><?= round(($row[1]['in_prev_acu'] + $row[2]['in_prev_acu']),2) ?></td><td class='derecha'></td>
            <td class='derecha'><?= round(($row[1]['in_acu'] + $row[2]['in_acu']),2) ?></td><td class='derecha'></td>
            <td class='derecha'><?= $row[0]['gasto'] ?></td><td class='derecha'></td>
            <td></td><td class='derecha'><?= round(($row[1]['superavit'] + $row[2]['superavit']),2) ?></td>

            </tr>
        <?php
        }
        ?>
        </table>
    <?php
    }
}
