<?php
use core\ConfigGlobal;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargoSacd;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use encargossacd\model\entity\GestorEncargoSacdObserv;
use encargossacd\model\entity\GestorEncargoTexto;
use personas\model\entity\GestorPersona;
use personas\model\entity\Persona;
use ubis\model\entity\Ubi;
use encargossacd\model\EncargoFunciones;
use web\DateTimeLocal;

/**
* Esta página muestra los encargos de un sacd. 
*
*@package	delegacion
*@subpackage	des
*@author	Daniel Serrabou
*@since		12/12/06.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qsel = (string) \filter_input(INPUT_POST, 'sel');

/* claves:
 *       "com_sacd";
 *       "t_titular"
 *       "t_secc"
 *       "t_mañanas"
 *       "t_tarde1"
 *       "t_tarde2"
 *       "t_suplente"
 *       "t_colaborador"
 *       "t_otros"
 *       "t_observ"
 */
$oGesEncargoTextos = new GestorEncargoTexto();
$cEncargoTextos = $oGesEncargoTextos->getEncargoTextos();
$a_txt_comunicacion = [];
foreach ($cEncargoTextos as $oEncargoTexto) {
    $clave = $oEncargoTexto->getClave();
    $idioma = $oEncargoTexto->getIdioma();
    $texto = $oEncargoTexto->getTexto();
    $a_txt_comunicacion[$idioma][$clave] = $texto;
}

// para ordenar los modos: 'modo'=>orden
$hoy_iso = date('Y-m-d');
$oDateLocal = new DateTimeLocal();
$hoy_local = $oDateLocal->getFromLocal('.'); 

$array_orden=array( '1'=>1, '2'=>2, '3'=>2, '4'=>4, '5'=>3, '6'=>5 );
// ciudad de la dl
$oEncargoFunciones = new EncargoFunciones();
$poblacion = $oEncargoFunciones->getLugar_dl();
$lugar_fecha= "$poblacion, $hoy_local";

// los sacd
$GesPersonas = new GestorPersona();
$aWhere = [];
$aOperador = [];
switch ($Qsel) {
	case "nagd":
		$aWhere['id_tabla'] = '^n|^a';
		$aWhere['situacion'] = 'A';
		$aWhere['sacd'] = 't';
		$aWhere['dl'] = ConfigGlobal::mi_dele();
		$aWhere['_ordre'] = 'apellido1,apellido2,nom';
		$aOperador['id_tabla'] = '~';
		$cPersonas = $GesPersonas->getPersonas($aWhere,$aOperador);
		break;
	case "sssc":
		$aWhere['id_tabla'] = '^sss';
		$aWhere['situacion'] = 'A';
		$aWhere['sacd'] = 't';
		$aWhere['dl'] = ConfigGlobal::mi_dele();
		$aWhere['_ordre'] = 'apellido1,apellido2,nom';
		$aOperador['id_tabla'] = '~';
		$cPersonas = $GesPersonas->getPersonas($aWhere,$aOperador);
	    //$sql_sacd="SELECT id_nom,".na()." as nom_ap,lengua FROM vp_sacd WHERE id_tabla ~ '^sss' AND dl='".ConfigGlobal::$dele."' ORDER BY apellido1,apellido2,nom";
		break;
}
$array_modo=array();
$s=0;
foreach ($cPersonas as $oPersona) {
	$s++;
	$id_nom=$oPersona->getId_nom();
	$array_modo[$s]['nom_ap']=$oPersona->getNombreApellidos();
	$lengua=$oPersona->getLengua();
	
	if (empty($a_txt_comunicacion[$lengua])) {
	    echo sprintf(_("falta definir el texto de comunicación en este idioma: %s"),$lengua);
	    // pongo el primero
	    reset($a_txt_comunicacion);
	    if (empty(current($a_txt_comunicacion))) {
            $a_txt_comunicacion[$lengua] = '?';
	    } else {
            $a_txt_comunicacion[$lengua] = current($a_txt_comunicacion);
	    }
	}
    $array_modo[$s]['txt'] = $a_txt_comunicacion[$lengua];
    
	// busco las observaciones (si las hay)
	$GesTareaSacdObserv = new GestorEncargoSacdObserv();
	$cTareaSacdObserv = $GesTareaSacdObserv->getEncargoSacdObservs(array('id_nom'=>$id_nom));
	if (is_array($cTareaSacdObserv) && count($cTareaSacdObserv) > 0) {
		$observ = $cTareaSacdObserv[0]->getObserv();
	} else {
		$observ = '';
	}
	/* busco los datos del encargo que se tengan */
	$GesTareas = new GestorEncargoSacd();
	$cTareasSacd1 = $GesTareas->getEncargosSacd(array('id_nom'=>$id_nom,'f_fin'=>'x','_ordre'=>'modo'),array('f_fin'=>'IS NULL'));
	$cTareasSacd2 = $GesTareas->getEncargosSacd(array('id_nom'=>$id_nom,'f_fin'=>$hoy_iso,'_ordre'=>'modo'),array('f_fin'=>'>'));
	$cTareasSacd = $cTareasSacd1 + $cTareasSacd2 ;
	foreach ($cTareasSacd as $oTareaSacd) {
		$id_enc = $oTareaSacd->getId_enc();
		$modo=$oTareaSacd->getModo();
		$oEncargo = new Encargo($id_enc);
		$id_tipo_enc = $oEncargo->getId_tipo_enc();
		// paso a texto para poder coger el segundo carcater.
		$id_tipo_enc_txt = (string)$id_tipo_enc;
		if ($id_tipo_enc_txt[0] == 4 || $id_tipo_enc_txt[0] == 7 || $id_tipo_enc_txt[0] == 8 ) continue;
		$sup_tit="";
		$desc_enc=$oEncargo->getDesc_enc();
		$id_ubi=$oEncargo->getId_ubi();
		$grupo = $array_orden[$modo];
		if (!empty($id_ubi)) { // en algunos encargos no hay ubi
			$oUbi = new Ubi($id_ubi);
			$nombre_ubi=$oUbi->getNombre_ubi();
		} else {
			$nombre_ubi="";
		}
		$seccion='';
		if (!empty($id_tipo_enc)) {
			if ($id_tipo_enc_txt[1] == 2) { $seccion="sf"; } else { $seccion="sv"; }
		}

		if ($modo==2 || $modo==3) {
			// busco el suplente
			$cTareasSacd1 = $GesTareas->getTareasSacd(array('id_enc'=>$id_enc,'f_fin'=>'x','modo'=>4),array('f_fin'=>'IS NULL'));
			if (is_array($cTareasSacd1) && count($cTareasSacd1) == 0 ) {
				$cTareasSacd1 = $GesTareas->getTareasSacd(array('id_enc'=>$id_enc,'f_fin'=>$hoy_iso,'modo'=>4),array('f_fin'=>'>'));
			}
			if (is_array($cTareasSacd1) && count($cTareasSacd1) == 1 ) {
				$id_nom_sup = $cTareasSacd1[0]->getId_nom();
				$oSacd  = new Persona($id_nom_sup);
				$sup_tit=$oSacd->getNombreApellidos();
			} else {
				$sup_tit='';
			}
		} elseif ($modo==4) {
			// busco el titular
			// busco el suplente
			$cTareasSacd1 = $GesTareas->getTareasSacd(array('id_enc'=>$id_enc,'f_fin'=>'x','modo'=>'[23]'),array('modo'=>'~','f_fin'=>'IS NULL'));
			if (is_array($cTareasSacd1) && count($cTareasSacd1) == 0 ) {
				$cTareasSacd1 = $GesTareas->getTareasSacd(array('id_enc'=>$id_enc,'f_fin'=>$hoy_iso,'modo'=>'[23]'),array('modo'=>'~','f_fin'=>'>'));
			}
			if (is_array($cTareasSacd1) && count($cTareasSacd1) == 1 ) {
				$id_nom_tit = $cTareasSacd1[0]->getId_nom();
				$oSacd  = new Persona($id_nom_tit);
				$sup_tit=$oSacd->getNombreApellidos();
			} else {
				$sup_tit='';
			}
		}

		// horario
		$aWhere = array();
		$aOperador = array();
		$GesHorario = new GestorEncargoSacdHorario();
		$aWhere['id_enc']=$id_enc;
		$aWhere['id_nom']=$id_nom;
		$aWhere['f_fin']="'$hoy_iso'";
		$aOperador['f_fin']='>';

		$cHorarios1 = $GesHorario->getTareaHorariosSacd($aWhere,$aOperador);
		$aOperador['f_fin']='IS NULL';
		$cHorarios2 = $GesHorario->getTareaHorariosSacd($aWhere,$aOperador);
		$cHorarios = $cHorarios1 + $cHorarios2;

		$dedic_m="";
		$dedic_t="";
		$dedic_v="";
		foreach ($cHorarios as $oTareaHorarioSacd) {
			$modulo=$oTareaHorarioSacd->getDia_ref();
			switch ($modulo) {
				case "m":
					$dedic_m=$oTareaHorarioSacd->getDia_inc();
					break;
				case "t":
					$dedic_t=$oTareaHorarioSacd->getDia_inc();
					break;
				case "v":
					$dedic_v=$oTareaHorarioSacd->getDia_inc();
					break;
			}
		}

		// estudio, descanso y otros como grupo 6
		if ($id_tipo_enc==5020 || $id_tipo_enc==5030 || $id_tipo_enc==6000 ) {
			$grupo=6;
			if (array_key_exists($desc_enc, $array_trad[$lengua]) ) {
				$nombre_ubi=$array_trad[$lengua][$desc_enc];
				$desc_enc=$array_trad[$lengua][$desc_enc];
			} else {
				$nombre_ubi=$desc_enc;
			}
			$dedic_m = $oEncargoFunciones->dedicacion($id_nom,$id_enc,$lengua);
		}

		// las colatios y rtm los pongo al final
		if ($id_tipo_enc==4002 || $id_tipo_enc==1110 || $id_tipo_enc==1210 ) {
			$otros_enc.=$desc_enc;
			continue;
		}
		if (!empty($id_enc)) { 
			$array_enc=array( "desc_enc" => $desc_enc,
							"nombre_ubi" => $nombre_ubi,
							"seccion" => $seccion,
							"dedic_m" => $dedic_m,
							"dedic_t" => $dedic_t,
							"dedic_v" => $dedic_v,
							"sup_tit" => $sup_tit
						);
			$array_modo[$s]['grupo'][$grupo][]= $array_enc; 
		}
	}
	if (!empty($observ)) { $array_modo[$s][7][]= array( "desc_enc" => $observ); }
	//include ("list_com_sacd.html");
} // fin del while de los sacd

$a_campos = ['oPosicion' => $oPosicion,
    'array_modo' => $array_modo,
    'Qsel' => $Qsel,
    'lugar_fecha' => $lugar_fecha,
];

$oView = new core\View('encargossacd/controller');
echo $oView->render('listas_com_sacd.phtml',$a_campos);
