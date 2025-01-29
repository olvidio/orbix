<?php

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorTipoDeActividad;
use cambios\model\entity\CambioUsuarioObjetoPref;
use cambios\model\entity\CambioUsuarioPropiedadPref;
use cambios\model\entity\GestorCambioUsuarioPropiedadPref;
use cambios\model\GestorAvisoCambios;
use core\ConfigGlobal;
use web\DesplegableArray;
use procesos\model\entity\GestorActividadFase;
use ubis\model\entity\GestorCasaDl;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use web\Hash;
use function core\is_true;

// INICIO Cabecera global de URL de controlador *********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos para esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$miSfsv = ConfigGlobal::mi_sfsv();

$Qsalida = (string)filter_input(INPUT_POST, 'salida');

// buscar las fases para estos procesos
switch ($Qsalida) {
    case 'guardar_cond':
        $Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
        $Qpropiedad = (string)filter_input(INPUT_POST, 'propiedad');
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qoperador = (string)filter_input(INPUT_POST, 'operador');
        $Qvalor = (string)filter_input(INPUT_POST, 'valor');
        $Qvalor_old = (string)filter_input(INPUT_POST, 'valor_old');
        $Qvalor_new = (string)filter_input(INPUT_POST, 'valor_new');


        $id_cond = $Qobjeto . "_" . $Qpropiedad . "_cond";
        if (!empty($Qid_item)) {
            $oCambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref(array('id_item' => $Qid_item));
        } else {
            $oCambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref();
        }

        if (!empty($Qpropiedad)) {
            $oCambioUsuarioPropiedadPref->setPropiedad($Qpropiedad);
            if ($Qpropiedad == 'id_ubi') {
                $Qvalor = implode(",", $_POST['id_ubi']);
            }
        }
        if (!empty($Qoperador)) $oCambioUsuarioPropiedadPref->setOperador($Qoperador);
        if (!empty($Qvalor)) $oCambioUsuarioPropiedadPref->setValor($Qvalor);

        if (!empty($Qvalor_old)) {
            $oCambioUsuarioPropiedadPref->setValor_old($Qvalor_old);
        } else {
            $oCambioUsuarioPropiedadPref->setValor_old('f');
        }
        if (!empty($Qvalor_new)) {
            $oCambioUsuarioPropiedadPref->setValor_new($Qvalor_new);
        } else {
            $oCambioUsuarioPropiedadPref->setValor_new('f');
        }

        // equivalente a serialize. No funciona porque es un objeto PDO. No sé usar sleep...
        $aCambioPropiedad = [
            'iid_item' => $Qid_item,
            //'iid_item_usuario_objeto' => $Qid_item_usuario_objeto,
            'spropiedad' => $Qpropiedad,
            'soperador' => $Qoperador,
            'svalor' => $Qvalor,
            'bvalor_old' => $Qvalor_old,
            'bvalor_new' => $Qvalor_new,
        ];
        $cambio_prop = json_encode($aCambioPropiedad);

        $condicion = $oCambioUsuarioPropiedadPref->getTextCambio();
        $rta = "<input type='hidden' id=$id_cond name=$id_cond value='$cambio_prop' >$condicion";
        echo $rta;
        break;
    case 'condicion':
        $Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
        $Qpropiedad = (string)filter_input(INPUT_POST, 'propiedad');
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

        $a_operadores = array("=" => _("igual"),
            "<" => _("menor"),
            ">" => _("mayor"),
            "regexp" => _("regExp")
        );

        if (!empty($Qid_item)) {
            $oCambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref(array('id_item' => $Qid_item));

            $valor = $oCambioUsuarioPropiedadPref->getValor();
            $operador = $oCambioUsuarioPropiedadPref->getOperador();
            if (empty($operador)) {
                $chk_old = 'checked';
                $chk_new = 'checked';
            } else {
                $chk_old = (is_true($oCambioUsuarioPropiedadPref->getValor_old())) ? 'checked' : '';
                $chk_new = (is_true($oCambioUsuarioPropiedadPref->getValor_new())) ? 'checked' : '';
            }
        } else {
            $valor = '';
            $chk_old = 'checked';
            $chk_new = 'checked';
        }
        $txt2 = '<tr>';
        $txt2 .= '<td>' . _("avisar si el valor") . ':';
        $txt2 .= "<input type=\"checkbox\" $chk_new name=\"valor_new\">" . _("nuevo");
        $txt2 .= "<input type=\"checkbox\" $chk_old name=\"valor_old\">" . _("actual");
        $txt2 .= '</td></tr>';
        $txt2 .= '<tr><td>' . _("es") . ':';
        foreach ($a_operadores as $op => $nom_op) {
            if (empty($operador)) {
                $chk_radio = ($op == '=') ? 'checked' : '';
            } else {
                $chk_radio = ($op == $operador) ? 'checked' : '';
            }
            $txt2 .= "<input type=\"radio\" $chk_radio name=\"operador\" value=\"$op\">$nom_op";
        }
        $txt2 .= '</td></tr>';
        $txt2 .= '<tr><td>' . _("a") . ':';
        $txt3 = '<input type="input" name="valor" value="' . $valor . '">';
        if ($Qpropiedad == 'id_ubi') {
            // miro que rol tengo. Si soy casa, sólo veo la mía
            if ($oMiUsuario->isRolePau(Role::PAU_CDC)) { //casa
                $id_pau = $oMiUsuario->getId_pau();
                $sDonde = str_replace(",", " OR id_ubi=", $id_pau);
                //formulario para casas cuyo calendario de actividades interesa
                $donde = "WHERE status='t' AND (id_ubi=$sDonde)";
            } else {
                if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
                    $donde = "WHERE status='t'";
                } else {
                    if ($miSfsv == 1) {
                        $donde = "WHERE status='t' AND sv='t'";
                    }
                    if ($miSfsv == 2) {
                        $donde = "WHERE status='t' AND sf='t'";
                    }
                }
            }
            $oGesCasas = new GestorCasaDl();
            $oOpciones = $oGesCasas->getPosiblesCasas($donde);

            $oSelects = new DesplegableArray($valor, $oOpciones, 'id_ubi');
            //$oSelects->setOpciones($oOpciones);
            $oSelects->setBlanco('t');
            $oSelects->setAccionConjunto('fnjs_mas_casas(event)');
            $txt3 = "<script>
			fnjs_mas_casas=function(e){
				var code = (e.keyCode ? e.keyCode : e.which);
				if(e==\"x\") {
					var valor=1;
				} else {
					var id_propiedad='#'+e.currentTarget.id;
					var valor=$(id_propiedad).val();
					if(code!=9) {
						e.preventDefault();
						e.stopPropagation();
					}
				}
				if ( code==9 || e.type==\"change\" || e==\"x\") {
					if (valor!=0) {";
            $txt3 .= $oSelects->ListaSelectsJs();
            $txt3 .= "}
				}
			}";
            $txt3 .= $oSelects->ComprobarSelectJs();
            $txt3 .= '</script>';
            $txt3 .= $oSelects->ListaSelects();
            $txt3 .= '<input type="hidden" name="valor" value="' . $valor . '">';
        }
        $txt2 .= $txt3;
        $txt2 .= '  ';
        $txt2 .= _("fecha en formato ISO (YYYY-MM-dd)") . '</td></tr>';

        $txt = "<form id='frm_cond'>";
        $txt .= '<h3>' . sprintf(_("condición para %s de %s"), $Qpropiedad, $Qobjeto) . '</h3>';
        $txt .= "<input type=hidden id='salida_cond' name='salida' value=\"update\" > ";
        $txt .= "<input type=hidden id='objeto_cond' name='objeto' value=\"$Qobjeto\" > ";
        $txt .= "<input type=hidden id='propiedad_cond' name='propiedad' value=\"$Qpropiedad\" > ";
        $txt .= "<input type=hidden name=id_item value=\"$Qid_item\" > ";
        $txt .= "<table style=\"width:490\" >";
        $txt .= $txt2;
        $txt .= '</table>';
        $txt .= '<br><br>';
        $txt .= "<input type='button' value='" . _("guardar") . "' onclick=\"fnjs_guardar_cond('guardar_cond');\" >";
        $txt .= "<input type='button' value='" . _("eliminar") . "' onclick=\"fnjs_guardar_cond('eliminar_cond');\" >";
        $txt .= "<input type='button' value='" . _("cancel") . "' onclick=\"fnjs_cerrar();\" >";

        $oHash = new Hash();
        $oHash->setCamposForm("salida!objeto!propiedad!operador!valor");
        $oHash->setCamposChk("valor_old!valor_new");
        $oHash->setCamposNo("id_ubi!id_ubi_mas!id_ubi_num");
        $aCamposHidden = [
            'id_item' => $Qid_item,
        ];
        $oHash->setArrayCamposHidden($aCamposHidden);

        $txt .= $oHash->getCamposHtml();
        $txt .= "</form> ";

        echo $txt;

        break;
    case 'propiedades':
        $Qid_item_usuario_objeto = (string)filter_input(INPUT_POST, 'id_item_usuario_objeto');
        $Qobjeto = (string)filter_input(INPUT_POST, 'objeto');

        $a_item_sel = array();
        $a_propiedades_sel = array();
        $a_condicion_sel = array();
        $a_cambio_propiedad_sel = [];
        if (!empty($Qid_item_usuario_objeto)) {
            $GesCambiosUsuarioPropiedadPref = new GestorCambioUsuarioPropiedadPref();
            $cListaCampos = $GesCambiosUsuarioPropiedadPref->getCambioUsuarioPropiedadesPrefs(array('id_item_usuario_objeto' => $Qid_item_usuario_objeto));
            $c = 0;
            foreach ($cListaCampos as $oCambioUsuarioPropiedadPref) {
                $c++;
                $a_item_sel[$c] = $oCambioUsuarioPropiedadPref->getId_item();
                $a_propiedades_sel[$c] = $oCambioUsuarioPropiedadPref->getPropiedad();
                $a_condicion_sel[$c] = $oCambioUsuarioPropiedadPref->getTextCambio();

                // equivalente a serialize. No funciona porque es un objeto PDO. No sé usar sleep...
                $aCambioPropiedad = [
                    'iid_item' => $oCambioUsuarioPropiedadPref->getId_item(),
                    //'iid_item_usuario_objeto' => $oCambioUsuarioPropiedadPref->getId_item_usuario_objeto (),
                    'spropiedad' => $oCambioUsuarioPropiedadPref->getPropiedad(),
                    'soperador' => $oCambioUsuarioPropiedadPref->getOperador(),
                    'svalor' => $oCambioUsuarioPropiedadPref->getValor(),
                    'bvalor_old' => $oCambioUsuarioPropiedadPref->getValor_old(),
                    'bvalor_new' => $oCambioUsuarioPropiedadPref->getValor_new(),
                ];

                $a_cambio_propiedad_sel[$c] = json_encode($aCambioPropiedad);
            }
        }
        if (!empty($Qobjeto)) {
            $ObjetoFullPath = GestorAvisoCambios::getFullPathObj($Qobjeto);
            $oObject = new $ObjetoFullPath();
            $cDatosCampos = $oObject->getDatosCampos();
            $html = "<td><table><tr><th>";
            $html .= "[<span class='link_inv' onclick='fnjs_selectAll(\"#propiedades_obj\",\"{$Qobjeto}[]\",\"all\",0);'>" . _("todos") . "</span>]";
            $html .= "  [<span class='link_inv' onclick='fnjs_selectAll(\"#propiedades_obj\",\"{$Qobjeto}[]\",\"none\",0);'>" . _("ninguno") . "</span>]";
            $html .= "  [<span class='link_inv' onclick='fnjs_selectAll(\"#propiedades_obj\",\"{$Qobjeto}[]\",\"toggle\",0);'>" . _("invertir") . "</span>]";
            $html .= "</th><th>" . _("condición") . "</th><th></th></tr>";
            $condicion = _("cualquier cambio");
            $cambio_prop = '';
            $scamposChk = $Qobjeto;
            $scamposForm = '';
            foreach ($cDatosCampos as $oDatosCampo) {
                $nom_prop = $oDatosCampo->getNom_camp();
                // me salto el id_schema.
                if ($nom_prop == 'id_schema') continue;
                // me salto los que pone FALSE en condicion aviso
                $condicion_aviso = $oDatosCampo->getAviso();
                if (!is_true($condicion_aviso)) continue;

                $etiqueta = $oDatosCampo->getEtiqueta();
                if ($key = array_search($nom_prop, $a_propiedades_sel)) {
                    $chk_prop = 'checked';
                    $condicion = empty($a_condicion_sel[$key]) ? _("cualquier cambio") : $a_condicion_sel[$key];
                    $id_item = $a_item_sel[$key];
                    $cambio_prop = $a_cambio_propiedad_sel[$key];
                } else {
                    // para el caso de las casas y los sacd, sólo puede avisar de un cambio suyo.
                    // miro que rol tengo. Si soy casa, sólo veo la mía
                    if ($nom_prop == 'id_ubi' && $oMiUsuario->isRolePau(Role::PAU_CDC)) {
                        $id_pau = $oMiUsuario->getId_pau();
                        $sDonde = str_replace(",", " OR id_ubi=", $id_pau);

                        $chk_prop = 'checked';
                        $id_item = '';
                        $cambio_prop = '';
                        $condicion = _("ja veurem");
                    } else {
                        $chk_prop = '';
                        $id_item = '';
                        $cambio_prop = '';
                        $condicion = _("cualquier cambio");
                    }
                }

                //$id = $Qobjeto.'_'.$nom_prop;
                $id_cond = $Qobjeto . '_' . $nom_prop . '_cond';
                $td_item = $Qobjeto . '_' . $nom_prop . '_item';
                $td_cond = "td_$id_cond";
                $txt_mod = "<span class='link' onclick='fnjs_modificar(\"$Qobjeto\",\"$nom_prop\",\"$id_item\");'>" . _("modificar condición") . "</span>";
                $html .= "<tr><td>";
                $html .= "<input type='hidden' id=$td_item name=$td_item value=\"$id_item\" >";
                $html .= "<input type='checkbox' name=\"{$Qobjeto}[]\" value=$id_cond $chk_prop >$etiqueta</td>";
                $html .= "<td id=$td_cond><input type='hidden' id='$id_cond' name='$id_cond' value='$cambio_prop' >$condicion</td>";
                $html .= "<td>$txt_mod</td></tr>";
                $scamposForm .= $id_cond . '!' . $td_item . '!';
            }
            $html .= '</table></td>';

            $oHash = new Hash();
            $oHash->setCamposForm($scamposForm . "!salida!id_item_usuario_objeto_prop");
            $aCamposHidden = [
                'objeto_prop' => $Qobjeto,
            ];
            $oHash->setArrayCamposHidden($aCamposHidden);
            $oHash->setCamposChk($scamposChk);
            $oHash->setCamposNo('casas!test');

            $html .= "<input type='hidden' id='salida_prop' name='salida' value=\"guardar_propiedades\" >";
            $html .= "<input type='hidden' id='id_item_usuario_objeto_prop' name='id_item_usuario_objeto_prop' value=\"$Qid_item_usuario_objeto\" >";
            $html .= $oHash->getCamposHtml();
        } else {
            $html = '';
        }
        echo $html;
        break;
    case 'av_fases':
        $Qobjeto = (string)filter_input(INPUT_POST, 'objeto');

        if (!empty($Qobjeto)) {
            $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
            $Qid_usuario = (string)filter_input(INPUT_POST, 'id_usuario');
            $Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
            if (is_true($Qdl_propia)) {
                $dl_propia = TRUE;
            } else {
                $dl_propia = FALSE;
            }

            if (ConfigGlobal::is_app_installed('procesos')) {
                /* Ahora no tengo en cuenta el permiso: la idea es ver todas y comprobar
                 * el permiso a la hora de generar el aviso.
                */

                // buscar los procesos posibles para estos tipos de actividad
                $GesTiposActiv = new GestorTipoDeActividad();
                $aTiposDeProcesos = $GesTiposActiv->getTiposDeProcesos($Qid_tipo_activ, $dl_propia);
                $oGesFases = new GestorActividadFase();
                $oDesplFases = $oGesFases->getListaActividadFases($aTiposDeProcesos);
                $oDesplFases->setNombre('id_fase_ref');
                //$oDesplFases->setOpcion_sel($id_fase_ref);

            } else {
                // Sólo los estado de la actividad
                $oActividad = new ActividadAll();
                $a_status = $oActividad->getArrayStatus();
                // Quitar el status 'qualquiera'
                unset($a_status[ActividadAll::STATUS_ALL]);
                $aFasesConPerm = array_flip($a_status);
            }

            echo $oDesplFases->desplegable();
        } else {
            $html = "<span class='alert'>";
            $html .= _("primero debe elegir un objeto sobre el que mirar los cambios");
            $html .= "</span>";
            echo $html;
        }
        break;
    case "eliminar":
        $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!empty($a_sel)) { //vengo de un checkbox
            $Qid_usuario = (integer)strtok($a_sel[0], "#");
            $Qid_item_usuario_objeto = (string)strtok("#");
            // el scroll id es de la página anterior, hay que guardarlo allí
            $oPosicion->addParametro('id_sel', $a_sel, 1);
            $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
            $oPosicion->addParametro('scroll_id', $scroll_id, 1);
        } else {
            $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
            $Qid_item_usuario_objeto = (integer)filter_input(INPUT_POST, 'id_item_usuario_objeto');
        }

        $oCambioUsuarioObjeto = new CambioUsuarioObjetoPref(array('id_item_usuario_objeto' => $Qid_item_usuario_objeto));
        if ($oCambioUsuarioObjeto->DBEliminar() === false) {
            echo _("Hay un error, no se ha eliminado");
            echo "\n" . $oCambioUsuarioObjeto->getErrorTxt();
        }
        break;
    case "guardar_objeto":
        $Qid_item_usuario_objeto = (integer)filter_input(INPUT_POST, 'id_item_usuario_objeto');
        $Qid_usuario = (integer)filter_input(INPUT_POST, 'id_usuario');
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qdl_propia = (string)filter_input(INPUT_POST, 'dl_propia');
        $Qobjeto = (string)filter_input(INPUT_POST, 'objeto');
        $Qaviso_tipo = (string)filter_input(INPUT_POST, 'aviso_tipo');
        $Qid_fase_ref = (integer)filter_input(INPUT_POST, 'id_fase_ref');
        $Qaviso_off = (string)filter_input(INPUT_POST, 'aviso_off');
        $Qaviso_on = (string)filter_input(INPUT_POST, 'aviso_on');
        $Qaviso_outdate = (string)filter_input(INPUT_POST, 'aviso_outdate');
        $Qa_casas = (array)filter_input(INPUT_POST, 'casas', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $a_relleno = array(1 => '.', 2 => '..', 3 => '...', 4 => '....', 5 => '.....');
        if (!empty($Qid_item_usuario_objeto)) {
            $oCambioUsuarioObjeto = new CambioUsuarioObjetoPref(array('id_item_usuario_objeto' => $Qid_item_usuario_objeto));
        } else {
            $oCambioUsuarioObjeto = new CambioUsuarioObjetoPref();
        }
        $oCambioUsuarioObjeto->setId_usuario($Qid_usuario);
        if (is_true($Qdl_propia)) {
            $isfsv = substr($Qid_tipo_activ, 0, 1);
            $dl_org = ConfigGlobal::mi_delef($isfsv);
        } else {
            $dl_org = 'x';
            // Aunque tocaría poner $Qobjeto = 'Actividad'; se pone
            // ActividadDl porque para la dl que anota el cambio es: 'ActividadDl'
        }
        $oCambioUsuarioObjeto->setDl_org($dl_org);
        $len = strlen($Qid_tipo_activ);
        if ($len != 6) {
            $dif = 6 - $len;
            if ($dif > 0) {
                $Qid_tipo_activ .= $a_relleno[$dif];
            } else {
                echo "Alerta roja<br>";
            }
        }
        $oCambioUsuarioObjeto->setId_tipo_activ_txt($Qid_tipo_activ);
        $oCambioUsuarioObjeto->setObjeto($Qobjeto);
        $oCambioUsuarioObjeto->setAviso_tipo($Qaviso_tipo);
        $oCambioUsuarioObjeto->setId_fase_ref($Qid_fase_ref);
        $aviso_off = is_true($Qaviso_off) ? 't' : 'f';
        $oCambioUsuarioObjeto->setAviso_off($aviso_off);
        $aviso_on = is_true($Qaviso_on) ? 't' : 'f';
        $oCambioUsuarioObjeto->setAviso_on($aviso_on);
        $aviso_outdate = is_true($Qaviso_outdate) ? 't' : 'f';
        $oCambioUsuarioObjeto->setAviso_outdate($aviso_outdate);
        // En el caso de filtrar por casas
        if (!empty($Qa_casas)) {
            $txt_casa = implode(",", $Qa_casas);
            $oCambioUsuarioObjeto->setId_pau($txt_casa);
        }
        if ($oCambioUsuarioObjeto->DBGuardar() === false) {
            echo _("Hay un error, no se ha guardado");
            echo "\n" . $oCambioUsuarioObjeto->getErrorTxt();
            exit();
        }
        $id_item_usuario_objeto = $oCambioUsuarioObjeto->getId_item_usuario_objeto();
        echo $id_item_usuario_objeto;
        break;
    case "guardar_propiedades":
        $Qid_item_usuario_objeto = (integer)filter_input(INPUT_POST, 'id_item_usuario_objeto_prop');
        $Qobjeto = (string)filter_input(INPUT_POST, 'objeto_prop');

        $a_propiedades_sel = array();
        // Si es empty, no hay ninguna propiedad seleccionada, hay que borrar todas.
        if (!empty($_POST[$Qobjeto])) {
            foreach ($_POST[$Qobjeto] as $id_cond) {
                $nom_prop_cond = substr(strstr($id_cond, '_'), 1);
                $nom_prop = strstr($nom_prop_cond, '_cond', true);
                $a_propiedades_sel[] = $nom_prop;
                if (!empty($_POST[$id_cond])) {
                    $oCambioPropiedad = new CambioUsuarioPropiedadPref();
                    $aCambioPropiedad = json_decode($_POST[$id_cond], TRUE);

                    $oCambioPropiedad->setId_item($aCambioPropiedad['iid_item']);
                    $oCambioPropiedad->setPropiedad($aCambioPropiedad['spropiedad']);
                    $oCambioPropiedad->setOperador($aCambioPropiedad['soperador']);
                    $oCambioPropiedad->setValor($aCambioPropiedad['svalor']);
                    $oCambioPropiedad->setValor_old($aCambioPropiedad['bvalor_old']);
                    $oCambioPropiedad->setValor_new($aCambioPropiedad['bvalor_new']);

                    $oCambioPropiedad->setId_item_usuario_objeto($Qid_item_usuario_objeto);
                } else {
                    $nom_item = str_replace('_cond', '_item', $id_cond);

                    if (!empty($_POST[$nom_item])) {
                        $oCambioPropiedad = new CambioUsuarioPropiedadPref(array('id_item' => $_POST[$nom_item]));
                    } else {
                        $oCambioPropiedad = new CambioUsuarioPropiedadPref();
                    }
                    $oCambioPropiedad->setId_item_usuario_objeto($Qid_item_usuario_objeto);
                    $oCambioPropiedad->setPropiedad($nom_prop);
                }
                if ($oCambioPropiedad->DBGuardar() === false) {
                    echo _("Hay un error, no se ha guardado");
                    echo "\n" . $oCambioPropiedad->getErrorTxt();
                }
            }
        }
        // Hay que borrar las propiedades/campos que no estan en la lista:
        if (!empty($Qid_item_usuario_objeto)) {
            $GesCambiosUsuarioPropiedadPref = new GestorCambioUsuarioPropiedadPref();
            $cListaPropiedades = $GesCambiosUsuarioPropiedadPref->getCambioUsuarioPropiedadesPrefs(array('id_item_usuario_objeto' => $Qid_item_usuario_objeto));
            $c = 0;
            $a_item_tot = array();
            $a_propiedades_tot = array();
            foreach ($cListaPropiedades as $oCambioUsuarioPropiedadPref) {
                $c++;
                $a_item_tot[$c] = $oCambioUsuarioPropiedadPref->getId_item();
                $a_propiedades_tot[$c] = $oCambioUsuarioPropiedadPref->getPropiedad();
            }
            $a_propiedades_borrar = array_diff($a_propiedades_tot, $a_propiedades_sel);
            //print_r($a_propiedades_borrar);
            foreach ($a_propiedades_borrar as $propiedad_borrar) {
                $key = array_search($propiedad_borrar, $a_propiedades_tot);
                $oCambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref(array('id_item' => $a_item_tot[$key]));
                if ($oCambioUsuarioPropiedadPref->DBEliminar() === false) {
                    echo _("Hay un error, no se ha eliminado");
                    echo "\n" . $oCambioUsuarioPropiedadPref->getErrorTxt();
                }
            }
        }
        break;
}
