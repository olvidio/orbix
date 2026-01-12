<?php
/**
 * Página que devuelve distintas paginas para el resumen económico de las casas.
 * Esta página sirve para ejecutar las operaciones de guardar, eliminar, listar...
 * que se piden desde: casa_ec_que.php
 *
 * permisos para modificar el presupuesto:
 *        -todo: casa | jefe calendario
 *        -sf: casa | pr (sf)
 *        -sv: casa | adl (sv)
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        22/12/2010
 *
 */


// INICIO Cabecera global de URL de controlador *********************************

use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\entity\UbiGasto;
use src\casas\domain\value_objects\UbiGastoTipo;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use web\DateTimeLocal;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$Qque = (string)filter_input(INPUT_POST, 'que');

$UbiGastoRepository = $GLOBALS['container']->get(UbiGastoRepositoryInterface::class);
switch ($Qque) {
    case "guardarGasto":
        $Qyear = (integer)filter_input(INPUT_POST, 'year');
        $Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
        for ($m = 1; $m < 13; $m++) {
            $Qg = (string)filter_input(INPUT_POST, "g$m");
            $Qap_sv = (string)filter_input(INPUT_POST, "ap_sv$m");
            $Qap_sf = (string)filter_input(INPUT_POST, "ap_sf$m");

            // cambio las comas por puntos decimales.
            $g = empty($Qg) ? 0 : str_replace(',', '.', $Qg);
            $ap_sv = empty($Qap_sv) ? 0 : str_replace(',', '.', $Qap_sv);
            $ap_sf = empty($Qap_sf) ? 0 : str_replace(',', '.', $Qap_sf);

            // lo pongo el 5 de cada mes.
            $oFecha = new DateTimeLocal("$Qyear/$m/5");
            //gasto
            $newId = $UbiGastoRepository->getNewId();
            $oUbiGasto = new UbiGasto();
            $oUbiGasto->setId_item($newId);
            $oUbiGasto->setF_gasto($oFecha);
            $oUbiGasto->setId_ubi($Qid_ubi);
            $oUbiGasto->setTipoGastoVo(UbiGastoTipo::GASTO);
            $oUbiGasto->setCantidadVo($g);
            if ($UbiGastoRepository->Guardar($oUbiGasto) === false) {
                echo _("Hay un error, no se ha guardado.");
            }
            //aportacion sv
            $newId = $UbiGastoRepository->getNewId();
            $oUbiGasto = new UbiGasto();
            $oUbiGasto->setId_item($newId);
            $oUbiGasto->setF_gasto($oFecha);
            $oUbiGasto->setId_ubi($Qid_ubi);
            $oUbiGasto->setTipoGastoVo(UbiGastoTipo::APORTACION_SV);
            $oUbiGasto->setCantidadVo($ap_sv);
            if ($UbiGastoRepository->Guardar($oUbiGasto) === false) {
                echo _("Hay un error, no se ha guardado.");
            }
            //aportacion sf
            $newId = $UbiGastoRepository->getNewId();
            $oUbiGasto = new UbiGasto();
            $oUbiGasto->setId_item($newId);
            $oUbiGasto->setF_gasto($oFecha);
            $oUbiGasto->setId_ubi($Qid_ubi);
            $oUbiGasto->setTipoGastoVo(UbiGastoTipo::APORTACION_SF);
            $oUbiGasto->setCantidadVo($ap_sf);
            if ($UbiGastoRepository->Guardar($oUbiGasto) === false) {
                echo _("Hay un error, no se ha guardado.");
            }

        }
        break;
    case 'getGastos':
        $Qyear = (integer)filter_input(INPUT_POST, 'year');
        // posible selección múltiple de casas
        $Qaid_cdc = (array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        // una lista de casas (id_ubi).
        $aGrupos = [];
        if (!empty($Qaid_cdc)) {
            foreach ($Qaid_cdc as $id_ubi) {
                if (empty($id_ubi)) continue;
                $aGrupos[$id_ubi] = $GLOBALS['container']->get(CasaDlRepositoryInterface::class)->findById($id_ubi)->getNombreUbiVo()->value();
            }
        } else {
            exit (_("Debe seleccionar una casa."));
        }

        $txt = '';
        $suma = 0;
        $suma_sv = 0;
        $suma_sf = 0;
        $aWhere = [];
        $aOperador = [];
        foreach ($aGrupos as $key => $Titulo) {
            $aWhere['id_ubi'] = $key; // en este caso $key=$id_ubi
            $aWhere['f_gasto'] = "'$Qyear/1/1','$Qyear/12/31'";
            $aOperador['f_gasto'] = 'BETWEEN';
            $aWhere['_ordre'] = 'f_gasto';
            $GesGastos = new GestorUbiGasto();
            $cGastos = $GesGastos->getUbiGastos($aWhere, $aOperador);
            $aGastos = [];
            foreach ($cGastos as $oUbiGasto) {
                $oFecha = $oUbiGasto->getF_gasto();
                $mes = $oFecha->format('n'); //sin el 0 delante.
                $tipo = $oUbiGasto->getTipo();
                $cantidad = $oUbiGasto->getCantidad();
                $aGastos[$mes][$tipo] = $cantidad;
            }
            $txt2 = "<tr><th>" . _('mes') . "</th><th>" . _('gasto') . "</th><th>" . _('aportación sv') . "</th><th>" . _('aportación sf') . "</th></tr>";
            $sCamposForm = '';
            for ($m = 1; $m < 13; $m++) {
                $g = empty($aGastos[$m][3]) ? 0 : $aGastos[$m][3];
                $ap_sv = empty($aGastos[$m][1]) ? 0 : $aGastos[$m][1];
                $ap_sf = empty($aGastos[$m][2]) ? 0 : $aGastos[$m][2];
                $suma += $g;
                $suma_sv += $ap_sv;
                $suma_sf += $ap_sf;
                $txt2 .= "<tr><td>$m</td><td class='derecha'><input type='text' class='derecha' id='g$m' name='g$m' size='8' value='$g' onblur=\"fnjs_comprobar_dinero('#g$m');\"> " . _('€') . "</td>";
                $txt2 .= "<td class='derecha'><input type='text' class='derecha' id='ap_sv$m' name='ap_sv$m' size='8' value='$ap_sv' onblur=\"fnjs_comprobar_dinero('#ap_sv$m');\"> " . _('€') . "</td>";
                $txt2 .= "<td class='derecha'><input type='text' class='derecha' id='ap_sf$m' name='ap_sf$m' size='8' value='$ap_sf' onblur=\"fnjs_comprobar_dinero('#ap_sf$m');\"> " . _('€') . "</td></tr>";
                $sCamposForm .= "!g$m!ap_sv$m!ap_sf$m";
            }

            $oHash = new Hash();
            $oHash->setCamposForm($sCamposForm);
            $oHash->setCamposNo('que');
            $a_camposHidden = array(
                'que' => '',
                'id_ubi' => $key,
                'year' => $Qyear,
            );
            $oHash->setArraycamposHidden($a_camposHidden);

            $txt .= "<form id='frm_gastos'>";
            $txt .= '<h3>' . sprintf(_("gastos y aportaciones %s para %s"), $Qyear, $Titulo) . '</h3>';
            $txt .= $oHash->getCamposHtml();
            $txt .= "<table width=690>";
            $txt .= $txt2;
            $txt .= "<tr class='titulo'><td>" . _("suma") . ":</td><td class='derecha'>$suma " . _('€') . "</td><td class='derecha'>$suma_sv " . _('€') . "</td><td class='derecha'>$suma_sf " . _('€') . "</td></tr>";
            $txt .= '</table>';
            $txt .= '<br><br>';
            $txt .= "<input type='button' value='" . _('guardar') . "' onclick=\"fnjs_guardar('#frm_gastos','guardarGasto');\" >";
            $txt .= "<input type='button' value='" . _('cancel') . "' onclick=\"fnjs_ver();\" >";
            $txt .= "</form> ";
        }
        echo $txt;
        break;
    /*
case 'form':
    switch ($_POST['seccion']) {
        case 'total': // Total
            $oResumenEcTot = new CasaResumenEc(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
            $i_total = $oResumenEcTot->getI_total();
        break;
        case 'sv':
            $oResumenEc = new CasaResumenEcSv(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
            break;
        case 'sf':
            $oResumenEc = new CasaResumenEcSf(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
            break;
    }
    if ($_POST['seccion'] == 'total') {
        $txt2="<tr><td>"._("ingresos totales")."<input type=text size=12 name=i_total value=\"$i_total\"></td>";
    } else {
        $i_no_minimo = $oResumenEc->getI_no_minimo();
        $i_asistentes = $oResumenEc->getI_asistentes();
        $i_deficit = $oResumenEc->getI_deficit();
        $dias = $oResumenEc->getDias();
        $num_total_asistencias = $oResumenEc->getNum_total_asistencias();
        $num_activ = $oResumenEc->getNum_activ();
        $num_total_asistentes = $oResumenEc->getNum_total_asistentes();
        $txt2="<tr><td>"._("ingresos por multa")." <input type=text class='derecha' size=12 id='i_no_minimo' name='i_no_minimo' value=\"$i_no_minimo\" onblur=\"fnjs_comprobar_dinero('#i_no_minimo');\"> "._('€')."</td>";
        $txt2.="<td>"._("ingresos de asistentes")." <input type=text class='derecha' size=12 id='i_asistentes' name='i_asistentes' value=\"$i_asistentes\" onblur=\"fnjs_comprobar_dinero('#i_asistentes');\"> "._('€')."</td></tr>";
        $txt2.="<tr><td>"._("ingresos por dl")." <input type=text class='derecha' size=12 id='i_deficit' name='i_deficit' value=\"$i_deficit\" onblur=\"fnjs_comprobar_dinero('#i_deficit');\"> "._('€')."</td>";
        $txt2.="<td>"._("dias")." <input type=text size=12 name=dias value=\"$dias\"></td></tr>";
        $txt2.="<tr><td>"._("número total de asistencias")." <input type=text size=12 name=num_total_asistencias value=\"$num_total_asistencias\"></td>";
        $txt2.="<td>"._("número total de actividades")." <input type=text size=12 name=num_activ value=\"$num_activ\"></td></tr>";
        $txt2.="<td>"._("número total de asistentes")." <input type=text size=12 name=num_total_asistentes value=\"$num_total_asistentes\"></td></tr>";
    }

    $txt="<form id='frm_ingresos'>";
    $txt.='<h3>'.sprintf(_("presupuesto %s %s"),$_POST['seccion'],$_POST['year']).'</h3>';
    $txt.="<input type=hidden name=que value=\"update\" > ";
    $txt.="<input type=hidden name=seccion value=\"{$_POST['seccion']}\" > ";
    $txt.="<input type=hidden name=id_ubi value=\"{$_POST['id_ubi']}\" > ";
    $txt.="<input type=hidden name=year value=\"{$_POST['year']}\" > ";
    $txt.="<table style=\"width:690\">";
    $txt.=$txt2;
    $txt.='</table>';
    $txt.='<br><br>';
    $txt.="<input type='button' value='". _('guardar') ."' onclick=\"fnjs_guardar('#frm_ingresos','guardar');\" >";
    $txt.="<input type='button' value='". _('eliminar') ."' onclick=\"fnjs_guardar('#frm_ingresos','eliminar');\" >";
    $txt.="<input type='button' value='". _('cancel') ."' onclick=\"fnjs_cerrar();\" >";
    $txt.="</form> ";
    echo $txt;
    break;
case "get":
    // sólo veo 5 años.
    $any_prox = date('Y') + 1;
    for ($i=0; $i<6; $i++) {
        $a_anys[] = $any_prox - $i;
    }
    $any_ini = $any_prox -6;
    $inicioIso = "$any_ini/1/1";
    $finIso = "$any_ini/12/31";

    $oInicio = new DateTimeLocal($inicioIso);
    $oFin = new DateTimeLocal($finIso);

    // copiado de casas_resumen_ajax.php
    // posible selección múltiple de casas
    if (!empty($_POST['id_cdc'])) {
        $aWhere['id_ubi'] = '^'. implode('$|^',$_POST['id_cdc']) .'$';
        $aOperador['id_ubi'] = '~';
    }
    $aWhere['_ordre']='nombre_ubi';
    $GesCasaDl = new GestorCasaDl();
    $cCasasDl = $GesCasaDl->getCasasDl($aWhere,$aOperador);

    $a_resumen = [];
    foreach ($cCasasDl as $oCasaDl) {
        $id_ubi=$oCasaDl->getId_ubi();
        $nombre_ubi=$oCasaDl->getNombre_ubi();
        // Caso especial para Castelldaura: Los gastos son comunes a las dos casas.
        // id_ubi = 1110196 (MAS), 1110198 (Torre).
        $id_ubi_hijo = 1110198;
        $id_ubi_padre = 1110196;

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
        $aWhere=[];
        $aOperador=[];
        $aWhere['f_ini']=$fin;
        $aOperador['f_ini']='<=';
        $aWhere['f_fin']=$inicio;
        $aOperador['f_fin']='>=';
        $aWhere['id_ubi']=$id_ubi;
        $aWhere['status']=4;
        $aOperador['status']='<';
        $aWhere['_ordre']='f_ini';
        $oGesActividades = new GestorActividad();
        $oActividades = $oGesActividades->getActividades($aWhere,$aOperador);
        foreach ($oActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $num_dias_act = $oActividad->getDuracionAumentada(); // no se carga con getTot() porque no es un campo de la base de datos.
            $num_dias = $oActividad->getDuracionEnPeriodo($inicio,$fin); // no se carga con getTot() porque no es un campo de la base de datos.
            $num_dias_real = $oActividad->getDuracionReal(); // no se carga con getTot() porque no es un campo de la base de datos.
            $factor_dias = ($num_dias/$num_dias_real);

            // saber a que periodo pertenece (sf/sv).
            $a_ocupacion= dias_ocupacion($aPeriodos,$oActividad,$inicio,$fin);
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
        if (($var = $a_resumen[$id_ubi][1]['dias']+$a_resumen[$id_ubi][2]['dias']) > 0) {
            $a_resumen[$id_ubi][1]['dias%'] = round($a_resumen[$id_ubi][1]['dias']/$var*100,2).'%';
            $a_resumen[$id_ubi][2]['dias%'] = round($a_resumen[$id_ubi][2]['dias']/$var*100,2).'%';
        } else {
            $a_resumen[$id_ubi][1]['dias%'] = '-';
            $a_resumen[$id_ubi][2]['dias%'] = '-';
        }
        if (($var = $a_resumen[$id_ubi][1]['asist_prev']+$a_resumen[$id_ubi][2]['asist_prev']) > 0) {
            $a_resumen[$id_ubi][1]['asist_prev%'] = round($a_resumen[$id_ubi][1]['asist_prev']/$var*100,2).'%';
            $a_resumen[$id_ubi][2]['asist_prev%'] = round($a_resumen[$id_ubi][2]['asist_prev']/$var*100,2).'%';
        } else {
            $a_resumen[$id_ubi][1]['asist_prev%'] = '-';
            $a_resumen[$id_ubi][2]['asist_prev%'] = '-';
        }
        if (($var = $a_resumen[$id_ubi][1]['asist']+$a_resumen[$id_ubi][2]['asist']) > 0) {
            $a_resumen[$id_ubi][1]['asist%'] = round($a_resumen[$id_ubi][1]['asist']/$var*100,2).'%';
            $a_resumen[$id_ubi][2]['asist%'] = round($a_resumen[$id_ubi][2]['asist']/$var*100,2).'%';
        } else {
            $a_resumen[$id_ubi][1]['asist%'] = '-';
            $a_resumen[$id_ubi][2]['asist%'] = '-';
        }
        if (($var = $a_resumen[$id_ubi][1]['in_prev_acu']+$a_resumen[$id_ubi][2]['in_prev_acu']) > 0) {
            $a_resumen[$id_ubi][1]['in_prev_acu%'] = round($a_resumen[$id_ubi][1]['in_prev_acu']/$var*100,2).'%';
            $a_resumen[$id_ubi][2]['in_prev_acu%'] = round($a_resumen[$id_ubi][2]['in_prev_acu']/$var*100,2).'%';
        } else {
            $a_resumen[$id_ubi][1]['in_prev_acu%'] = '-';
            $a_resumen[$id_ubi][2]['in_prev_acu%'] = '-';
        }
        if (($var = $a_resumen[$id_ubi][1]['in_acu']+$a_resumen[$id_ubi][2]['in_acu']) > 0) {
            $a_resumen[$id_ubi][1]['in_acu%'] = round($a_resumen[$id_ubi][1]['in_acu']/$var*100,2).'%';
            $a_resumen[$id_ubi][2]['in_acu%'] = round($a_resumen[$id_ubi][2]['in_acu']/$var*100,2).'%';
        } else {
            $a_resumen[$id_ubi][1]['in_acu%'] = '-';
            $a_resumen[$id_ubi][2]['in_acu%'] = '-';
        }

        // Gastos ------
        $GesGastos = new GestorUbiGasto();
        // tipo: 1=sv, 2=sf, 3=gastos
        $a_resumen[$id_ubi][1]['aportacion']=$GesGastos->getSumaGastos($id_ubi,1,$inicio,$fin);
        $a_resumen[$id_ubi][2]['aportacion']=$GesGastos->getSumaGastos($id_ubi,2,$inicio,$fin);
        $a_resumen[$id_ubi][0]['gasto']=$GesGastos->getSumaGastos($id_ubi,3,$inicio,$fin);

        $a_repartoGastos = reparto($aPeriodos);
        if (($var = $a_repartoGastos[1]+$a_repartoGastos[2]) > 0) {
            $a_resumen[$id_ubi][1]['gasto%'] = round($a_repartoGastos[1]/$var*100,2).'%';
            $a_resumen[$id_ubi][2]['gasto%'] = round($a_repartoGastos[2]/$var*100,2).'%';
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
        $tot[1]['dias%'] = round($tot[1]['dias']/$var*100,2).'%';
        $tot[2]['dias%'] = round($tot[2]['dias']/$var*100,2).'%';
    } else {
        $tot[1]['dias%'] = '-';
        $tot[2]['dias%'] = '-';
    }
    if (($var = $tot[1]['asist_prev']+$tot[2]['asist_prev']) > 0) {
        $tot[1]['asist_prev%'] = round($tot[1]['asist_prev']/$var*100,2).'%';
        $tot[2]['asist_prev%'] = round($tot[2]['asist_prev']/$var*100,2).'%';
    } else {
        $tot[1]['asist_prev%'] = '-';
        $tot[2]['asist_prev%'] = '-';
    }
    if (($var = $tot[1]['asist']+$tot[2]['asist']) > 0) {
        $tot[1]['asist%'] = round($tot[1]['asist']/$var*100,2).'%';
        $tot[2]['asist%'] = round($tot[2]['asist']/$var*100,2).'%';
    } else {
        $tot[1]['asist%'] = '-';
        $tot[2]['asist%'] = '-';
    }
    if (($var = $tot[1]['in_prev_acu']+$tot[2]['in_prev_acu']) > 0) {
        $tot[1]['in_prev_acu%'] = round($tot[1]['in_prev_acu']/$var*100,2).'%';
        $tot[2]['in_prev_acu%'] = round($tot[2]['in_prev_acu']/$var*100,2).'%';
    } else {
        $tot[1]['in_prev_acu%'] = '-';
        $tot[2]['in_prev_acu%'] = '-';
    }
    if (($var = $tot[1]['in_acu']+$tot[2]['in_acu']) > 0) {
        $tot[1]['in_acu%'] = round($tot[1]['in_acu']/$var*100,2).'%';
        $tot[2]['in_acu%'] = round($tot[2]['in_acu']/$var*100,2).'%';
    } else {
        $tot[1]['in_acu%'] = '-';
        $tot[2]['in_acu%'] = '-';
    }
    if (($var = $tot[1]['gasto']+$tot[2]['gasto']) > 0) {
        $tot[1]['gasto%'] = round($tot[1]['gasto']/$var*100,2).'%';
        $tot[2]['gasto%'] = round($tot[2]['gasto']/$var*100,2).'%';
    } else {
        $tot[1]['gasto%'] = '-';
        $tot[2]['gasto%'] = '-';
    }

    //------------- Mostrar -------------
    ?>
    <table border=1>
    <tr><th></th><th></th><th><?= _('días ocupados') ?></th><th>%</th><th><?= _('nº días x nº asistentes (previsto)') ?></th><th>%</th>
    <th><?= _('nº días x nº asistentes (real)') ?></th><th>%</th>
    <th><?= _('ingresos previstos acumulados') ?></th><th>%</th>
    <th><?= _('ingresos reales acumulados') ?></th><th>%</th>
    <th><?= _('gastos reales') ?></th><th>%</th><th><?= _('aportación real dl') ?></th><th><?= _('superávit') ?></th><tr>
    <?php
    foreach ($a_resumen as $id_ubi => $row) {
        echo "<tr id=detalles_$id_ubi style='display: none;'><td colspan=16><table>";
        echo "<tr><th>"._('actividad')."</th><th>"._('ocupación sv')."</th><th>"._('ocupación sf')."</th><th>"._('ingresos sv')."</th><th>"._('ingresos sf')."</th></tr>";
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
    <tr><td><span id='span_detalles_<?= $id_ubi ?>' class=link onclick=fnjs_detalles(<?= $id_ubi ?>) ><p><?= _('mostrar detalles') ?><p><p style="display: none"><?= _('ocultar detalles') ?><p></span></td><td>dlbf</td>
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
    <tr><td rowspan=3><?= _('totales') ?></td><td>dlb</td>
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
    break;
case "guardar":
    switch ($_POST['seccion']) {
        case 'total': // Total
            $oResumenEcTot = new CasaResumenEc(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
            $oResumenEcTot->DBCarregar(); //perque agafi els valor que ja té.
            if (!empty($_POST['i_total'])) $oResumenEcTot->setI_total($_POST['i_total']);
            if ($oResumenEcTot->DBGuardar() === false) {
                echo _("Hay un error, no se ha guardado.");
            }
            exit;
        break;
        case 'sv':
            $oResumenEc = new CasaResumenEcSv(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
            break;
        case 'sf':
            $oResumenEc = new CasaResumenEcSf(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
            break;
    }
    $oResumenEc->DBCarregar(); //perque agafi els valor que ja té.
    if (!empty($_POST['i_no_minimo'])) $oResumenEc->setI_no_minimo($_POST['i_no_minimo']);
    if (!empty($_POST['i_asistentes'])) $oResumenEc->setI_asistentes($_POST['i_asistentes']);
    if (!empty($_POST['i_deficit'])) $oResumenEc->setI_deficit($_POST['i_deficit']);
    if (!empty($_POST['dias'])) $oResumenEc->setDias($_POST['dias']);
    if (!empty($_POST['num_total_asistencias'])) $oResumenEc->setNum_total_asistencias($_POST['num_total_asistencias']);
    if (!empty($_POST['num_activ'])) $oResumenEc->setNum_activ($_POST['num_activ']);
    if (!empty($_POST['num_total_asistentes'])) $oResumenEc->setNum_total_asistentes($_POST['num_total_asistentes']);
    if ($oResumenEc->DBGuardar() === false) {
        echo _("Hay un error, no se ha guardado.");
    }
    break;
case "eliminar":
    switch ($_POST['seccion']) {
        case 'total': // Total
            $oResumenEc = new CasaResumenEc(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
        break;
        case 'sv':
            $oResumenEc = new CasaResumenEcSv(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
            break;
        case 'sf':
            $oResumenEc = new CasaResumenEcSf(array('id_ubi'=>$_POST['id_ubi'],'year'=>$_POST['year']));
            break;
    }
    $oResumenEc->DBCarregar(); //perque agafi els valor que ja té. (en aquest cas el id_item)
    if ($oResumenEc->DBEliminar() === false) {
        $error_txt=_("no sé cuál he de borar");
        echo "{ que: '".$Qque."', error: '$error_txt' }";
    }
    break;
    */
}
