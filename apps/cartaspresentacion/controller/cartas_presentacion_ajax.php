<?php

use cartaspresentacion\model\entity\CartaPresentacion;
use cartaspresentacion\model\entity\CartaPresentacionDl;
use cartaspresentacion\model\entity\CartaPresentacionEx;
use cartaspresentacion\model\entity\GestorCartaPresentacion;
use cartaspresentacion\model\entity\GestorCartaPresentacionDl;
use core\ConfigGlobal;
use function core\is_true;
use ubis\model\entity\Centro;
use ubis\model\entity\CentroDl;
use ubis\model\entity\DireccionCtr;
use ubis\model\entity\DireccionCtrDl;
use ubis\model\entity\DireccionCtrEx;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEx;
use ubis\model\entity\GestorCtrDlxDireccion;
use ubis\model\entity\GestorCtrExxDireccion;
use ubis\model\entity\GestorDireccionCtr;
use ubis\model\entity\GestorDireccionCtrDl;
use ubis\model\entity\Ubi;
use web\Desplegable;
use web\Hash;
use web\Lista;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


// OJO. con la variable poblacion hay conflictos al ir a la pagina del ubi. Ahora la llamo poblacion_sel

/* desactivado por que no se tiene acceso al fichero de cargos:
function llenar_dtor($oCartaPresentacion,$id_ubi) {
	$GesCargoCl = new GestorCargoCl();
	$cCargosCl = $GesCargoCl->getCargosCl(array('id_ubi'=>$id_ubi,'cargo'=>'d','f_cese'=>'x'),array('f_cese'=>'IS NULL'));
	//solo deberia haber uno
	if (!empty($cCargosCl[0])) {
		$id_nom = $cCargosCl[0]->getId_nom();
		$oPersona = Persona::newPersona($id_nom);
		$pres_nom = $oPersona->getNombreApellidos();
		$GesTelecoPersona = new GestorTelecoPersona();
		$cTelecos = $GesTelecoPersona->getTelecos($aWhere=array('id_nom'=>$id_nom));
		foreach ($cTelecos as $oTeleco) {
			$tipo = $oTeleco->getTipo_teleco();
			$num = $oTeleco->getNum_teleco();
			switch ($tipo) {
				case 'móvil':
					$pres_telf = $num;
					break;
				case 'e-mail':
					$pres_mail = $num;
					break;
			}
		}
		isset($pres_nom) ? $oCartaPresentacion->setPres_nom($pres_nom) : $oCartaPresentacion->setPres_nom('');
		isset($pres_telf) ? $oCartaPresentacion->setPres_telf($pres_telf) : $oCartaPresentacion->setPres_telf('');
		isset($pres_mail) ? $oCartaPresentacion->setPres_mail($pres_mail) : $oCartaPresentacion->setPres_mail('');
		if ($oCartaPresentacion->DBGuardar() === false) {
			echo _("Hay un error, no se ha guardado.");
		}
	} else { // En el caso de los apeaderos
		$oCentro = new Centro($id_ubi);
		$id_ctr_padre = $oCentro->getId_ctr_padre();
		if (!empty($id_ctr_padre)) {
			llenar_dtor($oCartaPresentacion,$id_ctr_padre);
		}
	}
}
*/


$Qque_mod = (string)filter_input(INPUT_POST, 'que_mod');

switch ($Qque_mod) {
    case 'poblaciones':
        $Qfiltro = (string)filter_input(INPUT_POST, 'filtro');
        switch ($Qfiltro) {
            case 'get_H':
                $sCondicion = "WHERE pais ILIKE 'españa'";
                $GesDirecciones = new GestorDireccionCtr();
                $oDesplPoblaciones = $GesDirecciones->getListaPoblaciones($sCondicion);
                break;
            case 'get_r':
                $sCondicion = "WHERE pais NOT ILIKE 'españa'";
                $GesDirecciones = new GestorDireccionCtr();
                $oDesplPoblaciones = $GesDirecciones->getListaPoblaciones($sCondicion);
                break;
            case 'get_dl':
                $oGesCentrosDl = new GestorCentroDl();
                //$cCentrosDl = $oGesCentrosDl->getCentros(['status'=>'t']);
                $cCentrosDl = $oGesCentrosDl->getCentros();
                $aPoblaciones = [];
                foreach ($cCentrosDl as $oCentroDl) {
                    $id_ubi = $oCentroDl->getId_ubi();
                    $oGesCtrxDir = new GestorCtrDlxDireccion();
                    $cCtrxDir = $oGesCtrxDir->getCtrxDirecciones(['id_ubi' => $id_ubi]);
                    foreach ($cCtrxDir as $oCtrxDir) {
                        $id_direccion = $oCtrxDir->getId_direccion();
                        $oDireccion = new DireccionCtrDl($id_direccion);
                        $poblacion = $oDireccion->getPoblacion();
                        if (!in_array($poblacion, $aPoblaciones)) {
                            $aPoblaciones[$poblacion] = $poblacion;
                        }
                    }
                }
                uksort($aPoblaciones, "core\strsinacentocmp"); // compara sin contar los acentos i insensitive.
                $oDesplPoblaciones = new Desplegable();
                $oDesplPoblaciones->setOpciones($aPoblaciones);
                break;
            default:
                $aPoblaciones = [];
                $oDesplPoblaciones = new Desplegable();
                $oDesplPoblaciones->setOpciones($aPoblaciones);
                break;
        }

        $options = $oDesplPoblaciones->options();

        $txt = "<select class=contenido id=\"poblacion_sel\" name=\"poblacion_sel\">";
        $txt .= $options;
        $txt .= "</select></td>";
        echo $txt;
        break;
    case 'form_pres':
        $msg_exit = '';
        $Qid_direccion = (integer)filter_input(INPUT_POST, 'id_direccion');
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

        $oDireccion = new DireccionCtr($Qid_direccion);
        $nom_sede = $oDireccion->getNom_sede();
        // Busco el ctr para saber si es de la dl o ex.
        $oCentro = new Centro($Qid_ubi);
        $nombre_ubi = $oCentro->getNombre_ubi();
        $nombre_ubi .= empty($nom_sede) ? '' : " ($nom_sede)";

        $dl = $oCentro->getDl();
        if ($dl == ConfigGlobal::mi_delef()) {
            $oCartaPresentacion = new CartaPresentacionDl();
        } else {
            $tipo_ctr = $oCentro->getTipo_ctr();
            if ($tipo_ctr == 'cr') {
                $oCartaPresentacion = new CartaPresentacionEx();
            } else {
                $msg_exit = _("No puede modificar datos de otra dl");
            }
        }

        if (empty($msg_exit)) {
            $pres_nom = '';
            $pres_telf = '';
            $pres_mail = '';
            $zona = '';
            $observ = '';

            $oGesCartasPresentacion = new GestorCartaPresentacion();
            $cCartasPresentacion = $oGesCartasPresentacion->getCartasPresentacion(['id_direccion' => $Qid_direccion, 'id_ubi' => $Qid_ubi]);
            if (count($cCartasPresentacion) > 0) {
                // solo deberia haber uno, clave unica id_direccion,id_ubi
                // sobreescribo el anterior objeto.
                $oCartaPresentacion = $cCartasPresentacion[0];
                $oCartaPresentacion->DBCarregar();
                $pres_nom = $oCartaPresentacion->getPres_nom();
                $pres_telf = $oCartaPresentacion->getPres_telf();
                $pres_mail = $oCartaPresentacion->getPres_mail();
                $zona = $oCartaPresentacion->getZona();
                $observ = $oCartaPresentacion->getObserv();
            }

            $oHash = new Hash();
            //$oHash->setUrl($url_ajax);
            $oHash->setArrayCamposHidden(['que_mod' => 'update',
                'id_direccion' => $Qid_direccion,
                'id_ubi' => $Qid_ubi,
            ]);

            $oHash->setcamposForm('pres_nom!pres_telf!pres_mail!zona!observ');
            $oHash->setCamposNo('scroll_id!sel');

            $txt = "<form id='frm_pres'>";
            $txt .= '<h3>' . _("centro") . '  ' . $nombre_ubi . '</h3>';
            $txt .= $oHash->getCamposHtml();
            $txt .= _("nombre") . "   <input type=text size=30 name=pres_nom value=\"$pres_nom\">";
            $txt .= '<br>';
            $txt .= _("teléfono") . "   <input type=text size=12 name=pres_telf value=\"$pres_telf\">";
            $txt .= '<br>';
            $txt .= _("e-mail") . "   <input type=text size=20 name=pres_mail value=\"$pres_mail\">";
            $txt .= '<br>';
            $txt .= _("zona") . "   <input type=text size=20 name=zona value=\"$zona\">";
            $txt .= '<br>';
            $txt .= _("observaciones") . "   <input type=text size=60 name=observ value=\"$observ\">";
            $txt .= '<br><br>';
            $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar_cp('#frm_pres');\" >";
            $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
            $txt .= "</form> ";
        } else {
            $txt = "<form id='frm_pres'>";
            $txt .= $msg_exit;
            $txt .= "<br><br><input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";
            $txt .= "</form> ";
        }
        echo $txt;
        break;
    case "eliminar":
        $Qid_direccion = (integer)filter_input(INPUT_POST, 'id_direccion');
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

        if (!empty($Qid_direccion) && !empty($Qid_ubi)) {
            $a_pkey = array('id_direccion' => $Qid_direccion,
                'id_ubi' => $Qid_ubi);
            $oCartaPresentacion = new CartaPresentacion($a_pkey);
            $oCartaPresentacion->DBCarregar();
            if ($oCartaPresentacion->DBEliminar() === false) {
                echo _("Hay un error, no se ha borrado.");
            }
        }
        break;
    case "update":
        $Qid_direccion = (integer)filter_input(INPUT_POST, 'id_direccion');
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');

        if (!empty($Qid_direccion) && !empty($Qid_ubi)) {
            $oGesCartasPresentacion = new GestorCartaPresentacion();
            $cCartasPresentacion = $oGesCartasPresentacion->getCartasPresentacion(['id_direccion' => $Qid_direccion, 'id_ubi' => $Qid_ubi]);
            if (count($cCartasPresentacion) > 0) {
                // solo deberia haber uno, clave unica id_direccion,id_ubi
                $oCartaPresentacion = $cCartasPresentacion[0];
                $oCartaPresentacion->DBCarregar();
            } else {
                // Busco el ctr para saber si es de la dl o ex.
                $oCentro = new Centro($Qid_ubi);
                $dl = $oCentro->getDl();
                if ($dl == ConfigGlobal::mi_delef()) {
                    $oCartaPresentacion = new CartaPresentacionDl();
                } else {
                    $tipo_ctr = $oCentro->getTipo_ctr();
                    if ($tipo_ctr == 'cr') {
                        $oCartaPresentacion = new CartaPresentacionEx();
                    } else {
                        exit (_("No puede modificar datos de otra dl"));
                    }
                }
                $oCartaPresentacion->setId_direccion($Qid_direccion);
                $oCartaPresentacion->setId_ubi($Qid_ubi);
            }

            $Qpres_nom = (string)filter_input(INPUT_POST, 'pres_nom');
            $Qpres_telf = (string)filter_input(INPUT_POST, 'pres_telf');
            $Qpres_mail = (string)filter_input(INPUT_POST, 'pres_mail');
            $Qzona = (string)filter_input(INPUT_POST, 'zona');
            $Qobserv = (string)filter_input(INPUT_POST, 'observ');

            $oCartaPresentacion->setPres_nom($Qpres_nom);
            $oCartaPresentacion->setPres_telf($Qpres_telf);
            $oCartaPresentacion->setPres_mail($Qpres_mail);
            $oCartaPresentacion->setZona($Qzona);
            $oCartaPresentacion->setObserv($Qobserv);
            if ($oCartaPresentacion->DBGuardar() === false) {
                echo _("Hay un error, no se ha guardado.");
            }
        }
        sanear();
        break;
    case "actualizar":
        // se trata de poner el nombre del director (tf i mail) en el dossier.
        // sólo los de la dl.
        $GesCartasPresentacion = new GestorCartaPresentacionDl();
        $cCartasPresentacion = $GesCartasPresentacion->getCartasPresentacion();
        foreach ($cCartasPresentacion as $oCartaPresentacion) {
            $pres_nom = '';
            $pres_telf = '';
            $pres_mail = '';
            $id_direccion = $oCartaPresentacion->getId_direccion();
            llenar_dtor($oCartaPresentacion, $id_direccion);
        }
        break;
    case "get_dl":
        $oPosicion->setBloque('#ficha2');
        //$oPosicion->addParametro('bloque', 'ficha2');
        $oPosicion->recordar();

        $Qpoblacion_sel = (string)filter_input(INPUT_POST, 'poblacion_sel');
        // listado de centros.
        $oGesCentros = new GestorCentroDl();
        $permiso = 'modificar';

        // si hay Qpoblacion, primero hay que buscar en las direcciones.
        if (!empty($Qpoblacion_sel)) {
            $GesDirecciones = new GestorDireccionCtrDl();
            $cDirecciones = $GesDirecciones->getDirecciones(array('poblacion' => $Qpoblacion_sel), array('poblacion' => 'sin_acentos'));
            $cDirCentros = array();
            $txt_direccion = '';
            $d = 0;
            $cId_ubis = [];
            foreach ($cDirecciones as $oDireccion) {
                $d++;
                $id_direccion = $oDireccion->getId_direccion();
                $txt_direccion = $oDireccion->getDireccionPostal(" - ");
                $nom_sede = $oDireccion->getNom_sede();
                $cId_ubis = $oDireccion->getUbis();
                $cCentros = [];
                foreach ($cId_ubis as $oUbi) {
                    $oCentro = new CentroDl($oUbi->getId_ubi());
                    $cCentros[] = $oCentro;
                }
                $cDirCentros[$d] = ['dir' => $txt_direccion,
                    'colCentros' => $cCentros,
                    'id_direccion' => $id_direccion,
                    'nom_sede' => $nom_sede];
            }
        } else {
            $oGesCentrosDl = new GestorCentroDl();
            $aWhere = array('status' => 't', '_ordre' => 'nombre_ubi');
            $cCentrosDl = $oGesCentrosDl->getCentros($aWhere);
        }
        $c = 0;
        $a_valores = array();
        $orden_nom = [];
        foreach ($cDirCentros as $key => $Cen) {
            $txt_direccion = $Cen['dir'];
            $cCentros = $Cen['colCentros'];
            $id_direccion = $Cen['id_direccion'];
            $nom_sede = $Cen['nom_sede'];
            foreach ($cCentros as $oCentro) {
                $c++;
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi();
                if (!is_true($oCentro->getStatus())) {
                    $nombre_ubi = _("ANULADO") . ' ' . $nombre_ubi;
                }
                $nombre_ubi .= empty($nom_sede) ? '' : " ($nom_sede)";
                $tipo_ctr = $oCentro->getTipo_ctr();
                $tipo_labor = $oCentro->getTipo_labor();
                $tipo_ubi = $oCentro->getTipo_ubi();

                $GesPresentacion = new GestorCartaPresentacionDl();
                $colPresentacion = $GesPresentacion->getCartasPresentacion(array('id_direccion' => $id_direccion, 'id_ubi' => $id_ubi));
                //sólo debería haber una.
                if (empty($colPresentacion[0])) {
                    $activo = FALSE;
                    $pres = _("no");
                } else {
                    $activo = TRUE;
                    $pres = _("si");
                }

                if ($permiso == 'modificar') {
                    $script = '';
                    $ctr_txt = $nombre_ubi;
                    $script = "fnjs_modificar($id_direccion,$id_ubi)";
                    $a_valores[$c][1] = array('script' => $script, 'valor' => 'director');
                    $script2 = "fnjs_ver_ubi($id_ubi)";
                    $a_valores[$c][2] = array('script2' => $script2, 'valor' => $ctr_txt);
                    if ($activo) {
                        $script3 = "fnjs_eliminar_cp($id_direccion,$id_ubi)";
                        $pres .= ", " . _("quitar");
                        $a_valores[$c][3] = array('script3' => $script3, 'valor' => $pres);
                    } else {
                        $a_valores[$c][3] = $pres;
                    }
                    $a_valores[$c][4] = $txt_direccion;
                } else {
                    $a_valores[$c][1] = '';
                    $a_valores[$c][2] = $nombre_ubi;
                    $a_valores[$c][3] = $pres;
                    $a_valores[$c][4] = $txt_direccion;
                }
                //$a_valores[$c][2]="<input type=checkbox size=12 id=$id_ubi name=presentacion $chk onClick=\"fnjs_check($id_ubi)\">";
                //$a_valores[$c][3]=$oPermActiv->cuadros_check('tipo_labor',$tipo_labor);
                $orden_nom[$c] = strtolower($nombre_ubi);
            }
        }
        // ordenar por nombre_ubi
        array_multisort($orden_nom, SORT_LOCALE_STRING, SORT_ASC, $a_valores);

        $a_cabeceras = [];
        $a_cabeceras[] = array('name' => ucfirst(_("nombre")), 'width' => 20, 'formatter' => 'clickFormatter');
        $a_cabeceras[] = array('name' => ucfirst(_("centro")), 'width' => 80, 'formatter' => 'clickFormatter2');
        $a_cabeceras[] = array('name' => ucfirst(_("carta de presentación")), 'width' => 20, 'formatter' => 'clickFormatter3');
        $a_cabeceras[] = array('name' => ucfirst(_("direccion")), 'width' => 100);

        $oLista = new Lista();
        $oLista->setId_tabla('cartas_presentacion_ajax_dl');
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->mostrar_tabla();
        /* DESACTIVADO por no tener acceso a las tablas de cargos
        echo "<br><input type=button name=\"actualizar\" value=\"". _("actualizar director") ."\" onclick=\"fnjs_actualizar_dtor();\">";
        */
        break;
    case "get_r":
        // listado de centros.
        $oGesCentros = new GestorCentroEx();
        $permiso = 'modificar';
        $aWhere = array('tipo_ctr' => 'cr|dl', 'status' => 't', '_ordre' => 'nombre_ubi');
        $aOperador = array('tipo_ctr' => '~');
        $cCentros = $oGesCentros->getCentros($aWhere, $aOperador);
        $c = 0;
        $a_valores = array();
        $orden_nom = [];
        foreach ($cCentros as $oCentro) {
            $c++;
            $id_ubi = $oCentro->getId_ubi();
            $nombre_ubi = $oCentro->getNombre_ubi();
            $tipo_ctr = $oCentro->getTipo_ctr();
            $tipo_labor = $oCentro->getTipo_labor();
            $tipo_ubi = $oCentro->getTipo_ubi();

            $oGesCtrxDir = new GestorCtrExxDireccion();
            $cCtrxDir = $oGesCtrxDir->getCtrxDirecciones(['id_ubi' => $id_ubi]);
            $cDirCentros = [];
            $txt_direccion = '';
            foreach ($cCtrxDir as $oCtrxDir) {
                $id_direccion = $oCtrxDir->getId_direccion();
                $oDireccion = new DireccionCtrEx($id_direccion);
                $txt_direccion = $oDireccion->getDireccionPostal(" - ");
                $nom_sede = $oDireccion->getNom_sede();
                $nombre_ubi .= empty($nom_sede) ? '' : " ($nom_sede)";

                $GesPresentacion = new GestorCartaPresentacion();
                $colPresentacion = $GesPresentacion->getCartasPresentacion(array('id_direccion' => $id_direccion, 'id_ubi' => $id_ubi));
                //sólo debería haber una.
                if (empty($colPresentacion[0])) {
                    $activo = FALSE;
                    $pres = _("no");
                } else {
                    $activo = TRUE;
                    $pres = _("si");
                }

                if ($permiso == 'modificar') {
                    $script = '';
                    $ctr_txt = $nombre_ubi;
                    $script = "fnjs_modificar($id_direccion,$id_ubi)";
                    $a_valores[$c][1] = array('script' => $script, 'valor' => 'director');
                    $script2 = "fnjs_ver_ubi($id_ubi)";
                    $a_valores[$c][2] = array('script2' => $script2, 'valor' => $ctr_txt);
                    if ($activo) {
                        $script3 = "fnjs_eliminar_cp($id_direccion,$id_ubi)";
                        $pres .= ", " . _("quitar");
                        $a_valores[$c][3] = array('script3' => $script3, 'valor' => $pres);
                    } else {
                        $a_valores[$c][3] = $pres;
                    }
                    $a_valores[$c][4] = $txt_direccion;
                } else {
                    $a_valores[$c][1] = '';
                    $a_valores[$c][2] = $nombre_ubi;
                    $a_valores[$c][3] = $pres;
                    $a_valores[$c][4] = $txt_direccion;
                }
                //$a_valores[$c][2]="<input type=checkbox size=12 id=$id_ubi name=presentacion $chk onClick=\"fnjs_check($id_ubi)\">";
                //$a_valores[$c][3]=$oPermActiv->cuadros_check('tipo_labor',$tipo_labor);
                $orden_nom[$c] = strtolower($nombre_ubi);
            }
        }
        // ordenar por nombre_ubi
        array_multisort($orden_nom, SORT_LOCALE_STRING, SORT_ASC, $a_valores);

        $a_cabeceras = [];
        $a_cabeceras[] = array('name' => ucfirst(_("nombre")), 'width' => 20, 'formatter' => 'clickFormatter');
        $a_cabeceras[] = array('name' => ucfirst(_("centro")), 'width' => 80, 'formatter' => 'clickFormatter2');
        $a_cabeceras[] = array('name' => ucfirst(_("carta de presentación")), 'width' => 20, 'formatter' => 'clickFormatter3');
        $a_cabeceras[] = array('name' => ucfirst(_("direccion")), 'width' => 100);

        $oLista = new Lista();
        $oLista->setId_tabla('cartas_presentacion_ajax_dl');
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->mostrar_tabla();
        /* DESACTIVADO por no tener acceso a las tablas de cargos
        echo "<br><input type=button name=\"actualizar\" value=\"". _("actualizar director") ."\" onclick=\"fnjs_actualizar_dtor();\">";
        */
        break;
    default:
        $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
        exit ($err_switch);
}

/**
 * Al hacer algunos cambios, puede quedar que una carta de presentacion tenga una dirección que
 * ya no pertenece al centro, y entonces aparece en sitios raros y no se puede localizar.
 * Esta función comprueba que las direcciones pertenezcan a los centros.
 */
function sanear()
{
    $GesCartasPresentacion = new GestorCartaPresentacionDl();
    $cCartasPresentacion = $GesCartasPresentacion->getCartasPresentacion();
    foreach ($cCartasPresentacion as $oCartaPresentacion) {
        $id_ubi = $oCartaPresentacion->getId_ubi();
        $id_direccion = $oCartaPresentacion->getId_direccion();

        $oUbi = Ubi::NewUbi($id_ubi);
        $a_direcciones_ctr = [];
        $cDirecciones = $oUbi->getDirecciones();
        foreach ($cDirecciones as $oDireccion) {
            $a_direcciones_ctr[] = $oDireccion->getId_direccion();
        }
        if (!in_array($id_direccion, $a_direcciones_ctr)) {
            $oCartaPresentacion->DBEliminar();
        }
    }
}