<?php
// INICIO Cabecera global de URL de controlador *********************************

use cartaspresentacion\model\entity\GestorCartaPresentacion;
use cartaspresentacion\model\entity\GestorCartaPresentacionDl;
use core\ConfigGlobal;
use ubis\model\entity\Centro;
use ubis\model\entity\GestorCentro;
use ubis\model\entity\GestorDireccionCtr;
use ubis\model\CuadrosLabor;
use ubis\model\entity\DireccionCtr;
use ubis\model\entity\GestorCtrxDireccion;
use function core\strtoupper_dlb;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// para mostrar todos los errores juntos:
$msgError = '';

$Qque = (string)  \filter_input(INPUT_POST, 'que');

$solo_dl = 0;
switch ($Qque) {
	case "lista_dl":
		$solo_dl = 1;
		$mi_dele = ConfigGlobal::mi_delef();
		$GesPresentacion = new GestorCartaPresentacionDl();
	case "lista_todo":
		$ordenar_dl = 1;
		if (empty($solo_dl)) {
		  $GesPresentacion = new GestorCartaPresentacion();
		}
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
			    $id_direccion = $oDireccion->getId_direccion();
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
					$colPresentacion = $GesPresentacion->getCartasPresentacion(array('id_ubi'=>$id_ubi,'id_direccion'=>$id_direccion));
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
				$cPresentacion = $GesPresentacion->getCartasPresentacion(array('id_ubi'=>$id_ubi));
				foreach ($cPresentacion as $oPresentacion) {
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
				$cPresentacion = $GesPresentacion->getCartasPresentacion(array('id_ubi'=>$id_ubi));
				foreach ($cPresentacion as $oPresentacion) {
					$a_mega = array_merge_recursive($a_mega,mega_array($oPresentacion,$oCentro,$ordenar_dl));
				}
			}
			echo lista_cartas($a_mega,$ordenar_dl);
		}
		break;
}

if (!empty($msgError)) {
    echo '<br>';
    echo _("Centros con el campo 'tipo labor' mal puesto:");
    echo '<br>';
    echo $msgError;
}

/* ************************ Funciones ********************************************/

function mega_array($oPresentacion,$oCentro,$ordenar_dl) {
    global $msgError;
    $a_mega = [];
    $id_ubi = $oPresentacion->getId_ubi();
    $id_direccion = $oPresentacion->getId_direccion();
	$pres_nom = $oPresentacion->getPres_nom();
	$pres_telf = $oPresentacion->getPres_telf();
	$pres_mail = $oPresentacion->getPres_mail();
	$zona = $oPresentacion->getZona();
	
	$dl = $oCentro->getDl();
	$region = $oCentro->getRegion();
	$tipo_ctr = $oCentro->getTipo_ctr();
	$tipo_labor = $oCentro->getTipo_labor();
	$id_ctr_padre = $oCentro->getId_ctr_padre();

	$direccion = '';
	$nom_sede = '';
	$a_p = '';
	$c_p = '';
	$poblacion = '';
	$pais = '';
	$telf = '';
	$a_direccion = array();

    $oDireccion = new DireccionCtr($id_direccion);
    $direccion = $oDireccion->getDireccion();
    $poblacion = $oDireccion->getPoblacion();
    $c_p = $oDireccion->getC_p();
    $pais = $oDireccion->getPais();
    $nom_sede = $oDireccion->getNom_sede();

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
			$a_direccion[] = array('direccion' => $oDireccion1->getDireccion(),
							'a_p'       => $oDireccion1->getA_p(),
							'c_p'       => $oDireccion1->getC_p(),
							'poblacion' => $oDireccion1->getPoblacion(),
							'telf'		=> $telf1	);	
		}
	}
	// Similar a ctr_padre: Si hay una segunda dirección del centro que sea la principal.
	$GesCtrxDireccion = new GestorCtrxDireccion();
	$cCtrxDirecciones = $GesCtrxDireccion->getCtrxDirecciones(['id_ubi' => $id_ubi,'principal' => 't']);
	if (count($cCtrxDirecciones) > 0 ){
	    foreach ($cCtrxDirecciones as $oCtrxDireccion) {
	        $id_dir = $oCtrxDireccion->getId_direccion();
	        if ($id_dir != $id_direccion) {
	            $oDireccion2 = new DireccionCtr($id_dir);
                //$telf1 .= 'fax:'.teleco($id_ctr_padre,"fax","*"," / ") ;
                $a_direccion[] = array('direccion' => $oDireccion2->getDireccion(),
                                'a_p'       => $oDireccion2->getA_p(),
                                'c_p'       => $oDireccion2->getC_p(),
                                'poblacion' => $oDireccion2->getPoblacion(),
                                'telf'		=> ''	);	
	        }
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
	$oTipoLabor = new CuadrosLabor();
	$aTiposLabor = $oTipoLabor->getTxtTiposLabor();
	$aTipo = [];
    $edad = '';
	if (!empty($tipo_labor)) {
        if (($tipo_labor & 128) == 128) $aTipo[] = $aTiposLabor[128];  //'agd';
        if (($tipo_labor & 256) == 256) $aTipo[] = $aTiposLabor[256];  //'numerarios';
        if (($tipo_labor & 64) == 64) $aTipo[] = $aTiposLabor[64];  //'s';
        if (($tipo_labor & 32) == 32) $aTipo[] = $aTiposLabor[32];  //'sss+';
        if ($tipo_ctr == 'dl' OR $tipo_ctr == 'cr') $aTipo[] = 'otras r';
        if (($tipo_labor & 2) == 2) { $edad .= $aTiposLabor[2]; }  //'jóvenes'
        if (($tipo_labor & 1) == 1) { $edad .= !empty($edad)? ', ' : ''; $edad .= $aTiposLabor[1]; }  //'mayores';
        if (($tipo_labor & 4) == 4) { $edad .= !empty($edad)? ', ' : ''; $edad .= $aTiposLabor[4]; }  //'universitarios';
        if (($tipo_labor & 8) == 8) { $edad .= !empty($edad)? ', ' : ''; $edad .= $aTiposLabor[8]; }  //'bachilleres';
	} else {
	    $msgError .= empty($msgError)? '' : ', ';
	    $msgError .= $oCentro->getNombre_ubi();
	}
	//zona
	if (!empty($zona)) $edad .= "<br>$zona";

	$poblacion .= empty($pais)? '' :  '<br>('.$pais.')';
	if ($ordenar_dl == 1) {
	    foreach($aTipo as $tipo) {
            $a_mega[$tipo][$dl][$poblacion][$edad]= datos_a_celdas($a_texto);
	    }
	} else {
	    foreach($aTipo as $tipo) {
            $a_mega[$tipo][$poblacion][$edad]= datos_a_celdas($a_texto);
	    }
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
	    foreach ($a_direccion as $aa_direccion) {
            $d1 = $aa_direccion['direccion'];  
            //$a1 = $a_direccion['a_p'];        
            $c1 = $aa_direccion['c_p'];        
            $p1 = $aa_direccion['poblacion'];  
            $tf1 = $aa_direccion['telf'];  
            $tf1 = format_telf ($tf1);

            $col1 .= "<br>---<br>$d1<br>$c1  $p1";	
            $col2 .= "<br>---<br>$tf1";
	    }
	}

	$html = "<td class=\"line-top\">$col1</td><td class=\"line-top\">$col2</td>";
	return $html;
}
function lista_cartas($a_mega,$ordenar_dl){
	//print_r($a_mega);
	// cartas agd
	$html = '';
	$class = "class=\"line-top\"";
    ksort($a_mega);
	if ($ordenar_dl == 1) {
		foreach ($a_mega as $tipo => $a_dl_pob_edad) {
            uksort($a_dl_pob_edad, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
			$html .= '<h3>';
			$html .= sprintf(_("Cartas de presentación de %s"),$tipo);
			$html .= '</h3>';
			$dl_anterior = '';
			foreach ($a_dl_pob_edad as $dl => $a_pob_edad) {
                uksort($a_pob_edad, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
				if ($dl != $dl_anterior) {
					$html .= "<h3>$dl - $tipo</h3>";
				}
				$poblacion_anterior = '';
				$html .= '<table>';
				foreach ($a_pob_edad as $poblacion => $a_edad) {
					krsort($a_edad); // primero m, después j
					if ($poblacion != $poblacion_anterior OR empty($poblacion)) {
						$html .= "<tr><td $class>".strtoupper_dlb($poblacion)."</td>";
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
            uksort($a_pob_edad, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
			$html .= '<h3>';
			$html .= sprintf(_("Cartas de presentación de %s"),$tipo);
			$html .= '</h3>';
			$poblacion_anterior = '';
			$html .= '<table>';
			foreach ($a_pob_edad as $poblacion => $a_edad) {
				krsort($a_edad); // primero m, después j
				if ($poblacion != $poblacion_anterior) {
					$txt_poblacion = strtoupper_dlb($poblacion);
					$txt_poblacion .= empty($pais)? '' :  '<br>('.$pais.')';
					$html .= "<tr><td $class>".strtoupper_dlb($txt_poblacion)."</td>";
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
			$html .= '</table>';
		}
	}
	echo $html;
}

function format_telf ($number) {
    // The regular expression is set to a variable.
    $regex = "/^(\(?\d{3}\)?)?[- .]?(\d{3})[- .]?(\d{3})[- .]?( \(?.*\)?)?$/";
	$a_telf = explode(" / ", $number);
	$formattedValue = [];
	foreach ($a_telf as $tel) {
 	   $formattedValue[] = preg_replace($regex, "\\1 \\2 \\3\\4", $tel);
	}

	return implode(" / ", $formattedValue);
}
