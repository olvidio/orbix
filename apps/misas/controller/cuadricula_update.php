<?php

// INICIO Cabecera global de URL de controlador *********************************

use actividadcargos\model\entity\GestorActividadCargo;
use actividades\model\entity\ActividadAll;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargoHorario;
use encargossacd\model\entity\GestorEncargoSacdHorario;
use Illuminate\Http\JsonResponse;
use misas\domain\EncargoDiaId;
use misas\domain\EncargoDiaTend;
use misas\domain\EncargoDiaTstart;
use misas\domain\entity\EncargoDia;
use misas\domain\entity\InicialesSacd;
use misas\domain\repositories\EncargoDiaRepository;
use web\DateTimeLocal;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Quuid_item = (string)filter_input(INPUT_POST, 'uuid_item');
$Qkey = (string)filter_input(INPUT_POST, 'key');
$Qtstart = (string)filter_input(INPUT_POST, 'tstart');
$Qtend = (string)filter_input(INPUT_POST, 'tend');
$Qobserv = (string)filter_input(INPUT_POST, 'observ');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qdia = (string)filter_input(INPUT_POST, 'dia');
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'tipo_plantilla');
$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$sdia=$Qdia;
$dia = new DateTimeLocal($Qdia);
$dia_iso=$dia->getIso();

$error_txt = '';

if (empty($Quuid_item)) {
    exit("Error: falta el id_item");
}
$Uuid = new EncargoDiaId($Quuid_item);

$EncargoDiaRepository = new EncargoDiaRepository();
$oEncargoDia = $EncargoDiaRepository->findById($Uuid);
if ($oEncargoDia === null) {
    $oEncargoDia = new EncargoDia();
    $oEncargoDia->setUuid_item($Uuid);
    $oEncargoDia->setId_enc($Qid_enc);
}
$id_sacd_anterior=$oEncargoDia->getId_nom();
$estado= $oEncargoDia->getStatus();
$zona= $Qid_zona;
$color_misa='';
if (trim($QTipoPlantilla)==EncargoDia::PLAN_DE_MISAS)
{
    if ($estado==EncargoDia::STATUS_PROPUESTA)
    {
        $color_misa = 'rojoclaro';
    }
    if ($estado==EncargoDia::STATUS_COMUNICADO_SACD)
    {
        $color_misa = 'amarilloclaro';
    }
    if ($estado==EncargoDia::STATUS_COMUNICADO_CTR)
    {
        $color_misa = 'verdeclaro';
    }    
}



$flag_borrado = FALSE;
if (empty($Qkey)) { // no hay ningún sacd
    if ($EncargoDiaRepository->Eliminar($oEncargoDia) === FALSE) {
        $error_txt .= $EncargoDiaRepository->getErrorTxt();
    }
    $flag_borrado = TRUE;
} else {
    $porciones = explode("#", $Qkey);
    $iniciales = $porciones[0];
    $id_nom = $porciones[1];
//    echo 'id nom: '.$id_nom.'<br>';

    $oEncargoDia->setId_nom($id_nom);

    if (empty($Qtstart) || empty($Qtend)) {
        // comprobar si es obligatorio
        $oEncargo = new Encargo($Qid_enc);
        $id_tipo_encargo = $oEncargo->getId_tipo_enc();
        $oTipoEncargo = new EncargoTipo($id_tipo_encargo);
        $modo_horario = $oTipoEncargo->getMod_horario();
        if ($modo_horario === EncargoTipo::HORARIO_POR_HORAS) {
            $oDia = new DateTimeLocal($Qdia);
            $dia_week = $oDia->format('N'); // N: 1 (para lunes) hasta 7 (para domingo)
            $h_ini = '';
            $h_fin = '';
            $aWhere = [
                'dia_ref' => "$dia_week|A",
                'id_enc' => $Qid_enc,
                'f_ini' => $Qdia,
                'f_fin' => 'x',
            ];
            $aOperador = [
                'dia_ref' => '~',
                'f_ini' => '<=',
                'f_fin' => 'IS NULL'
            ];
            $gesEncargoHorario = new GestorEncargoHorario();
            $cEncargoHorarios1 = $gesEncargoHorario->getEncargoHorarios($aWhere, $aOperador);
            // añadir los que tienen f_fin pero en un futuro:
            $aWhere['f_fin'] = $Qdia;
            $aOperador['f_fin'] = '>=';
            $cEncargoHorarios2 = $gesEncargoHorario->getEncargoHorarios($aWhere, $aOperador);
            $cEncargoHorarios = array_merge($cEncargoHorarios1, $cEncargoHorarios2);
            // TODO si hay varios?¿?¿
            if (count($cEncargoHorarios) > 0) {            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
            $EncargoDiaRepository = new EncargoDiaRepository();
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);

            }
            if (empty($Qtstart) && !empty($h_ini)) {
                $Qtstart = $h_ini;
            }
            if (empty($Qtend) && !empty($h_fin)) {
                $Qtend = $h_fin;
            }
        }
        // poner por defecto el del encargo

        //$Qtstart = (new DateTimeLocal(''))->format('H:i');

    }

    $QTstart = new EncargoDiaTstart($Qdia, $Qtstart);
    $oEncargoDia->setTstart($QTstart);

    $QTend = new EncargoDiaTend($Qdia, $Qtend);
    $oEncargoDia->setTend($QTend);

    $oEncargoDia->setObserv($Qobserv);


    if ($EncargoDiaRepository->Guardar($oEncargoDia) === FALSE) {
        $error_txt .= $EncargoDiaRepository->getErrorTxt();
    }

    $aWhere = [];
$aWhere['id_zona'] = $zona;
$aOperador = array();
$GesZonasSacd = new GestorZonaSacd();
$cZonaSacd = $GesZonasSacd->getZonasSacds($aWhere, $aOperador);
foreach ($cZonaSacd as $oZonaSacd) {
    $data_cols = [];
    $id_nom = $oZonaSacd->getId_nom();
    $InicialesSacd = new InicialesSacd();
    $nombre_sacd=$InicialesSacd->nombre_sacd($id_nom);
    $iniciales=$InicialesSacd->iniciales($id_nom);
    $key =  $id_nom;
    $lista_sacd[$key]=$nombre_sacd;
    $esta_en_zona[$key]=array('', $oZonaSacd->getDw1(),$oZonaSacd->getDw2(),$oZonaSacd->getDw3(),$oZonaSacd->getDw4(),$oZonaSacd->getDw5(),$oZonaSacd->getDw6(),$oZonaSacd->getDw7());
}

if ($id_sacd_anterior!='')
{

    $contador_1a_sacd_anterior = 0;    
    $contador_total_sacd_anterior = 0;    
    $esta_sacd_anterior = 1; 
    $donde_esta_sacd_anterior = '';

    $aWhereAct = [];
    $aOperadorAct = [];
    $aWhereAct['f_ini'] = $dia_iso;
    $aOperadorAct['f_ini'] = '<=';
    $aWhereAct['f_fin'] = $dia_iso;
    $aOperadorAct['f_fin'] = '>=';
    $aWhereAct['status'] = ActividadAll::STATUS_ACTUAL;
    $aWhere = ['id_nom' => $id_sacd_anterior];
    $aOperador = [];
 
    $oGesActividadCargo = new GestorActividadCargo();
    $cAsistentes = $oGesActividadCargo->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
    
    foreach ($cAsistentes as $aAsistente) {
        $id_activ = $aAsistente['id_activ'];
        $propio = $aAsistente['propio'];
        $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];
            
// Seleccionar sólo las del periodo
        $aWhereAct['id_activ'] = $id_activ;
        $GesActividades = new GestorActividad();
        $cActividades = $GesActividades->getActividades($aWhereAct, $aOperadorAct);
        if (is_array($cActividades) && count($cActividades) == 0) continue;
            
        $oActividad = $cActividades[0]; // sólo debería haber una.
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dInicioActividad = $oActividad->getF_ini();
        $sInicioActividad = $dInicioActividad->format('Y-m-d');
        $dFinActividad = $oActividad->getF_fin();
        $sFinActividad = $dFinActividad->format('Y-m-d');
        $nom_activ = $oActividad->getNom_activ();
        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
        $nom_llarg = $nom_activ;

//        echo $nom_llarg.'<br>';
//        echo $id_nom.'<br>';
        if (isset($esta_sacd_anterior)) {
            if ($esta_sacd_anterior == 1) {
                $esta_sacd_anterior = 2;  
            }
        }
        $donde_esta_sacd_anterior = $nom_llarg;
    }
            // ++++++++++++++ Añado las ausencias +++++++++++++++
            $aWhereE = [];
            $aOperadorE = [];
            $aWhereE['id_nom'] = $id_nom;
            $aWhereE['f_ini'] = "'$dia_iso'";
            $aOperadorE['f_ini'] = '<=';
            $aWhereE['f_fin'] = "'$dia_iso'";
            $aOperadorE['f_fin'] = '>=';
            $GesAusencias = new GestorEncargoSacdHorario();
            $cAusencias = $GesAusencias->getEncargoSacdHorarios($aWhereE, $aOperadorE);
            foreach ($cAusencias as $oTareaHorarioSacd) {
                $id_enc = $oTareaHorarioSacd->getId_enc();
                $oF_ini = $oTareaHorarioSacd->getF_ini();
                $oF_fin = $oTareaHorarioSacd->getF_fin();

                $oEncargo = new Encargo($id_enc);
                $id_tipo_enc = $oEncargo->getId_tipo_enc();
                $id = (string)$id_tipo_enc;
                if ($id[0] != 7 && $id[0] != 4) { continue; }

                    $ini = (string)$oF_ini->getFromLocal();
                $fi = (string)$oF_fin->getFromLocal();

                $nom_llarg = $oEncargo->getDesc_enc();
                $nom_curt = ($nom_llarg[0] === 'A') ? 'a' : 'x';
                if ($ini != $fi) {
                    $nom_llarg .= " ($ini-$fi)";
                } else {
                    $nom_llarg .= " ($ini)";
                }

//                echo $nom_llarg;
                if (isset($esta_sacd_anterior)) {
                    if ($esta_sacd_anterior == 1) {
                        $esta_sacd_anterior = 2;  
                    }
                }
                $donde_esta_sacd_anterior = $nom_llarg;
            }

    }
}
    $contador_1a_sacd = 0;    
    $contador_total_sacd = 0;    
    $esta_sacd = 1; 
    $donde_esta_sacd = '';


    $aWhereAct = [];
    $aOperadorAct = [];
    $aWhereAct['f_ini'] = $dia_iso;
    $aOperadorAct['f_ini'] = '<=';
    $aWhereAct['f_fin'] = $dia_iso;
    $aOperadorAct['f_fin'] = '>=';
    $aWhereAct['status'] = ActividadAll::STATUS_ACTUAL;
    $aWhere = ['id_nom' => $id_nom];
    $aOperador = [];
            
    $oGesActividadCargo = new GestorActividadCargo();
    $cAsistentes = $oGesActividadCargo->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);
            
    foreach ($cAsistentes as $aAsistente) {
        $id_activ = $aAsistente['id_activ'];
        $propio = $aAsistente['propio'];
        $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];
            
// Seleccionar sólo las del periodo
        $aWhereAct['id_activ'] = $id_activ;
        $GesActividades = new GestorActividad();
        $cActividades = $GesActividades->getActividades($aWhereAct, $aOperadorAct);
        if (is_array($cActividades) && count($cActividades) == 0) continue;
            
        $oActividad = $cActividades[0]; // sólo debería haber una.
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $dInicioActividad = $oActividad->getF_ini();
        $sInicioActividad = $dInicioActividad->format('Y-m-d');
        $dFinActividad = $oActividad->getF_fin();
        $sFinActividad = $dFinActividad->format('Y-m-d');
        $nom_activ = $oActividad->getNom_activ();
        $oTipoActividad = new TiposActividades($id_tipo_activ);
        $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
        $nom_llarg = $nom_activ;

//        echo $nom_llarg.'<br>';
//        echo $id_nom.'<br>';
        if (isset($esta_sacd)) {
            if ($esta_sacd == 1) {
                $esta_sacd = 2;  
            }
        }
        $donde_esta_sacd = $nom_llarg;
    }
            // ++++++++++++++ Añado las ausencias +++++++++++++++
            $aWhereE = [];
            $aOperadorE = [];
            $aWhereE['id_nom'] = $id_nom;
            $aWhereE['f_ini'] = "'$dia_iso'";
            $aOperadorE['f_ini'] = '<=';
            $aWhereE['f_fin'] = "'$dia_iso'";
            $aOperadorE['f_fin'] = '>=';
            $GesAusencias = new GestorEncargoSacdHorario();
            $cAusencias = $GesAusencias->getEncargoSacdHorarios($aWhereE, $aOperadorE);
            foreach ($cAusencias as $oTareaHorarioSacd) {
                $id_enc = $oTareaHorarioSacd->getId_enc();
                $oF_ini = $oTareaHorarioSacd->getF_ini();
                $oF_fin = $oTareaHorarioSacd->getF_fin();

                $oEncargo = new Encargo($id_enc);
                $id_tipo_enc = $oEncargo->getId_tipo_enc();
                $id = (string)$id_tipo_enc;
                if ($id[0] != 7 && $id[0] != 4) { continue; }

                    $ini = (string)$oF_ini->getFromLocal();
                $fi = (string)$oF_fin->getFromLocal();

                $nom_llarg = $oEncargo->getDesc_enc();
                $nom_curt = ($nom_llarg[0] === 'A') ? 'a' : 'x';
                if ($ini != $fi) {
                    $nom_llarg .= " ($ini-$fi)";
                } else {
                    $nom_llarg .= " ($ini)";
                }

//                echo $nom_llarg;
                if (isset($esta_sacd)) {
                    if ($esta_sacd == 1) {
                        $esta_sacd = 2;  
                    }
                }
                $donde_esta_sacd = $nom_llarg;
            }

        $inicio_dia = $Qdia.' 00:00:00';
        $fin_dia = $Qdia.' 23:59:59';
//echo $inicio_dia.'-'.$fin_dia.'<br>';
//echo 'id nom: '.$id_nom.'<br>';
        $texto='';
        $color_fondo='';

        $aWhere = [
            'id_nom' => $id_sacd_anterior,
            'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aWhere['_ordre'] = 'tstart';
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = new EncargoDiaRepository();
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);

        $misas_dia=0;
        $misas_1a_hora=0;
        $misas_dia_zona=0;
        $misas_1a_hora_zona=0;
        foreach($cEncargosDia as $oEncargoDia) {
            $id_enc = $oEncargoDia->getId_enc();
//            echo 'id_enc: '.$id_enc.'<br>';
            $oEncargo = new Encargo(array('id_enc' => $id_enc));
            $id_tipo_enc = $oEncargo->getId_tipo_enc();
            $id_zona_enc = $oEncargo->getId_zona();
//            echo 'tipo: '.$id_tipo_enc.' zona: '.$id_zona_enc.'<br>';
            if (substr($id_tipo_enc,1,1)=='1')
            {
                $misas_dia++;
                $misas_1a_hora++;
                if ($zona==$id_zona_enc){
                    $misas_dia_zona++;
                    $misas_1a_hora_zona++;
                }
            }
            if (substr($id_tipo_enc,1,1)=='2')
            {
                $misas_dia++;
                if ($zona==$id_zona_enc){
                    $misas_dia_zona++;
                }
            }
 //           echo $misas_dia.$misas_1a_hora.'<br>';
        }
 //       echo $misas_dia.$misas_1a_hora.'<br>';
        $num_dia = $Qdia;
        $dws = $dia->format('N');
//echo $num_dia.'-'.$dws.'='.$esta_en_zona[$dws].'<br>';
        $color_fondo='verdeclaro';
        $texto='';
        if ($misas_dia>2){
            $texto='Este día tiene más de dos Misas';
            $color_fondo='rojo';
        }
        if ($misas_dia==2){
            $texto='Este día tiene dos Misas';
            $color_fondo='amarillo';
        }
        if (($misas_dia==0) && ($esta_en_zona)){
            $texto='Este día no tiene ninguna Misa';
            $color_fondo='verde';
        }
        if (($misas_dia==0) && (!$esta_en_zona)){
            $texto='Este día no tiene ninguna Misa';
            $color_fondo='azulclaro';
        }
        if ($misas_1a_hora==2){
            $texto='Tiene dos Misas a primera hora';
            $color_fondo='rojo';
        }


        if ($esta_en_zona){
            $texto_sacd = 'SI';    
        } else {
            if ($misas_1a_hora_zona>0){
                $color_fondo='rojo';
                $texto='No está en la zona y tiene Misa a primera hora';
            }
            $texto_sacd = 'NO';
        }
        if ($esta_sacd<1)
        {
//            echo $id_nom.' está en '.$donde_esta_sacd[$id_nom][$num_dia].$num_dia.'<br>';
            if ($misas_1a_hora_zona>0){
//                echo '1a: '.$misas_1a_hora.'<br>';
                $color_fondo='rojo';
            }
            $texto='Está en '.$donde_esta_sacd[$id_nom][$num_dia];
            $texto_sacd = '--';
        }
        $inicio_dia = $Qdia.' 00:00:00';
        $fin_dia = $Qdia.' 23:59:59';
//echo $inicio_dia.'-'.$fin_dia.'<br>';
//echo 'id nom: '.$id_nom.'<br>';
        $texto='';
        $color_fondo='';

        $aWhere = [
            'id_nom' => $id_nom,
            'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aWhere['_ordre'] = 'tstart';
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = new EncargoDiaRepository();
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);

        $misas_dia=0;
        $misas_1a_hora=0;
        $misas_dia_zona=0;
        $misas_1a_hora_zona=0;
        foreach($cEncargosDia as $oEncargoDia) {
            $id_enc = $oEncargoDia->getId_enc();
//            echo 'id_enc: '.$id_enc.'<br>';
            $oEncargo = new Encargo(array('id_enc' => $id_enc));
            $id_tipo_enc = $oEncargo->getId_tipo_enc();
            $id_zona_enc = $oEncargo->getId_zona();
//            echo 'tipo: '.$id_tipo_enc.' zona: '.$id_zona_enc.'<br>';
            if (substr($id_tipo_enc,1,1)=='1')
            {
                $misas_dia++;
                $misas_1a_hora++;
                if ($zona==$id_zona_enc){
                    $misas_dia_zona++;
                    $misas_1a_hora_zona++;
                }
            }
            if (substr($id_tipo_enc,1,1)=='2')
            {
                $misas_dia++;
                if ($zona==$id_zona_enc){
                    $misas_dia_zona++;
                }
            }
 //           echo $misas_dia.$misas_1a_hora.'<br>';
        }
 //       echo $misas_dia.$misas_1a_hora.'<br>';
//echo $num_dia.'-'.$dws.'='.$esta_en_zona[$dws].'<br>';
        $color_fondo='verdeclaro';
        $texto='';
        if ($misas_dia>2){
            $texto='Este día tiene más de dos Misas';
            $color_fondo='rojo';
        }
        if ($misas_dia==2){
            $texto='Este día tiene dos Misas';
            $color_fondo='amarillo';
        }
        if (($misas_dia==0) && ($esta_en_zona)){
            $texto='Este día no tiene ninguna Misa';
            $color_fondo='verde';
        }
        if (($misas_dia==0) && (!$esta_en_zona)){
            $texto='Este día no tiene ninguna Misa';
            $color_fondo='azulclaro';
        }
        if ($misas_1a_hora==2){
            $texto='Tiene dos Misas a primera hora';
            $color_fondo='rojo';
        }


        if ($esta_en_zona){
//            $data_cols[$num_dia]=$misas_dia*10+$misas_1a_hora;
            $data_cols[$num_dia] = 'SI';    
//            $data_cols[$num_dia] = 'SI';
        } else {
//            $data_cols[$num_dia]=$misas_dia*10+$misas_1a_hora;
            if ($misas_1a_hora_zona>0){
                $color_fondo='rojo';
                $texto='No está en la zona y tiene Misa a primera hora';
            }
            $data_cols[$num_dia] = 'NO';
//            $data_cols[$num_dia] = 'NO';
        }
        if ($esta_sacd<1)
        {
//            echo $id_nom.' está en '.$donde_esta_sacd[$id_nom][$num_dia].$num_dia.'<br>';
            if ($misas_1a_hora_zona>0){
//                echo '1a: '.$misas_1a_hora.'<br>';
                $color_fondo='rojo';
            }
            $texto='Está en '.$donde_esta_sacd[$id_nom][$num_dia];
            $data_cols[$num_dia] = '--';
//            $data_cols[$num_dia] = '--';
        }

        $inicio_dia = $sdia.' 00:00:00';
        $fin_dia = $sdia.' 23:59:59';
//echo $inicio_dia.'-'.$fin_dia.'<br>';
//echo 'id nom: '.$id_nom.'<br>';
        $texto='';
        $color_fondo='';

        $aWhere = [
            'id_nom' => $id_nom,
            'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aWhere['_ordre'] = 'tstart';
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = new EncargoDiaRepository();
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);

        $misas_dia=0;
        $misas_1a_hora=0;
        $misas_dia_zona=0;
        $misas_1a_hora_zona=0;
        foreach($cEncargosDia as $oEncargoDia) {
            $id_enc = $oEncargoDia->getId_enc();
//            echo 'id_enc: '.$id_enc.'<br>';
            $oEncargo = new Encargo(array('id_enc' => $id_enc));
            $id_tipo_enc = $oEncargo->getId_tipo_enc();
            $id_zona_enc = $oEncargo->getId_zona();
//            echo 'tipo: '.$id_tipo_enc.' zona: '.$id_zona_enc.'<br>';
            if (substr($id_tipo_enc,1,1)=='1')
            {
                $misas_dia++;
                $misas_1a_hora++;
                if ($zona==$id_zona_enc){
                    $misas_dia_zona++;
                    $misas_1a_hora_zona++;
                }
            }
            if (substr($id_tipo_enc,1,1)=='2')
            {
                $misas_dia++;
                if ($zona==$id_zona_enc){
                    $misas_dia_zona++;
                }
            }
 //           echo $misas_dia.$misas_1a_hora.'<br>';
        }
 //       echo $misas_dia.$misas_1a_hora.'<br>';
//        $num_dia = $date->format('Y-m-d');
//        $dws = $dia_week_sacd[$num_dia];
//echo $num_dia.'-'.$dws.'='.$esta_en_zona[$dws].'<br>';
        $color_fondo='verdeclaro';
        $texto='';
        if ($misas_dia>2){
            $texto='Este día tiene más de dos Misas';
            $color_fondo='rojo';
        }
        if ($misas_dia==2){
            $texto='Este día tiene dos Misas';
            $color_fondo='amarillo';
        }
        if (($misas_dia==0) && ($esta_en_zona)){
            $texto='Este día no tiene ninguna Misa';
            $color_fondo='verde';
        }
        if (($misas_dia==0) && (!$esta_en_zona)){
            $texto='Este día no tiene ninguna Misa';
            $color_fondo='azulclaro';
        }
        if ($misas_1a_hora==2){
            $texto='Tiene dos Misas a primera hora';
            $color_fondo='rojo';
        }


        if ($esta_en_zona){
//            $data_cols[$num_dia]=$misas_dia*10+$misas_1a_hora;
            $data_cols[$num_dia] = 'SI';    
//            $data_cols[$num_dia] = 'SI';
        } else {
//            $data_cols[$num_dia]=$misas_dia*10+$misas_1a_hora;
            if ($misas_1a_hora_zona>0){
                $color_fondo='rojo';
                $texto='No está en la zona y tiene Misa a primera hora';
            }
            $data_cols[$num_dia] = 'NO';
//            $data_cols[$num_dia] = 'NO';
        }
        if ($esta_sacd<1)
        {
//            echo $id_nom.' está en '.$donde_esta_sacd[$id_nom][$num_dia].$num_dia.'<br>';
            if ($misas_1a_hora_zona>0){
//                echo '1a: '.$misas_1a_hora.'<br>';
                $color_fondo='rojo';
            }
            $texto='Está en '.$donde_esta_sacd[$id_nom][$num_dia];
            $data_cols[$num_dia] = '--';
//            $data_cols[$num_dia] = '--';
        }

if (empty($error_txt)) {
    $jsondata['success'] = true;
    if ($flag_borrado) {
        $a_meta = [
            'uuid-item' => '',
            'key' => '',
            'tstart' => '',
            'tend' => '',
            'observ' => '',
            'color_misa' => '',
            'id_sacd_anterior' => $id_sacd_anterior
        ];
    } else {
        $a_meta = [
            'uuid-item' => $Quuid_item,
            'key' => $Qkey,
            'tstart' => $QTstart->getHora(),
            'tend' => $QTend->getHora(),
            'observ' => $Qobserv,
            'color_misa' => $color_misa,
            'id_sacd_anterior' => $id_sacd_anterior
        ];
    }
    $jsondata['meta'] = $a_meta;
} else {
    $jsondata['success'] = false;
    $jsondata['mensaje'] = 'ERROR: '.$error_txt;
}

(new JsonResponse($jsondata))->send();
exit();
