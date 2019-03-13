<?php
// INICIO Cabecera global de URL de controlador *********************************

use cartaspresentacion\model\entity\GestorCartaPresentacion;
use core\ConfigGlobal;
use ubis\model\entity\Centro;
use ubis\model\entity\Direccion;
use ubis\model\entity\GestorCentro;
use ubis\model\entity\GestorDireccionCtr;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)  \filter_input(INPUT_POST, 'que');

$solo_dl = 0;
switch ($Qque) {
	case "lista_dl":
		$solo_dl = 1;
		$mi_dele = ConfigGlobal::mi_dele();
	case "lista_todo":
		$ordenar_dl = 1;
		$GesPresentacion = new GestorCartaPresentacion();
		$colPresentacion = $GesPresentacion->getCartasPresentacion();
		$a_mega = array();
		foreach ($colPresentacion as $oPresentacion) {
			$id_ubi = $oPresentacion->getId_ubi();

			$oCentro = new Centro($id_ubi);
		    $dl = $oCentro->getDl();
			if ($solo_dl == 1 && $dl != $mi_dele) continue;
			$a_mega = array_merge_recursive( $a_mega, mega_array($oPresentacion,$oCentro,$ordenar_dl));
		}
		echo lista_cartas($a_mega,$ordenar_dl);
		break;
	case "get":
		$ordenar_dl = 1;
        $Qpoblacion = (string)  \filter_input(INPUT_POST, 'poblacion');
        $Qpais = (string)  \filter_input(INPUT_POST, 'pais');
        $Qregion = (string)  \filter_input(INPUT_POST, 'region');
        $Qdl = (string)  \filter_input(INPUT_POST, 'dl');
		// buscar los ctr y de allí mirar los que tienen cartas de presentacion.
		$aWhere = array();
		$aOperador = array();
		if (!empty($Qpais) || !empty($Qpoblacion)) {
			if (!empty($Qpoblacion)) {
				$aWhere['poblacion'] = $Qpoblacion;
				$aOperador['poblacion'] = 'sin_acentos';
			}
			if (!empty($Qpais)) {
				$aWhere['pais'] = $Qpais;
				$aOperador['pais'] = 'sin_acentos';
			}

			$GesDirecciones = new GestorDireccionCtr();
			$cDirecciones = $GesDirecciones->getDirecciones($aWhere,$aOperador);
			$a_mega = array();
			foreach ($cDirecciones as $oDireccion) {
			    $cId_ubis = $oDireccion->getUbis();
			    $cCentros = [];
			    foreach ($cId_ubis as $oUbi) {
			        $oCentro = new Centro($oUbi->getId_ubi());
			        if ($oCentro->getStatus()) {
			            $cCentros[] = $oCentro;
			        }
			    }
				foreach ($cCentros as $oCentro) {
					$id_ubi = $oCentro->getId_ubi();
					$GesPresentacion = new GestorCartaPresentacion();
					$colPresentacion = $GesPresentacion->getCartasPresentacion(array('id_ubi'=>$id_ubi));
					if (!empty($colPresentacion) && !empty($colPresentacion[0])) {
						$oPresentacion=$colPresentacion[0];
						$a_mega = array_merge_recursive($a_mega,mega_array($oPresentacion,$oCentro,$ordenar_dl));
					}
				}
			}
			// Extiendo la búsqueda al campo zona
			if (!empty($Qpoblacion)) {
				$GesPresentacion = new GestorCartaPresentacion();
				$colPresentacion = $GesPresentacion->getCartasPresentacion(array('zona'=>$Qpoblacion),array('zona'=>'sin_acentos'));
				foreach ($colPresentacion as $oPresentacion) {
					$id_ubi = $oPresentacion->getId_ubi();
					$oCentro = new Centro($id_ubi);
					$a_mega = array_merge_recursive($a_mega,mega_array($oPresentacion,$oCentro,$ordenar_dl));
				}
			}

			echo lista_cartas($a_mega,$ordenar_dl);
		}
		if (!empty($Qregion)) {
			$aWhere['region'] = $Qregion;
			$aOperador = array();

			$GesCentros = new GestorCentro();
			$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
			$a_mega = array();
			foreach ($cCentros as $oCentro) {
				$id_ubi = $oCentro->getId_ubi();
				$GesPresentacion = new GestorCartaPresentacion();
				$colPresentacion = $GesPresentacion->getCartasPresentacion(array('id_ubi'=>$id_ubi));
				if (!empty($colPresentacion) && !empty($colPresentacion[0])) {
					$oPresentacion=$colPresentacion[0];
					$a_mega = array_merge_recursive($a_mega,mega_array($oPresentacion,$oCentro,$ordenar_dl));
				}
			}
			echo lista_cartas($a_mega,$ordenar_dl);
		}
		if (!empty($Qdl)) {
			$aWhere['dl'] = $Qdl;
			$aOperador = array();

			$GesCentros = new GestorCentro();
			$cCentros = $GesCentros->getCentros($aWhere,$aOperador);
			$a_mega = array();
			foreach ($cCentros as $oCentro) {
				$id_ubi = $oCentro->getId_ubi();
				$GesPresentacion = new GestorCartaPresentacion();
				$colPresentacion = $GesPresentacion->getCartasPresentacion(array('id_ubi'=>$id_ubi));
				if (!empty($colPresentacion) && !empty($colPresentacion[0])) {
					$oPresentacion=$colPresentacion[0];
					$a_mega = array_merge_recursive($a_mega,mega_array($oPresentacion,$oCentro,$ordenar_dl));
				}
			}
			echo lista_cartas($a_mega,$ordenar_dl);
		}
		break;
}

function mega_array($oPresentacion,$oCentro,$ordenar_dl) {
    $a_mega = [];
	$pres_nom = $oPresentacion->getPres_nom();
	$pres_telf = $oPresentacion->getPres_telf();
	$pres_mail = $oPresentacion->getPres_mail();
	$zona = $oPresentacion->getZona();
	
	$dl = $oCentro->getDl();
	$region = $oCentro->getRegion();
	$tipo_ctr = $oCentro->getTipo_ctr();
	$tipo_labor = $oCentro->getTipo_labor();
	$id_ctr_padre = $oCentro->getId_ctr_padre();

	$cDirecciones = $oCentro->getDirecciones();

	$direccion = '';
	$nom_sede = '';
	$a_p = '';
	$c_p = '';
	$poblacion = '';
	$pais = '';
	$telf = '';
	$a_direccion = array();

	$d = 0;
	foreach ($cDirecciones as $oDireccion) {
	    $d++;
	    if ($d > 1) { continue; } // Solo una
	    $direccion = $oDireccion->getDireccion();
	    $poblacion = $oDireccion->getPoblacion();
	    $c_p = $oDireccion->getC_p();
	    $nom_sede = $oDireccion->getNom_sede();
	}

	$telf = $oCentro->getTeleco("telf","*"," / ") ;
	$fax = $oCentro->getTeleco("fax","*"," / ");
	if (!empty($fax)) {
		$fax = format_telf ($fax);
		$telf .= ' fax:'.$fax;
	}
	// si es una dl o r fuera de España, pongo el e-mail del centro.
	if ($region != 'H' && (strpos($tipo_ctr,'cr') !== false OR strpos($tipo_ctr,'dl') !== false)) {
		// 15 es el id para otros asuntos ( 20 es para asuntos de gobierno).
		$mail = $oCentro->getTeleco("e-mail","15"," / ");
		if (!empty($mail)) {
			$pres_mail .= 'mail casa: '.$mail;
		}
	}

	$a_direccion = array();
	if (!empty($id_ctr_padre))	{
		$oCentro1 = new Centro($id_ctr_padre);
        $cDirecciones1 = $oCentro1->getDirecciones();
        if (!empty($cDirecciones1)) {
            $oDireccion1 = $cDirecciones1[0];
			$telf1 = $oCentro1->getTeleco("telf","*"," / ") ;
			//$telf1 .= 'fax:'.teleco($id_ctr_padre,"fax","*"," / ") ;
			$a_direccion = array('direccion' => $oDireccion1->getDireccion(),
							'a_p'       => $oDireccion1->getA_p(),
							'c_p'       => $oDireccion1->getC_p(),
							'poblacion' => $oDireccion1->getPoblacion(),
							'telf'		=> $telf1	);	
		}
	}

	$a_texto = array ('pres_nom' => $pres_nom,
					'pres_telf' => $pres_telf,
					'pres_mail' => $pres_mail,
					'direccion' => $direccion,
					'nom_sede'  => $nom_sede,
					'a_p'       => $a_p,
					'c_p'       => $c_p,
					'poblacion' => $poblacion,	
					'telf' => $telf,	
					'a_direccion' => $a_direccion);
	//agd, n, sssc
	$tipo = '';
	if (($tipo_labor & 128) == 128) $tipo = 'agd';
	if (($tipo_labor & 256) == 256) $tipo = 'numerarios';
	if (($tipo_labor & 64) == 64) $tipo = 's';
	if (($tipo_labor & 32) == 32) $tipo = 'sss+';
	$edad = '';
	if (($tipo_labor & 2) == 2) $edad = 'jóvenes';
	if (($tipo_labor & 1) == 1) $edad = 'mayores';
	if (($tipo_labor & 4) == 4) $edad = 'universitarios';
	if (($tipo_labor & 8) == 8) $edad = 'bachilleres';
	//zona
	if (!empty($zona)) $edad .= "<br>$zona";

	$poblacion .= empty($pais)? '' :  '<br>('.$pais.')';
	if ($ordenar_dl == 1) {
		$a_mega[$tipo][$dl][$poblacion][$edad]= datos_a_celdas($a_texto);
	} else {
		$a_mega[$tipo][$poblacion][$edad]= datos_a_celdas($a_texto);
	}
	return $a_mega;
}

function datos_a_celdas($a_texto){
	//$texto = "$pres_nom, $pres_telf, $pres_mail, $direccion<br>$a_p<br>$c_p $poblacion";	
	$pres_nom   = $a_texto['pres_nom'];
	$pres_telf  = $a_texto['pres_telf'];  
	$pres_telf = format_telf ($pres_telf);
	$pres_mail  = $a_texto['pres_mail']; 
	$direccion  = $a_texto['direccion'];  
	$nom_sede   = $a_texto['nom_sede'];        
	$a_p        = $a_texto['a_p'];        
	$c_p        = $a_texto['c_p'];        
	$poblacion	= $a_texto['poblacion'];  
	$telf		= $a_texto['telf'];  
	$telf = format_telf ($telf);
	$a_direccion = $a_texto['a_direccion'];  

	$html = '';
	$nom_sede = empty($nom_sede)? '' : "$nom_sede<br>";
	$a_p = empty($a_p)? '' : "$a_p<br>";
	$col1 = "$pres_nom<br>$nom_sede$direccion<br>$a_p$c_p $poblacion";	
	$col2 = "$pres_telf<br>$pres_mail<br>$telf";	
	if (!empty($a_direccion)) {
		$d1 = $a_direccion['direccion'];  
		//$a1 = $a_direccion['a_p'];        
		$c1 = $a_direccion['c_p'];        
		$p1 = $a_direccion['poblacion'];  
		$tf1 = $a_direccion['telf'];  
		$tf1 = format_telf ($tf1);

		$col1 .= "<br>---<br>$d1<br>$c1  $p1";	
		$col2 .= "<br>---<br>$tf1";
	}

	$html = "<td class=\"line-top\">$col1</td><td class=\"line-top\">$col2</td>";
	return $html;
}
function lista_cartas($a_mega,$ordenar_dl){
	//print_r($a_mega);
	// cartas agd
	$html = '';
	$class = "class=\"line-top\"";
	if ($ordenar_dl == 1) {
		foreach ($a_mega as $tipo => $a_dl_pob_edad) {
			ksort($a_dl_pob_edad);
			$html .= '<h3>';
			$html .= sprintf(_("Cartas de presentación de %s"),$tipo);
			$html .= '</h3>';
			$dl_anterior = '';
			foreach ($a_dl_pob_edad as $dl => $a_pob_edad) {
				ksort($a_pob_edad);
				if ($dl != $dl_anterior) {
					$html .= "</h3>$dl - $tipo</h3>";
				}
				$poblacion_anterior = '';
				$html .= '<table>';
				foreach ($a_pob_edad as $poblacion => $a_edad) {
					krsort($a_edad); // primero m, después j
					if ($poblacion != $poblacion_anterior) {
						$html .= "<tr><td $class>".strtoupper($poblacion)."</td>";
					}
					$f=0;
					foreach ($a_edad as $edad => $texto) {
						$f++;
						if ($f > 1)	{ $html .= "<tr><td></td>"; }
						if (is_array($texto)) {
							$ff = 0;
							foreach ($texto as $key=>$txt) {
								$ff++;
								if ($ff > 1)	{ 
									$html .= "<tr><td></td><td $class></td>";
								} else {
									$html .= "<td $class>$edad</td>";
								}
								$html .= "$txt</tr>";
							}
						} else {
							$html .= "<td $class>$edad</td>$texto</tr>";
						}
					}
					$poblacion_anterior = $poblacion;
				}
				$dl_anterior = $dl;
				$html .= '</table>';
			}
		}
	} else {
		foreach ($a_mega as $tipo => $a_pob_edad) {
			ksort($a_pob_edad);
			$html .= '<h3>';
			$html .= sprintf(_("Cartas de presentación de %s"),$tipo);
			$html .= '</h3>';
			$poblacion_anterior = '';
			$html .= '<table>';
			foreach ($a_pod_edad as $poblacion => $a_edad) {
				$style = "";
				krsort($a_edad); // primero m, después j
				if ($poblacion != $poblacion_anterior) {
					$txt_popblacion = strtoupper($poblacion);
					$txt_popblacion .= empty($pais)? '' :  '<br>('.$pais.')';
					$html .= "<tr><td $class>".strtoupper($poblacion)."</td>";
				}
				$f=0;
				foreach ($a_edad as $edad => $texto) {
					$f++;
					if ($f > 1)	{ $html .= "<tr><td></td>"; }
					if (is_array($texto)) {
						$txt1 = '';
						$ff = 0;
						foreach ($texto as $key=>$txt) {
							$ff++;
							if ($ff > 1)	{ 
								$html .= "<tr><td></td><td $class></td>";
							} else {
								$html .= "<td $class>$edad</td>";
							}
							$html .= "$txt</tr>";
						}
					} else {
						$html .= "<td $class>$edad</td>$texto</tr>";
					}
				}
				$poblacion_anterior = $poblacion;
			}
			$html .= '</table>';
		}
	}
	echo $html;


}

function format_telf ($number) {
  // The regular expression is set to a variable.
    $regex = "/^(\(?\d{3}\)?)?[- .]?(\d{3})[- .]?(\d{3})[- .]?( \(?.*\)?)?$/";
	$a_telf = explode(" / ", $number);
	foreach ($a_telf as $tel) {
 	   $formattedValue[] = preg_replace($regex, "\\1 \\2 \\3\\4", $tel);
	}

	return implode(" / ", $formattedValue);
}
