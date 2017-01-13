<?php 
use actividades\model as actividades;
use actividadcargos\model as actividadcargos;
use asistentes\model as asistentes;
use personas\model as personas;
use usuarios\model as usuarios;
use ubis\model as ubis;

/**
* Esta página lista los asistentes a una actividad seleccionada
*
* Admite dos tipos de lista: una simple 
* y otra con datos útiles al cl de la actividad
*
*@package	delegacion
*@subpackage	actividades
*@author	Josep Companys
*@since		15/5/02.
*		
*/
/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$id_usuario= core\ConfigGlobal::mi_id_usuario();
$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'ordenApellidos'));
$Pref_ordenApellidos=$oPref->getPreferencia();

if (!empty($_POST['sel'])) { //vengo de un checkbox
	$id_sel=$_POST['sel'];
    $id_pau=strtok($_POST['sel'][0],"#");
    $nom_activ=strtok("#");
	if (empty($nom_activ) && !empty($id_pau)) {
		$oActividad = new actividades\Actividad($id_pau);
		$nom_activ = $oActividad->getNom_activ();
	}
	$oPosicion->addParametro('id_sel',$id_sel);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id);
}

$queSel = empty($_POST['queSel'])? '' : $_POST['queSel'];

$gesAsistentes = new asistentes\GestorAsistente();



function datos($oPersona,$tipo) {
	$estudios = '';
	$profesion = '';
	$edad = '';
	$eap = '';
	$observ = '';
	$obj_persona = get_class($oPersona);
	$obj_persona = str_replace("personas\\model\\",'',$obj_persona);
	Switch($obj_persona) {
		case 'PersonaN':
		case 'PersonaNax':
		case 'PersonaAgd':
		case 'PersonaS':
		case 'PersonaSSSC':				
		case 'PersonaDl':
			$profesion=$oPersona->getProfesion();
			$f_nacimiento=$oPersona->getF_nacimiento();
			$observ=$oPersona->getObserv();
			$inc=$oPersona->getInc();
			if ($inc=="?") {
				$f_inc="?";
			} else {
				$get="getF_$inc()";
				$f_inc=$oPersona->$get;
			}
			$edad=$oPersona->getEdad();

			//$estudios=$oPersona->getEstudios();
			// cargos
			/*
			$oGesCargo = new GestorCargoCl();
			$aWhere['id_nom'] = $id_nom;
			$aWhere['f_cese'] = '';
			$aOperadores['f_cese'] = 'IS NULL';
			$aWhere['f_ult_nombramiento'] = '';
			$aOperadores['f_ult_nombramiento'] = 'IS NOT NULL';
			$aWhere['tipo_cargo'] = 'of-';
			$aOperadores['tipo_cargo'] = '!~';
			$oGesCargos = new GestorCargoCl();
			$cCargos = $oGesCargos->getCargosCl($aWhere,$aOperadores);

			$eap='';
			if ($id_tabla == 'a' && ($oPersona->getCel() == 's')) { $eap="cel"; }
			if (is_array($cCargos) && count($cCargos) > 0) $eap="cl";
			*/
			$eap='?';
			break;
		case 'PersonaIn':
		case 'PersonaEx':
			$profesion=$oPersona->getProfesion();
			$edad=$oPersona->getEdad();
			$inc=$oPersona->getInc();
			$f_inc=$oPersona->getF_inc();
			if (!empty($inc)) {
			$inc_f_inc=$inc .' : '. $f_inc;
			}
			$eap=$oPersona->getEap();
			$observ=$oPersona->getObserv();
			break;
	}

	// ahora los de sm no quieren la inc:
	if ( $tipo == 1 ) {
	    $txt_cl="<td class=contenido_especial>$estudios</td><td class=contenido_especial>$profesion</td><td class=contenido_especial>$edad</td><td class=contenido_especial>$eap</td><td class=contenido_especial>$observ</td>";
	} else {
	    $txt_cl="<td class=contenido_especial>$estudios</td><td class=contenido_especial>$profesion</td><td class=contenido_especial>$edad</td><td class=contenido_especial>$inc: $f_inc</td><td class=contenido_especial>$eap</td><td class=contenido_especial>$observ</td>";
	}
    return $txt_cl;
}

// -----------------------------------------------------------


// primero el cl:
$c=0;
$num=0;
$a_valores=array();
$aListaCargos=array();
$msg_err = '';
// primero los cargos
if (core\configGlobal::is_app_installed('actividadcargos')) {
	$GesCargosEnActividad=new actividadcargos\GestorActividadCargo();
	$cCargosEnActividad = $GesCargosEnActividad->getActividadCargos(array('id_activ'=>$id_pau));
	$mi_sfsv = core\ConfigGlobal::mi_sfsv();
	foreach($cCargosEnActividad as $oActividadCargo) {
		$c++;
		$num++; // número total de asistentes.
		$id_nom=$oActividadCargo->getId_nom();
		$aListaCargos[]=$id_nom;
		$id_cargo=$oActividadCargo->getId_cargo();
		$oCargo = new actividadcargos\Cargo(array('id_cargo'=>$id_cargo));
		$tipo_cargo=$oCargo->getTipo_cargo();		
		// para los sacd en sf
		if ($tipo_cargo == 'sacd' && $mi_sfsv == 2) {
			continue;
		}

		$oPersona = personas\Persona::NewPersona($id_nom);
		if (!is_object($oPersona)) {
			$msg_err .= "<br>$oPersona con id_nom: $id_nom";
			continue;
		}
		$oCargo=new actividadcargos\Cargo($id_cargo);
		if ($Pref_ordenApellidos== 'nom_ap') {
			$nom = $oPersona->getNombreApellidos();
		} else {
			$nom = $oPersona->getApellidosNombre();
		}

		$cargo=$oCargo->getCargo();
		$puede_agd=$oActividadCargo->getPuede_agd();
		$observ=$oActividadCargo->getObserv();
		$ctr_dl=$oPersona->getCentro_o_dl();

		$puede_agd=='t' ? $chk_puede_agd="si" : $chk_puede_agd="no" ;

		// ahora miro si también asiste:
		$aWhere=array('id_activ'=>$id_pau,'id_nom'=>$id_nom);
		$aOperador=array('id_activ'=>'=','id_nom'=>'=');
		// me aseguro de que no sea un cargo vacio (sin id_nom)
		if (!empty($id_nom) && $cAsistente=$gesAsistentes->getAsistentes($aWhere,$aOperador)) {
			if(is_array($cAsistente) && count($cAsistente)>1) {
				$tabla = '';
				foreach ($cAsistente as $Asistente) {
					$tabla .= "<li>".$Asistente->getNomTabla()."</li>";
				}
				$msg_err .= "ERROR: más de un asistente con el mismo id_nom<br>";
				$msg_err .= "<br>$nom(".$oPersona->getId_tabla().")<br><br>En las tablas:<ul>$tabla</ul>";
				exit ("$msg_err");
			}
			$propio=$cAsistente[0]->getPropio();
			$falta=$cAsistente[0]->getFalta();
			$est_ok=$cAsistente[0]->getEst_ok();
			$observ1=$cAsistente[0]->getObserv();

			if ($propio=='t') {
				$chk_propio=_("si");
			} else { 
				$chk_propio=_("no") ;
			}
			$falta=='t' ? $chk_falta=_("si") : $chk_falta=_("no") ;
			$est_ok=='t' ? $chk_est_ok=_("si") : $chk_est_ok=_("no") ;
			$asis="t";
			$a_valores[$c][3]=$chk_propio;
			$a_valores[$c][4]=$chk_est_ok;
			$a_valores[$c][5]=$chk_falta;
		} else {
			$a_valores[$c][3]= array( 'span'=>3, 'valor'=> _("no asiste"));
			$observ1='';
			$num--;
			$asis="f";
		}

		$a_valores[$c][1]=$cargo;
		$a_valores[$c][2]="$nom  ($ctr_dl)";
		$a_valores[$c][6]="$observ $observ1";
		$a_valores[$c][7]=$oPersona;
	}
}
// ahora los asistentes sin los cargos
$asistentes = array();
$msg_err = '';
foreach($gesAsistentes->getAsistentes(array('id_activ'=>$id_pau)) as $oAsistente) {
	$c++;
	$num++;
	$id_nom=$oAsistente->getId_nom();
	// si ya está en la lista voy a por otro asistente
	if(in_array($id_nom,$aListaCargos)) { $num--; continue; }

	$oPersona = personas\Persona::NewPersona($id_nom);
	if (!is_object($oPersona)) {
		$msg_err .= "<br>$oPersona con id_nom: $id_nom";
		continue;
	}
	$nom=$oPersona->getApellidosNombre();
	$ctr_dl=$oPersona->getCentro_o_dl();

	$propio=$oAsistente->getPropio();
	$falta=$oAsistente->getFalta();
	$est_ok=$oAsistente->getEst_ok();
	$observ=$oAsistente->getObserv();

	if (core\configGlobal::is_app_installed('actividadplazas')) {
		$plaza=$oAsistente->getPlaza();
		if ($plaza < 4) continue;
	}
	if ($propio=='t') {
		$chk_propio=_("si");
	} else { 
		$chk_propio=_("no") ;
	}
	$falta=='t' ? $chk_falta=_("si") : $chk_falta=_("no") ;
	$est_ok=='t' ? $chk_est_ok=_("si") : $chk_est_ok=_("no") ;
			
	$a_val[2]="$nom  ($ctr_dl)";
	$a_val[3]=$chk_propio;
	$a_val[4]=$chk_est_ok;
	$a_val[5]=$chk_falta;
	$a_val[6]=$observ;
	$a_val[7]=$oPersona;
	$asistentes[$nom] = $a_val;
}
uksort($asistentes,"core\strsinacentocmp");

$c = 0;
if (core\configGlobal::is_app_installed('actividadcargos')) {
	$c = count($a_valores);
}
foreach ($asistentes as $nom => $val) {
	$c++;
	$val[1] = "$c.-";
	$a_valores[$c] = $val;
}


if (!empty($msg_err)) { echo $msg_err; }


/* ---------------------------------- html --------------------------------------- */

echo $oPosicion->atras();
?>
<table cellpadding="2">
<tr>
<th class=titulo_inv colspan=6><?php echo $nom_activ;?></th>
<tr><th class=subtitulo_inv colspan=6><?php echo ucfirst(_("relación de asistentes")); ?></th>
</tr>
<tr><tr><td><br></td></tr>
<tr>
<?php 
$tipo =  '';
if ($queSel == "listcl") {
    // los de sm no quieren la inc
    // busco el tipo de actividad para no poner la inc en caso de n
	$oActividad = new actividades\Actividad(array('id_activ'=>$id_pau));
	$id_tipo_activ = $oActividad->getId_tipo_activ();
    $tipo=substr($id_tipo_activ,1,1); // 1 para los n
    if ( $tipo == 1 ) { //n.
		$etiqueta_inc="";
    } else {
		$etiqueta_inc="<th class=subtitulo_inv>inc</th>";
    }
    echo "<tr><th></th><th class=subtitulo_inv>".ucfirst(_("nombre"))."</th><th></th><th class=subtitulo_inv>".ucfirst(_("estudios"))."</th><th class=subtitulo_inv>".ucfirst(_("profesión"))."</th><th class=subtitulo_inv>".ucfirst(_("edad"))."</th>$etiqueta_inc<th class=subtitulo_inv>"._("eap")."</th><th class=subtitulo_inv>".ucfirst(_("observaciones"))."</th></tr>";
}

$txt_cl = '';
foreach ($a_valores as $c => $val) {
	$oPersona = $val[7];
    if ($queSel=="listcl") { $txt_cl= datos($oPersona,$tipo); }
    
	echo "<tr><td class=contenido_especial align=RIGHT>$c</td><td class=contenido_especial>$val[2]</td><td class=contenido_especial></td>$txt_cl</tr>";
}

?>
</table>
