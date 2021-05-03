<?php

use encargossacd\model\GestorPropuestas;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorPropuestaEncargoSacdHorario;
use encargossacd\model\entity\GestorPropuestaEncargosSacd;
use encargossacd\model\entity\PropuestaEncargoSacd;
use encargossacd\model\entity\PropuestaEncargoSacdHorario;
use personas\model\entity\GestorPersonaSacd;
use web\DateTimeLocal;
use web\Hash;
use personas\model\entity\PersonaSacd;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string) \filter_input(INPUT_POST, 'que');
$Qfiltro_ctr = (integer) \filter_input(INPUT_POST, 'filtro_ctr');

$error_txt = '';
switch ($Qque) {
    case "lista_sacd":
        $Qid_sacd = (integer) \filter_input(INPUT_POST, 'id_sacd');
        $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
        $Qid_enc = (integer) \filter_input(INPUT_POST, 'id_enc');
        $Qtipo = (string) \filter_input(INPUT_POST, 'tipo');
        /* lista sacd posibles */
        $GesPersonas = new GestorPersonaSacd();
        $oDesplSacd = $GesPersonas->getListaSacd("AND id_tabla ~ '^(a|n|sss)$'");
        $html = '';
        
        $oDesplTitular = clone $oDesplSacd;
        $oDesplTitular->setNombre("prop_sacd");
        $oDesplTitular->setOpcion_sel($Qid_sacd);
        $oDesplTitular->setAction("fnjs_cmb_sacd('$Qtipo',$Qid_item,$Qid_enc);");
        
        $html .= '<span class="x" onClick=$("#div_sacd").remove(); title='._("cerrar").'>[x]</span>';
        $html .= '<br>';
        $html .= '<br>';
        $html .= $oDesplTitular->desplegable();
        
        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
            $jsondata['html'] = $html;
        }
        //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsondata);
        exit();
        break;
    case "dedicacion_update":
        $Qid_sacd = (integer) \filter_input(INPUT_POST, 'id_sacd');
        $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
        $Qid_enc = (integer) \filter_input(INPUT_POST, 'id_enc');
        $Qdedic_m = (integer) \filter_input(INPUT_POST, 'dedic_m');
        $Qdedic_t = (integer) \filter_input(INPUT_POST, 'dedic_t');
        $Qdedic_v = (integer) \filter_input(INPUT_POST, 'dedic_v');
        
        $oHoy = new DateTimeLocal();
        $f_ini = $oHoy->getFromLocal();
        foreach (['m','t','v'] as $modulo ) {
            if ($modulo == "m") { $dedicacion = $Qdedic_m; }
            if ($modulo == "t") { $dedicacion = $Qdedic_t; }
            if ($modulo == "v") { $dedicacion = $Qdedic_v; }
            
            $gesPropuestaEncargoSacdHorario = new GestorPropuestaEncargoSacdHorario();
            $aWhere= [ 'id_enc' => $Qid_enc, 'id_nom' => $Qid_sacd, 'id_item_tarea_sacd' => $Qid_item, 'dia_ref' => $modulo ];
            $cEncargosSacdHorario = $gesPropuestaEncargoSacdHorario->getEncargoSacdHorarios($aWhere);
            if (is_array($cEncargosSacdHorario) && count($cEncargosSacdHorario) > 0 ) {
                $oEncargoSacdHorario = $cEncargosSacdHorario[0];
            } else { // nuevo
                $oEncargoSacdHorario = new PropuestaEncargoSacdHorario();
                $oEncargoSacdHorario->setId_enc($Qid_enc);
                $oEncargoSacdHorario->setId_nom($Qid_sacd);
                $oEncargoSacdHorario->setF_ini($f_ini);
                $oEncargoSacdHorario->setF_fin(NULL);
                $oEncargoSacdHorario->setDia_ref($modulo);
                $oEncargoSacdHorario->setId_item_tarea_sacd($Qid_item);
            }
            $oEncargoSacdHorario->setDia_inc($dedicacion);
            
            if ($oEncargoSacdHorario->DBGuardar() === false) {
                $error_txt = $oEncargoSacdHorario->getErrorTxt();
            }
        }
        
        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
        }
        //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsondata);
        exit();
        break;
    case "dedicacion":
        $Qid_sacd = (integer) \filter_input(INPUT_POST, 'id_sacd');
        $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
        $Qid_enc = (integer) \filter_input(INPUT_POST, 'id_enc');
        
        $gesPropuestaEncargoSacdHorario = new GestorPropuestaEncargoSacdHorario();
        $aWhere['id_nom'] = $Qid_sacd;
        $aWhere['id_item_tarea_sacd'] = $Qid_item;
        $cEncargosSacdHorario = $gesPropuestaEncargoSacdHorario->getEncargoSacdHorarios($aWhere);
        $html = '';
        $dedic_m = '';
        $dedic_t = '';
        $dedic_v = '';
        foreach ($cEncargosSacdHorario as $oEncargoSacdHorario) {
            $modulo=$oEncargoSacdHorario->getDia_ref();
            switch ($modulo) {
                case 'm':
                    $dedic_m=$oEncargoSacdHorario->getDia_inc();
                    break;
                case 't':
                    $dedic_t=$oEncargoSacdHorario->getDia_inc();
                    break;
                case 'v':
                    $dedic_v=$oEncargoSacdHorario->getDia_inc();
                    break;
            }
        }
        $oEncargo = new Encargo($Qid_enc);
        $desc_enc = $oEncargo->getDesc_enc();
        
        $url_ajax = "apps/encargossacd/controller/propuestas_ajax.php";
        $aCamposHidden = ['que' => 'dedicacion_update',
                        'id_sacd' =>$Qid_sacd,
                        'id_item' =>$Qid_item,
                        'id_enc' =>$Qid_enc,
                    ];
        $oHash = new Hash();
        $oHash->setUrl($url_ajax);
        $oHash->setArrayCamposHidden($aCamposHidden);
        $oHash->setCamposForm('dedic_m!dedic_t!dedic_v');
        
        $html .= '<span class="x" onClick=$("#div_sacd").remove(); title='._("cerrar").'>[x]</span>';
        $html .= '<br>';
        $html .= "<form method='post' id='modulos' action=''>";
        $html .= $oHash->getCamposHtml();
        $html .= "<table style='width: 400px;' class='tono2' ><tr><td colspan=3>$desc_enc</td></tr>";
        $html .= "<td><input type='text' size='1' name='dedic_m' value='$dedic_m'>". _("mañanas") ."</td>";
        $html .= "<td><input type='text' size='1' name='dedic_t' value='$dedic_t'>". _("tarde 1ª hora") ."</td>";
        $html .= "<td><input type='text' size='1' name='dedic_v' value='$dedic_v'>". _("tarde 2ª hora") ."</td></tr>";
        $html .= "<tr><td colspan=3>";
        $html .= "<input type='button' onClick='fnjs_guardar_horario();' value='". _("ok")."'></td></tr>";
        $html .= "</table>";
        $html .= "</form>";
        
        
        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
            $jsondata['html'] = $html;
        }
        //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsondata);
        exit();
        break;
    case "info":
        $Qid_sacd = (integer) \filter_input(INPUT_POST, 'id_sacd');
        
        $gesPropuestaEncargoSacd = new GestorPropuestaEncargosSacd();
        $aWhere['id_nom_new'] = $Qid_sacd;
        $cEncargosSacd = $gesPropuestaEncargoSacd->getEncargosSacd($aWhere);
        $html = '';
        $html .= '<span class="x" onClick=$("#div_sacd").remove(); title='._("cerrar").'>[x]</span>';
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $id_enc = $oEncargoSacd->getId_enc();
            $oEncargo = new Encargo($id_enc);
            
            $desc_enc = $oEncargo->getDesc_enc();
            
            $html .= '<br>';
            $html .= '<br>';
            $html .= $desc_enc;
        }
        
        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
            $jsondata['html'] = $html;
        }
        //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsondata);
        exit();
        break;
    case "cmb_sacd":
        $Qtipo = (string) \filter_input(INPUT_POST, 'tipo');
        $Qid_item = (integer) \filter_input(INPUT_POST, 'id_item');
        $Qid_enc = (integer) \filter_input(INPUT_POST, 'id_enc');
        $Qid_sacd = (integer) \filter_input(INPUT_POST, 'id_sacd');
        
        if ($Qid_item == 1) { // generar una fila nueva
            $id_sacd_old = 0;
            $modo = 0;
            switch ($Qtipo) {
                case 'titular':
                    $modo = 2;
                    break;
                case 'suplente':
                    $modo = 4;
                    break;
                case 'colaborador':
                    $modo = 5;
                    break;
            }
            $oHoy = new DateTimeLocal();
            $f_ini = $oHoy->getFromLocal();
            $oPropuestaEncargoSacd = new PropuestaEncargoSacd();
            $oPropuestaEncargoSacd->setId_enc($Qid_enc);
            $oPropuestaEncargoSacd->setModo($modo);
            $oPropuestaEncargoSacd->setF_ini($f_ini);
        } else {
            $oPropuestaEncargoSacd = new PropuestaEncargoSacd($Qid_item);
            $oPropuestaEncargoSacd->DBCarregar();
            $id_sacd_old = $oPropuestaEncargoSacd->getId_nom();
            $id_sacd_prop = $oPropuestaEncargoSacd->getId_nom_new();
        }
        $oPropuestaEncargoSacd->setId_nom_new($Qid_sacd);
        if ($oPropuestaEncargoSacd->DBGuardar() === FALSE ) {
            $error_txt .= $oPropuestaEncargoSacd->getErrorTxt();
        }
        // cambiar también el horario
        if (!empty($id_sacd_old) || !empty($id_sacd_prop)) {
            $id_sacd_ref = empty($id_sacd_prop)? $id_sacd_old : $id_sacd_prop;
            $gesPropuestaEncargoSacdHorario = new GestorPropuestaEncargoSacdHorario();
            $gesPropuestaEncargoSacdHorario->cambiarSacd($Qid_enc, $id_sacd_ref, $Qid_sacd);
        }
        
        $oPersonaSacd = new PersonaSacd($Qid_sacd);
        $nombre = $oPersonaSacd->getApellidosNombre();
        
        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
            $jsondata['nombre'] = $nombre;
            $jsondata['id_sacd'] = $Qid_sacd;
        }
        //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsondata);
        exit();
        break;
    case "get_lista":
        $gesPropuestas = new GestorPropuestas();
        $rta = $gesPropuestas->getLista($Qfiltro_ctr);
        
        if (!empty($error_txt)) {
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
        } else {
            $jsondata['success'] = TRUE;
            $jsondata['lista'] = $rta;
        }
        //Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsondata);
        exit();
        break;
    case "crear_tabla":
        $gesPropuestas = new GestorPropuestas();
        $gesPropuestas->crearTabla();
        break;
}