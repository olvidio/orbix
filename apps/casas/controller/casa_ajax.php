<?php

// INICIO Cabecera global de URL de controlador *********************************

use actividades\model\entity\Actividad;
use actividades\model\entity\GestorActividad;
use actividadescentro\model\entity\GestorCentroEncargado;
use actividadtarifas\model\entity\GestorTipoTarifa;
use actividadtarifas\model\entity\TipoTarifa;
use casas\model\entity\Ingreso;
use core\ConfigGlobal;
use permisos\model\PermisosActividadesTrue;
use procesos\model\entity\GestorActividadProcesoTarea;
use ubis\model\entity\CasaDl;
use ubis\model\entity\CentroDl;
use ubis\model\entity\Tarifa;
use usuarios\model\entity\Role;
use web\Lista;
use web\Periodo;
use web\TiposActividades;

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qque = (string) \filter_input(INPUT_POST, 'que');

$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
$Qyear = (string) \filter_input(INPUT_POST, 'year');


switch ($Qque) {
	case 'nuevo':
        $Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
		$txt="<form id='frm_periodo'>";
		$txt.='<h3>'._("Periodo").'</h3>';
		$txt.="<input type=hidden name=que value=\"update\" > ";
		$txt.="<input type=hidden name=id_ubi value=\"$Qid_ubi\" > ";
		$txt.= _("de") ."<input type=text size=12 name=f_ini value=\"\">   "._("hasta")." <input type=text size=12 name=f_fin value=\"\">";
		$txt.= _("asignado a")." <select name=sfsv_num><option value=1 >". _("sv")."</option>";
		$txt.="<option value=2 >". _("sf") ."</option>";
		$txt.="<option value=3 >". _("reservado") ."</option></select>";
		$txt.='<br><br>';
		$txt.="<input type='button' value='". _('guardar') ."' onclick=\"fnjs_guardar('#frm_periodo','guardar');\" >";
		$txt.="<input type='button' value='". _('cancel') ."' onclick=\"fnjs_cerrar();\" >";
		$txt.="</form> ";
		echo $txt;
		break;
	case 'form_ingreso':
        $Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
		$oActividad = new Actividad($Qid_activ);
		$nom_activ = $oActividad->getNom_activ();
	    $id_tipo_activ = $oActividad->getId_tipo_activ();
	    $dl_org = $oActividad->getDl_org();
	    $tarifa = $oActividad->getTarifa();
	    $precio = $oActividad->getPrecio();
		
		// permiso para tarifas
		$_SESSION['oPermActividades']->setActividad($Qid_activ,$id_tipo_activ,$dl_org);
		$oPermTar = $_SESSION['oPermActividades']->getPermisoActual('tarifa'); //tarifas

		$oTipoActiv= new TiposActividades($id_tipo_activ);	
		$oGesTipoTarifa = new GestorTipoTarifa();
		$isfsv=$oTipoActiv->getSfsvId();

		if ($oPermTar->have_perm_action('modificar')) {
			$oDesplPosiblesTipoTarifas = $oGesTipoTarifa->getListaTipoTarifas($isfsv);
			$oDesplPosiblesTipoTarifas->setNombre('tarifa');
			$oDesplPosiblesTipoTarifas->setOpcion_sel($tarifa);
			$tarifa_html = $oDesplPosiblesTipoTarifas->desplegable();
		} else {
			$oTipoTarifa = new TipoTarifa($tarifa);
			$tarifa_html = $oTipoTarifa->getLetra();
		}

		$oIngreso = new Ingreso(array('id_activ'=>$Qid_activ));
		$ingresos = $oIngreso->getIngresos();
		$num_asistentes = $oIngreso->getNum_asistentes();
		$observ = $oIngreso->getObserv();
		
		$oHash = new web\Hash();
		$oHash->setcamposForm('tarifa!precio!ingresos!num_asistentes!observ');
		$oHash->setCamposNo('que');
		$a_camposHidden = array(
		    'que' => '',
		    'id_activ' => $Qid_activ,
		);
		$oHash->setArraycamposHidden($a_camposHidden);
		
		$txt="<form id='frm_ingreso'>";
		$txt.='<h3>'._("Actividad").':</h3>';
		$txt.='<h5>'.$nom_activ.'</h5>';
		$txt.= _("tarifa") .": $tarifa_html<br>"._("precio")." <input type=text size=8 name=precio value=\"$precio\">";
		$txt.='<h3>'._("Ingreso").':</h3>';
		$txt.=$oHash->getCamposHtml();
		$txt.= _("ingresos reales") ."<input type=text size=12 name=ingresos value=\"$ingresos\">   "._("asistentes")." <input type=text size=12 name=num_asistentes value=\"$num_asistentes\">";
		$txt.='<br>';
		$txt.= _("observaciones") ."<input type=text size=40 name=observ value=\"".htmlspecialchars($observ)."\">";
		$txt.='<br><br>';
		$txt.="<input type='button' value='". _('guardar') ."' onclick=\"fnjs_guardar('#frm_ingreso','guardar');\" >";
		$txt.="<input type='button' value='". _('eliminar') ."' onclick=\"fnjs_guardar('#frm_ingreso','eliminar');\" >";
		$txt.="<input type='button' value='". _('cancel') ."' onclick=\"fnjs_cerrar();\" >";
		$txt.="</form> ";
		echo $txt;
		break;
	case "get":
		$tot_asis_pr[1] = 0;	$tot_asis_pr[2] = 0;	$tot_asis_pr['tot'] = 0;
		$tot_asis[1] = 0;   	$tot_asis[2] = 0;    	$tot_asis['tot'] = 0;
		$tot_ing_pr[1] = 0; 	$tot_ing_pr[2] = 0;  	$tot_ing_pr['tot'] = 0; 
		$tot_ing[1] = 0;    	$tot_ing[2] = 0;     	$tot_ing['tot'] = 0;
		$tot_ing_acu[1] = 0;	$tot_ing_acu[2] = 0;

		$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
		$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
		$Qyear = (string) \filter_input(INPUT_POST, 'year');
		$Qempiezamin = (string) \filter_input(INPUT_POST, 'empiezamin');
		$Qempiezamax = (string) \filter_input(INPUT_POST, 'empiezamax');
		
		// permisos:
		// miro que rol tengo. Si soy casa, sólo veo la mía
		$miRolePau = ConfigGlobal::mi_role_pau();
		if ($miRolePau == Role::PAU_CDC || ($_SESSION['oPerm']->have_perm_oficina('pr')) ) {
		    $permiso = 'modificar';
		} else {
		    $permiso = '';
		}
		// listado de actividades por casa y periodo.
		
		$aWhere = [];
		$aOperador = [];
		
		// periodo.
		$oPeriodo = new Periodo();
		$oPeriodo->setDefaultAny('next');
		$oPeriodo->setAny($Qyear);
		$oPeriodo->setEmpiezaMin($Qempiezamin);
		$oPeriodo->setEmpiezaMax($Qempiezamax);
		$oPeriodo->setPeriodo($Qperiodo);
		
		
		$inicioIso = $oPeriodo->getF_ini_iso();
		$finIso = $oPeriodo->getF_fin_iso();
		if (!empty($Qperiodo) && $Qperiodo == 'desdeHoy') {
		    $aWhere['f_fin'] = "'$inicioIso','$finIso'";
		    $aOperador['f_fin'] = 'BETWEEN';
		} else {
		    $aWhere['f_ini'] = "'$inicioIso','$finIso'";
		    $aOperador['f_ini'] = 'BETWEEN';
		}

		// posible selección múltiple de casas
		$Qaid_cdc = (array)  \filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		// una lista de casas (id_ubi).
		$aGrupos = [];
		if (!empty($Qaid_cdc)) {
			foreach ($Qaid_cdc as $id_ubi) {
				if (empty($id_ubi)) continue;
				$oCasa = new CasaDl($id_ubi);
				$aGrupos[$id_ubi]= $oCasa->getNombre_ubi();
			}
		} else {
			exit (_("Debe seleccionar una casa."));
		}
		$a_valores = [];
		foreach ($aGrupos as $id_ubi => $Titulo) {
			$aWhere['id_ubi']=$id_ubi;
			$aWhere['status'] = 4;
			$aOperador['status'] = '<';
			$aWhere['_ordre'] = 'f_ini';
			$GesActividades = new GestorActividad();
			$cActividades = $GesActividades->getActividades($aWhere,$aOperador);

			$a=0;
			$i_previstos_acumulados=0;
			$i_acumulados=0;
			$ingresos_previstos=0;
			$ingresos=0;
			$num_asistentes_previstos=0;
			$num_asistentes=0;
			$txt_err = '';
			foreach ($cActividades as $oActividad) {
			    $id_activ = $oActividad->getId_activ();
			    $id_tipo_activ = $oActividad->getId_tipo_activ();
			    $nom_activ = $oActividad->getNom_activ();
			    $dl_org = $oActividad->getDl_org();
			    $tarifa = $oActividad->getTarifa();
			    $precio = $oActividad->getPrecio();
			    $oF_ini = $oActividad->getF_ini();
			    $oF_fin = $oActividad->getF_fin();
			    
				$num_dias_act = $oActividad->getDuracion();
				$num_dias = $oActividad->getDuracionEnPeriodo($oF_ini,$oF_fin);
				$num_dias_real = $oActividad->getDuracionReal();
				$factor_dias = ($num_dias/$num_dias_real);
				$factor = ($num_dias_act-$num_dias_real)/$num_dias_real;

				$num_dias_ajust = round($num_dias*(1+$factor),1);

				// mirar permisos.
				$_SESSION['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
				$oPermEco = $_SESSION['oPermActividades']->getPermisoActual('economic'); //dossiers económicos

				//echo "$a, $nom_activ, $permiso<br>";
				//print_r($oPermEco);

				if (!$oPermEco->have_perm_action('ver')) { continue; } // no tiene permisos ni para ver.
				if ($oPermEco->have_perm_action('modificar')) { // sólo puede ver que està ocupado
					$permiso = 'modificar';
				} else {
					$permiso = '';
				}

				$oTipoTarifa = new TipoTarifa(array('tarifa'=>$tarifa));
				$modo = $oTipoTarifa->getModo();
				$oTarifa = new Tarifa(array('id_ubi'=>$id_ubi,'tarifa'=>$tarifa,'year'=>$Qyear));
				$cantidad = $oTarifa->getCantidad();
				if (empty($precio)) {
					$flag = ($factor_dias != 1)? '*' : ''; 
					if ($modo == 1) { // precio fijo
						$precio = round($factor_dias*$cantidad,2) . $flag;
						$precio_pr = round($factor_dias*$cantidad,2);
					} else { 
						$precio = sprintf(_('%s %s días x %s ~= %s'),$flag,($num_dias_ajust),$cantidad,($num_dias_ajust * $cantidad));
						$precio_pr = round($num_dias_ajust*$cantidad,2);
					}
				} else {
					$precio_pr = round($factor_dias*$precio,2);
				}
				$a_valores[$id_ubi][$a][1]=$oF_ini->getFromLocal();
				$a_valores[$id_ubi][$a][2]=$oF_fin->getFromLocal();
				// trec les dates del nom perque ocupi menys.
				//$nom_activ = preg_replace('/\(.*\)(.*)/','\1',$nom_activ);
				$oTipoActiv= new TiposActividades($id_tipo_activ);
				$nom_activ = $oTipoActiv->getNomGral();
				if ($permiso == 'modificar') {
					$script="fnjs_modificar($id_activ)";
					$a_valores[$id_ubi][$a][3]=array( 'script'=>$script, 'valor'=>$nom_activ);
				} else {
					$a_valores[$id_ubi][$a][3]=$nom_activ;
				}

				$err = 0;
				$oIngreso = new Ingreso(array('id_activ'=>$id_activ));
				$num_asistentes_previstos=$oIngreso->getNum_asistentes_previstos();
				if (empty($num_asistentes_previstos)) {
				    $txt_err .= empty($txt_err)? '' : "<br>";
                    $txt_err .= sprintf(_("No está definido el núm. de asistente previstos para %s"),$nom_activ); 
                    $num_asistentes_previstos = 0;
				}
				$num_asistentes=$oIngreso->getNum_asistentes();
				if (empty($num_asistentes)) {
				    $txt_err .= empty($txt_err)? '' : "<br>";
                    $txt_err .= sprintf(_("No está definido el núm. de asistente para %s"),$nom_activ); 
                    $num_asistentes = 0;
				}
				//$ingresos_previstos=round($factor_dias*$oIngreso->getIngresos_previstos(),2);
				$ingresos_previstos=$num_asistentes_previstos*$precio_pr;
				$ingresos=round($factor_dias*$oIngreso->getIngresos(),2);
				$observ=$oIngreso->getObserv();
				
				$i_previstos_acumulados+=$ingresos_previstos;
				$i_acumulados+=$ingresos;

				//sumas
				$sfsv = substr($id_tipo_activ,0,1);
				$tot_asis_pr[$sfsv] += $num_asistentes_previstos;
				$tot_asis[$sfsv] += $num_asistentes;
				$tot_ing_pr[$sfsv] += $ingresos_previstos;
				$tot_ing[$sfsv] += $ingresos;
				$tot_ing_acu[$sfsv] += $ingresos;

				$tot_asis_pr['tot'] += $num_asistentes_previstos;
				$tot_asis['tot'] += $num_asistentes;
				$tot_ing_pr['tot'] += $ingresos_previstos;
				$tot_ing['tot'] += $ingresos;

				$a_valores[$id_ubi][$a][4]=$precio;
				$a_valores[$id_ubi][$a][5]=$num_asistentes_previstos;
				$a_valores[$id_ubi][$a][6]=$num_asistentes;
				$a_valores[$id_ubi][$a][7]=$num_asistentes-$num_asistentes_previstos;
				$a_valores[$id_ubi][$a][8]=$ingresos_previstos;
				$a_valores[$id_ubi][$a][9]=$ingresos;
				$a_valores[$id_ubi][$a][10]=$i_previstos_acumulados;
				$a_valores[$id_ubi][$a][11]=$i_acumulados;
				$a_valores[$id_ubi][$a][12]=$observ;
				$a_valores[$id_ubi][$a]['clase']='derecha';
				$a++;
			}
			$oF_ini_periodo = $oPeriodo->getF_ini();
			$oF_fin_periodo = $oPeriodo->getF_fin();
			// total sv
			$a_valores[$id_ubi][$a][1]= $oF_ini_periodo->getFromLocal();
			$a_valores[$id_ubi][$a][2]= $oF_fin_periodo->getFromLocal();
			$a_valores[$id_ubi][$a][3]=_('totales sv');
			$a_valores[$id_ubi][$a][4]= '';
			$a_valores[$id_ubi][$a][5]= $tot_asis_pr[1];
			$a_valores[$id_ubi][$a][6]= $tot_asis[1];
			$a_valores[$id_ubi][$a][7]= empty($tot_asis['tot'])? '-' : round($tot_asis[1]/$tot_asis['tot']*100,2).'%';
			$a_valores[$id_ubi][$a][8]= $tot_ing_pr[1];
			$a_valores[$id_ubi][$a][9]= $tot_ing[1];
			$a_valores[$id_ubi][$a][10]= empty($tot_ing['tot'])? '-' : round($tot_ing[1]/$tot_ing['tot']*100,2).'%';
			$a_valores[$id_ubi][$a][11]= $tot_ing_acu[1];
			$a_valores[$id_ubi][$a][12]= '';
			$a_valores[$id_ubi][$a]['clase']='derecha';

			// total sf
			$a_valores[$id_ubi][$a+1][1]= $oF_ini_periodo->format('d/m/y');
			$a_valores[$id_ubi][$a+1][2]= $oF_fin_periodo->format('d/m/y');
			$a_valores[$id_ubi][$a+1][3]=_('totales sf');
			$a_valores[$id_ubi][$a+1][4]= '';
			$a_valores[$id_ubi][$a+1][5]= $tot_asis_pr[2];
			$a_valores[$id_ubi][$a+1][6]= $tot_asis[2];
			$a_valores[$id_ubi][$a+1][7]= empty($tot_asis['tot'])? '-' : round($tot_asis[2]/$tot_asis['tot']*100,2).'%';
			$a_valores[$id_ubi][$a+1][8]= $tot_ing_pr[2];
			$a_valores[$id_ubi][$a+1][9]= $tot_ing[2];
			$a_valores[$id_ubi][$a+1][10]= empty($tot_ing['tot'])? '-' : round($tot_ing[2]/$tot_ing['tot']*100,2).'%';
			$a_valores[$id_ubi][$a+1][11]= $tot_ing_acu[2];
			$a_valores[$id_ubi][$a+1][12]= '';
			$a_valores[$id_ubi][$a+1]['clase']='derecha';

			// total
			$a_valores[$id_ubi][$a+2][1]= $oF_ini_periodo->format('d/m/y');
			$a_valores[$id_ubi][$a+2][2]= $oF_fin_periodo->format('d/m/y');
			$a_valores[$id_ubi][$a+2][3]=_('totales');
			$a_valores[$id_ubi][$a+2][4]= '';
			$a_valores[$id_ubi][$a+2][5]= $tot_asis_pr['tot'];
			$a_valores[$id_ubi][$a+2][6]= $tot_asis['tot'];
			$a_valores[$id_ubi][$a+2][7]= '';
			$a_valores[$id_ubi][$a+2][8]= $tot_ing_pr['tot'];
			$a_valores[$id_ubi][$a+2][9]= $tot_ing['tot'];
			$a_valores[$id_ubi][$a+2][10]= $i_previstos_acumulados; // es el último valor de la tabla (ya se ha sumado).
			$a_valores[$id_ubi][$a+2][11]= $i_acumulados; // es el último valor de la tabla (ya se ha sumado).
			$a_valores[$id_ubi][$a+2][12]= '';
			$a_valores[$id_ubi][$a+2]['clase']='derecha';
		}
		$a_cabeceras = [ _("inicio"),
                    _("fin"),
                    _("tipo de actividad"),
                    _("precio"),
                    _("asistentes previstos"),
                    _("asistentes reales"),
                    _("dif. asistencias"),
                    _("ingresos previstos"),
                    _("ingresos reales"),
                    _("ing. previstos acumulados"),
                    _("ing. reales acumulados"),
                    _("observaciones"),
		              ];

		$oLista = new Lista();
		$oLista->setGrupos($aGrupos);
		$oLista->setCabeceras($a_cabeceras);
		$oLista->setDatos($a_valores);
		echo $oLista->listaPaginada();
		echo _("* Se cuentan los ingresos proporcionales correspondientes al periodo.");
		if (!empty($txt_err)) {
            echo "<br>";
		    echo _("CUIDADO. Falta introducir datos");
            echo "<br>";
		    echo $txt_err;
		}
		break;
	case "guardar":
        $Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
        $Qprecio = (integer) \filter_input(INPUT_POST, 'precio');
        $Qtarifa = (string) \filter_input(INPUT_POST, 'tarifa');

        $Qingresos = (integer) \filter_input(INPUT_POST, 'ingresos');
        $Qnum_asistentes = (integer) \filter_input(INPUT_POST, 'num_asistentes');
        $Qobserv = (string) \filter_input(INPUT_POST, 'observ');
		// también los datos en la actividad.
		if (!empty($Qid_activ)) {
			$oActividad = new Actividad($Qid_activ);
			$oActividad->DBCarregar();
			isset($Qtarifa) ? $oActividad->setTarifa($Qtarifa) : '';
			if (isset($Qprecio)) { $Qprecio = str_replace(',','.',$Qprecio); $oActividad->setPrecio($Qprecio); }
			if ($oActividad->DBGuardar() === false) {
				echo _("Hay un error, no se ha guardado la actividad.");
			}
		}
		if (!empty($Qid_activ)) {
			$oIngreso = new Ingreso(array('id_activ'=>$Qid_activ));
			$oIngreso->DBCarregar(); //perque agafi els valor que ja té.
		} else {
			$oIngreso = new Ingreso();
		}
        $Qingresos = str_replace(',','.',$Qingresos);
        $oIngreso->setIngresos($Qingresos);
		$oIngreso->setNum_asistentes($Qnum_asistentes);
		$oIngreso->setObserv($Qobserv);
		if ($oIngreso->DBGuardar() === false) {
			echo _("Hay un error, no se ha guardado.");
		}
		break;
	case "eliminar":
        $Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');
		if (!empty($Qid_activ)) {
			$oIngreso = new Ingreso(array('id_activ'=>$Qid_activ));
			if ($oIngreso->DBEliminar() === false) {
				echo _('Hay un error, no se ha eliminado');
			}
		} else {
			$error_txt=_("no sé cuál he de borar");
			echo "{ que: '".$Qque."', error: '$error_txt' }";
		}
		break;
	case "lista_activ":
		$Qid_ubi = (integer) \filter_input(INPUT_POST, 'id_ubi');
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
		
		$inicioIso = $oPeriodo->getF_ini_iso();
		$finIso = $oPeriodo->getF_fin_iso();

		// posible selección múltiple de casas
		if (!empty($_POST['id_cdc'])) {
			foreach ($_POST['id_cdc'] as $id_ubi) {
				if (empty($id_ubi)) continue;
				$oCasa = new CasaDl($id_ubi);
				$aGrupos[$id_ubi]= $oCasa->getNombre_ubi();
			}
		}
		$a_valores = array();
		foreach ($aGrupos as $id_ubi => $Titulo) {
			$aWhere['id_ubi']=$id_ubi;
			$aWhere['f_ini'] = "'$inicioIso','$finIso'";
			$aOperador['f_ini'] = 'BETWEEN';
			$aWhere['status'] = 4;
			$aOperador['status'] = '<';
			$aWhere['_ordre'] = 'f_ini';
			$GesActividades = new GestorActividad();
			$cActividades = $GesActividades->getActividades($aWhere,$aOperador);
			if ($cActividades === false) continue;

			$a=0;
			$i_previstos_acumulados=0;
			$i_acumulados=0;
			$ingresos_previstos=0;
			$ingresos=0;
			$num_asistentes_previstos=0;
			$num_asistentes=0;
			foreach ($cActividades as $oActividad) {
				$a++;
				$id_activ = $oActividad->getId_activ();
				$id_tipo_activ = $oActividad->getId_tipo_activ();
				$dl_org = $oActividad->getDl_org();
				$f_ini = $oActividad->getF_ini()->getFromLocal();
				$h_ini = $oActividad->getH_ini();
				$f_fin = $oActividad->getF_fin()->getFromLocal();
				$h_fin = $oActividad->getH_fin();
				$tarifa = $oActividad->getTarifa();
				$observ = $oActividad->getObserv();
				
				if (strlen($h_ini)) {$h_ini=substr($h_ini,0, (strlen($h_ini)-3));}
				if (strlen($h_fin)) {$h_fin=substr($h_fin,0, (strlen($h_fin)-3));}
				// mirar permisos.
				if(core\ConfigGlobal::is_app_installed('procesos')) {
				    $_SESSION['oPermActividades']->setActividad($id_activ,$id_tipo_activ,$dl_org);
				    $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');
				    $oPermCtr = $_SESSION['oPermActividades']->getPermisoActual('ctr');
                    $oPermSacd = $_SESSION['oPermActividades']->getPermisoActual('sacd');
				} else {
				    $oPermActividades = new PermisosActividadesTrue(core\ConfigGlobal::mi_id_usuario());
				    $oPermActiv = $oPermActividades->getPermisoActual('datos');
				    $oPermCtr =  $oPermActividades->getPermisoActual('ctr');
                    $oPermSacd = $oPermActividades->getPermisoActual('sacd');
				}

				$oCasa = new CasaDl($id_ubi);
				$nombre_ubi = $oCasa->getNombre_ubi();
				if (!$oPermActiv->have_perm_action('ocupado')) { continue; } // no tiene permisos ni para ver.
				if (!$oPermActiv->have_perm_action('ver')) { // sólo puede ver que està ocupado
					$oTipoActiv= new TiposActividades($id_tipo_activ);
					$ssfsv=$oTipoActiv->getSfsvText();
					$sasistentes='';
					$sactividad='';
					$snom_tipo=_('ocupado');
					$observ = '';
				} else {
					$oTipoActiv= new TiposActividades($id_tipo_activ);
					$ssfsv=$oTipoActiv->getSfsvText();
					$sasistentes=$oTipoActiv->getAsistentesText();
					$sactividad=$oTipoActiv->getActividadText();
					$snom_tipo=$oTipoActiv->getNom_tipoText();
				}


				$txt_ctr='';
				if ($oPermCtr->have_perm_action('ver')) {
					$oEnc=new GestorCentroEncargado();
					foreach($oEnc->getCentrosEncargadosActividad($id_activ) as $oCentroEncargado) {;
						$id_ctr = $oCentroEncargado->getId_ubi();
						$Centro = new CentroDl($id_ctr);
						$nombre_ctr = $Centro->getNombre_ubi();
						$txt_ctr .= empty($txt_ctr)? $nombre_ctr : "; $nombre_ctr";
					}
				}
				
				$txt_sacds='';
				if(core\ConfigGlobal::is_app_installed('actividadessacd')) {
				    // sólo si tiene permiso
				    $aprobado = TRUE;
				    if (ConfigGlobal::mi_sfsv() == 2) {
				        $gesActividadProcesoTarea = new GestorActividadProcesoTarea();
				        $aprobado = $gesActividadProcesoTarea->getSacdAprobado($id_activ);
				    }
				    if (!core\ConfigGlobal::is_app_installed('procesos')
				        OR ($oPermSacd->have_perm_activ('ver') === true && $aprobado) )
				    {
				        $gesCargosActividad=new actividadcargos\model\entity\GestorActividadCargo();
				        foreach($gesCargosActividad->getActividadSacds($id_activ) as $oPersona) {
				            $nom_sacd = $oPersona->getApellidosNombre();
				            //separar con #. la coma la utilizo como separador de apellidos, nombre.
				            $txt_sacds .= empty($txt_sacds)? $nom_sacd : "# $nom_sacd";
				        }
				    }
				}
				
				$oTipoTarifa = new TipoTarifa(array('tarifa'=>$tarifa));
				$letra_tarifa = $oTipoTarifa->getLetra();

				$a_valores[$id_ubi][$a][1]=$f_ini;
				$a_valores[$id_ubi][$a][2]=$h_ini;
				$a_valores[$id_ubi][$a][3]=$f_fin;
				$a_valores[$id_ubi][$a][4]=$h_fin;
				$a_valores[$id_ubi][$a][5]=$ssfsv;
				$a_valores[$id_ubi][$a][6]=$sactividad;
				$a_valores[$id_ubi][$a][7]=$sasistentes;
				$a_valores[$id_ubi][$a][8]=$snom_tipo;

				$a_valores[$id_ubi][$a][9]=$nombre_ubi;
				$a_valores[$id_ubi][$a][10]=$letra_tarifa;
				$a_valores[$id_ubi][$a][11]=$txt_ctr;
				$a_valores[$id_ubi][$a][12]=$txt_sacds;
				$a_valores[$id_ubi][$a][13]=$observ;
			}
		}
		$a_cabeceras[]=ucfirst(_("empieza"));
		$a_cabeceras[]=ucfirst(_("hora ini"));
		$a_cabeceras[]=ucfirst(_("termina"));
		$a_cabeceras[]=ucfirst(_("hora fin"));
		$a_cabeceras[]=_("sf/sv");
		$a_cabeceras[]=ucfirst(_("activ."));
		$a_cabeceras[]=ucfirst(_("asist."));
		$a_cabeceras[]=ucfirst(_("tipo actividad"));
		$a_cabeceras[]=ucfirst(_("lugar"));
		$a_cabeceras[]=ucfirst(_("tar."));
		$a_cabeceras[]=ucfirst(_("centro"));
		$a_cabeceras[]=ucfirst(_("sacd"));
		$a_cabeceras[]=ucfirst(_("observaciones"));

		$oLista = new Lista();
		$oLista->setGrupos($aGrupos);
		$oLista->setCabeceras($a_cabeceras);
		$oLista->setDatos($a_valores);
		echo $oLista->listaPaginada();
		break;
}
