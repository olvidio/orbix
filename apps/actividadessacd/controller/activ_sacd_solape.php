<?php
use actividadcargos\model\entity\ActividadCargo;
use actividadcargos\model\entity\GestorActividadCargo;
use actividadcargos\model\entity\GestorCargo;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\ActividadDl;
use actividades\model\entity\GestorActividadDl;
use actividadescentro\model\entity\GestorCentroEncargado;
use asistentes\model\entity\AsistenteDl;
use core\ConfigGlobal;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoSacd;
use personas\model\entity\GestorPersona;
use personas\model\entity\Persona;
use procesos\model\entity\GestorActividadProcesoTarea;
use web\Periodo;
use function core\is_true;
use actividadcargos\model\GestorCargoOAsistente;
use actividades\model\entity\Actividad;

/**
* Esta página sirve para ejecutar las operaciones de guardar, eliminar, listar...
* que se piden desde: activ_sacd.php
*
*@param string $que
*			'orden' -> cambia el orden del cargo a uno más o menos. También borra el cargo y la asistencia.
*			'get'   -> lista de los sacd encargados (por orden cargo) con onclick para cambiar o borrar.
*			'nuevo'	-> una lista con los sacd posibles. Primero el sacd del centro encargado con (*) y después el resto.
*			'asignar'-> asigna el sacd a la actividad con cargo uno más del que exista. También poen la asistencia a la
*						actividad si es de sv.
*			'lista_activ'-> para la primera presentación. Devuelve la lista de actividades con los sacd encargados.
*						Se puede pasar el parámetro $tipo para seleccionar un tipo de actividades.
*@param string $tipo na|sg|sr|sssc|sf_na|sf_sg|sf_sr
*
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		22/11/2021.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// valores del id_cargo de tipo_cargo = sacd:
$gesCargos = new GestorCargo();
$aIdCargos_sacd = $gesCargos->getArrayCargosDeTipo('sacd');
$txt_where_cargos = implode(',',array_keys($aIdCargos_sacd));

/**
* En teoria tendria que cambiar el orden de la lista de los centros encargados
* de la actividad. Si num_orden és '+' (más importante), hago descender el orden un valor, y reordeno el resto de cargos...
*/
function ordena($id_activ,$id_nom,$num_orden) {
    global $txt_where_cargos;
    
    $aWhere = [];
    $aOperador = [];
	$aWhere['id_activ']=$id_activ;
	$aWhere['id_cargo'] = $txt_where_cargos;
	$aOperador['id_cargo']= 'IN';
	
	// Si no pongo nada me lo ordena por orden cargo.
	//$aWhere['_ordre']='id_cargo';
	$GesActividadCargos = new GestorActividadCargo();
	$cActividadCargos = $GesActividadCargos->getActividadCargos($aWhere,$aOperador);
	$i_max=count($cActividadCargos);
	for($i=0;$i<$i_max;$i++) {
		if ($cActividadCargos[$i]->getId_nom() == $id_nom) {
			switch ($num_orden) {
				case "mas":
					if ($i>=1) {
						$anterior_id_nom=$cActividadCargos[($i-1)]->getId_nom();
						if (!empty($anterior_id_nom)) {
							$cActividadCargos[($i-1)]->setId_nom($id_nom);
							if ($cActividadCargos[($i-1)]->DBGuardar() === false) {
								echo _("hay un error, no se ha guardado");
							}
							$cActividadCargos[($i)]->setId_nom($anterior_id_nom);
							if ($cActividadCargos[($i)]->DBGuardar() === false) {
								echo _("hay un error, no se ha guardado");
							}
						}
					}
					break;
				case "menos":
					if ($i<($i_max-1)) {
						$post_id_nom=$cActividadCargos[($i+1)]->getId_nom();
						if (!empty($post_id_nom)) {
							$cActividadCargos[($i+1)]->setId_nom($id_nom);
							if ($cActividadCargos[($i+1)]->DBGuardar() === false) {
								echo _("hay un error, no se ha guardado");
							}
							$cActividadCargos[($i)]->setId_nom($post_id_nom);
							if ($cActividadCargos[($i)]->DBGuardar() === false) {
								echo _("hay un error, no se ha guardado");
							}
						}
					}
					break;
			}
		}
	}
}

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qid_activ = (integer) \filter_input(INPUT_POST, 'id_activ');

$aWhere = [];
$aOperador = [];

$Qtipo = (string) \filter_input(INPUT_POST, 'tipo');
$Qyear = (integer) \filter_input(INPUT_POST, 'year');
$Qperiodo = (string) \filter_input(INPUT_POST, 'periodo');
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


$inicioIso = '2021-01-01';
$finIso = '2021-04-01';


$aWhere['f_ini'] = "'$inicioIso','$finIso'";
$aOperador['f_ini'] = 'BETWEEN';

$aWhere['status'] = ActividadAll::STATUS_TERMINADA;
$aOperador['status'] = "<";

$aWhere['_ordre']='f_ini';

$GesActividades = new GestorActividadDl();
$cActividades = $GesActividades->getActividades($aWhere,$aOperador);

$GesSacd = new GestorPersona();
// Para los de la dl y de_paso:
$aClases = [];
$aClases[] = array('clase'=>'PersonaDl','get'=>'getPersonasDl');
$aClases[] = array('clase'=>'PersonaEx','get'=>'getPersonasEx');
$GesSacd->setClases($aClases);

$mi_dl = ConfigGlobal::mi_delef();
$aWhere = [];
$aWhere['sacd'] = 't';
$aWhere['dl'] = $mi_dl;
$aWhere['_ordre'] = 'apellido1,apellido2,nom';
$cSacds = $GesSacd->getPersonas($aWhere);


$gesCargoOAsistente = new GestorCargoOAsistente();
$a_solapes = $gesCargoOAsistente->getSolapes($cSacds, $cActividades);

$titulo=ucfirst(_("listado de sacd con actividades incompatibles"));
    
$a_cabeceras = [];
$a_cabeceras[]=ucfirst(_("sacd"));
$a_cabeceras[]=ucfirst(_("actividades"));

$i=0;
$sin=0;
$a_valores = [];
foreach ($a_solapes as $id_nom => $aId_activ) {
    $i++;
    
    $oPersona = Persona::NewPersona($id_nom);
    $ap_nom = $oPersona->getApellidosNombre();
    
    $a_valores[$i][0]=$id_nom;
    $a_valores[$i][1]=$ap_nom;
    
    $a_nom_activ = [];
    foreach($aId_activ as $id_activ) {
        $oActividad = new Actividad($id_activ);
        $nom_activ = $oActividad->getNom_activ();
        $status = $oActividad->getStatus();
        // Fase en la que se en cuentra
        $GesActividadProceso=new GestorActividadProcesoTarea();
        $sacd_aprobado = $GesActividadProceso->getSacdAprobado($id_activ);
        if ($sacd_aprobado === TRUE) {
            $clase = 'plaza4'; // color de plaza asignada.
        } else {
            $clase = '';
        }
        if ($status == ActividadAll::STATUS_PROYECTO) {
            $clase = 'wrong-soft';
        }
        $a_nom_activ[] = $nom_activ;
    }

    $a_valores[$i][2]=$a_nom_activ;
    $a_valores[$i][5]=$clase;
}

?>

<h3><?= $titulo ?></h3>
<span class="comentario">
<?= _("NOTA: Si termina y empieza el mismo día en el mismo lugar no se pone."); ?>
</span>
<br>
<table><tr>
<?php
foreach ($a_cabeceras as $cabecera) {
    echo "<td>$cabecera</td>";
}
foreach ($a_valores as $valores) {
    $id_nom=$valores[0];
    $nom_sacd=$valores[1];
    $clase=$valores[5];
    $txt_activ="";
    if (is_array($valores[2])) {
        foreach ($valores[2] as $nom_activ){
            $txt_activ.="<span> ${nom_activ};</span><br>";
        }
    }
    $txt_id=$id_activ."_sacds";
    echo "<tr class=$clase id=$id_nom ><td>$valores[1]</td><td id=$txt_id>$txt_activ</td></tr>";
}
?>
</table>