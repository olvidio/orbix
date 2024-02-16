<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use actividadcargos\model\entity\GestorActividadCargo;
use misas\domain\repositories\EncargoDiaRepository;
use misas\domain\entity\EncargoDia;
use misas\domain\EncargoDiaId;
use misas\domain\EncargoDiaTend;
use misas\domain\EncargoDiaTstart;
use misas\model\EncargosZona;
use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaSacd;
use personas\model\entity\PersonaEx;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use web\TiposActividades;
use zonassacd\model\entity\GestorZonaSacd;
use core\ValueObject;
use Ramsey\Uuid\Uuid as RamseyUuid;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

function iniciales($id_nom) {
    if ($id_nom>0) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $nom = $PersonaSacd->getNom();
        $ap1 = $PersonaSacd->getApellido1();
        $ap2 = $PersonaSacd->getApellido2();
    } else {
        $PersonaEx = new PersonaEx($id_nom);
        $sacdEx = $PersonaEx->getNombreApellidos();
        $nom = $PersonaEx->getNom();
        $ap1 = $PersonaEx->getApellido1();
        $ap2 = $PersonaEx->getApellido2();
    }

    // iniciales
    $inom='';
    if (!is_null($nom))
        $inom = mb_substr($nom, 0, 1);
    $iap1='';
    if (!is_null($ap1))
        $iap1 = mb_substr($ap1, 0, 1);
    $iap2='';
    if (!is_null($ap2))
        $iap2 = mb_substr($ap2, 0, 1);

    $iniciales = strtoupper($inom . $iap1 . $iap2);
    return $iniciales;
}

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QTipoPlantilla = (string)filter_input(INPUT_POST, 'TipoPlantilla');
$Qseleccion = (string)filter_input(INPUT_POST, 'seleccion');

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qempiezamin_rep=str_replace('/','-',$Qempiezamin);
$Qempiezamax_rep=str_replace('/','-',$Qempiezamax);

echo 'HOLA: '.$Qempiezamin_rep.$Qempiezamax_rep.'<br>';

$a_dias_semana_breve=[1=>'L', 2=>'M', 3=>'X', 4=>'J', 5=>'V', 6=>'S', 7=>'D'];

$a_Clases = [];

$gesZonaSacd = new GestorZonaSacd();
$a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);

foreach ($a_Id_nom as $id_nom) {
    $PersonaSacd = new PersonaSacd($id_nom);
    $sacd = $PersonaSacd->getNombreApellidos();
    $iniciales = iniciales($id_nom);

    $key = $id_nom . '#' . $iniciales;

    $a_sacd[$key] = $sacd ?? '?';
}


$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(TRUE);

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 150, "cssClass" => "cell-title"],
];

//FALTA periode propera setmana i proper mes
//Funciona solament quan es dona data d'inici i final

        $oInicio = new DateTimeLocal($Qempiezamin_rep);
        $oFin = new DateTimeLocal($Qempiezamax_rep.' 23:59:59');
        $interval = new DateInterval('P1D');
        $date_range = new DatePeriod($oInicio, $interval, $oFin);
        $a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
        foreach ($date_range as $date) {
                    $num_dia = $date->format('Y-m-d');
            //$nom_dia = $date->format('D');
            $dia_week = $date->format('N');
            $dia_mes = $date->format('d');
            //$nom_dia = $a_dias_semana[$dia_week];
            $nom_dia=$a_dias_semana_breve[$dia_week].' '.$dia_mes;
echo 'nom_dia: '.$nom_dia.'<br';
            $columns_cuadricula[] =
                ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 80, "cssClass" => "cell-title"];
        }




$data_cuadricula = [];
// encargos de misa (8010) para la zona
$a_tipo_enc = [8010, 8011];
$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();
$e = 0;
foreach ($cEncargosZona as $oEncargo) {
    $e++;
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
    $d = 0;
    $data_cols = [];
    $meta_dia = [];
    foreach ($date_range as $date) {
        $d++;
        $num_dia = $date->format('Y-m-d');
        $nom_dia = $date->format('D');
        echo 'dia: '.$num_dia.$nom_dia.'--'.$QTipoPlantilla.'<br>';
        if($QTipoPlantilla=='s')
        {
            echo 'tipo s OK<br>';
            $dia_week = $date->format('N');
            $dia_plantilla= '2001-01-'.$dia_week;
            echo 'dia '.$dia_plantilla.'<br>';
            $inicio_dia_plantilla = $dia_plantilla.' 00:00:00';
            $fin_dia_plantilla = $dia_plantilla.' 23:59:59';
            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_dia_plantilla', '$fin_dia_plantilla'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];

            $EncargoDiaRepository = new EncargoDiaRepository();
            $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);
    
            if (count($cEncargosDia) > 1) {
                exit(_("sólo debería haber uno"));
            }
    echo 'count: '.count($cEncargosDia).'<br>';
            if (count($cEncargosDia) === 1) {
                $oEncargoDia = $cEncargosDia[0];
                $id_nom = $oEncargoDia->getId_nom();
                echo 'id_nom'.$id_nom.'<br>';
                $hora_ini = $oEncargoDia->getTstart()->format('H:i');
                $hora_fin = $oEncargoDia->getTend()->format('H:i');
                $observ = $oEncargoDia->getObserv();
            }



            //compruebo que no esté fuera
            $aWhereAct = [];
            $aOperadorAct = [];
            $aWhereAct['f_ini'] = "'$num_dia'";
            $aOperadorAct['f_ini'] = '<=';
            $aWhereAct['f_fin'] = "'$num_dia'";
            $aOperadorAct['f_fin'] = '>=';
//            if (!is_true($Qpropuesta)) {
                $aWhereAct['status'] = ActividadAll::STATUS_ACTUAL;
//            } else {
//                $aWhereAct['status'] = ActividadAll::STATUS_BORRABLE;
//                $aOperadorAct['status'] = '!=';
//            }
            /*			
			$aWhere = ['id_nom' => $id_nom, 'plaza' => Asistente::PLAZA_PEDIDA];
			$aOperador = ['plaza' => '>='];
			*/
            $aWhere = ['id_nom' => $id_nom];
            $aOperador = [];

            $oGesActividadCargo = new GestorActividadCargo();
            $cAsistentes = $oGesActividadCargo->getAsistenteCargoDeActividad($aWhere, $aOperador, $aWhereAct, $aOperadorAct);

            foreach ($cAsistentes as $aAsistente) {
                $id_activ = $aAsistente['id_activ'];
echo 'actividad: '.$id_activ;
                $propio = $aAsistente['propio'];
                $plaza = $aAsistente['plaza'];
                $id_cargo = empty($aAsistente['id_cargo']) ? '' : $aAsistente['id_cargo'];

                // Seleccionar sólo las del periodo
                $aWhereAct['id_activ'] = $id_activ;
                $GesActividades = new GestorActividad();
                $cActividades = $GesActividades->getActividades($aWhereAct, $aOperadorAct);
                if (is_array($cActividades) && count($cActividades) == 0) continue;

                $oActividad = $cActividades[0]; // sólo debería haber una.
                $id_tipo_activ = $oActividad->getId_tipo_activ();
                $oF_ini = $oActividad->getF_ini();
                $oF_fin = $oActividad->getF_fin();
                $h_ini = $oActividad->getH_ini();
                $h_fin = $oActividad->getH_fin();
                $dl_org = $oActividad->getDl_org();
                $nom_activ = $oActividad->getNom_activ();
echo ' nom: '.$nom_activ.'<br>';
                $oTipoActividad = new TiposActividades($id_tipo_activ);
                $ssfsv = $oTipoActividad->getSfsvText();

                //para el caso de que la actividad comience antes
                //del periodo de inicio obligo a que tome una hora de inicio
                //en el entorno de las primeras del día (a efectos del planning
                //ya es suficiente con la 1:16 de la madrugada)
/*                if ($oIniPlanning > $oF_ini) {
                    $ini = $inicio_local;
                    $hini = "1:16";
                } else {
                    $ini = (string)$oF_ini->getFromLocal();
                    $hini = (string)$h_ini;
                }
                $fi = (string)$oF_fin->getFromLocal();
                $hfi = (string)$h_fin;
*/

//                if (is_true($Qpropuesta)) {
                    $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                    $nom_llarg = $nom_activ;
echo 'nom llarg: '.$nom_llarg;
            }

            $inicio_nuevo_dia = $num_dia.' 00:00:00';
            $fin_nuevo_dia = $num_dia.' 23:59:59';
            $aWhere = [
                'id_enc' => $id_enc,
                'tstart' => "'$inicio_nuevo_dia', '$fin_nuevo_dia'",
            ];
            $aOperador = [
                'tstart' => 'BETWEEN',
            ];
            $EncargoNuevoDiaRepository = new EncargoDiaRepository();
            $cEncargoNuevoDia = $EncargoNuevoDiaRepository->getEncargoDias($aWhere,$aOperador);
    
            if (count($cEncargoNuevoDia) > 1) {
                exit(_("sólo debería haber máximo uno"));
            }

            if (empty($cEncargoNuevoDia)) {
echo 'empty<br>';
                $oEncargoDia = new EncargoDia();
                $Uuid = new EncargoDiaId(RamseyUuid::uuid4()->toString());
//                $Uuid = new EncargoDiaId(uuid4()->toString);
                $oEncargoDia->setUuid_item($Uuid);
                $oEncargoDia->setId_nom($id_nom);
                $tstart = new EncargoDiaTstart($num_dia, $hora_ini);
                $oEncargoDia->setTstart($tstart);
            
                $tend = new EncargoDiaTend($num_dia, $hora_fin);
                $oEncargoDia->setTend($tend);

                if (isset($observ))
                    $oEncargoDia->setObserv($observ);
                $oEncargoDia->setId_enc($id_enc);
                if ($EncargoDiaRepository->Guardar($oEncargoDia) === FALSE) {
                    $error_txt .= $EncargoDiaRepository->getErrorTxt();
                }
            } else {
                echo 'ple<br>';
                // debería haber solamente uno
                $oEncargoDia = $cEncargoNuevoDia[0];
                $oEncargoDia->setId_nom($id_nom);
                $tstart = new EncargoDiaTstart($num_dia, $hora_ini);
                $oEncargoDia->setTstart($tstart);
                $tend = new EncargoDiaTend($num_dia, $hora_fin);
                $oEncargoDia->setTend($tend);
                $oEncargoDia->setObserv($observ);
                $oEncargoDia->setId_enc($id_enc);
                if ($EncargoDiaRepository->Guardar($oEncargoDia) === FALSE) {
                    $error_txt .= $EncargoDiaRepository->getErrorTxt();
                }
            }
        }

        $data_cols["$num_dia"] = " -- ";

        $meta_dia["$num_dia"] = [
            "uuid_item" => "",
            "color" => "",
            "key" => '',
            "tstart" => '',
            "tend" => '',
            "observ" => '',
            "id_enc" => $id_enc,
        ];

        // sobreescribir los que tengo datos:
        $inicio_dia = $num_dia.' 00:00:00';
        $fin_dia = $num_dia.' 23:59:59';
        $aWhere = [
            'id_enc' => $id_enc,
            'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = new EncargoDiaRepository();
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere,$aOperador);

        if (count($cEncargosDia) > 1) {
            exit(_("sólo debería haber uno"));
        }

        if (count($cEncargosDia) === 1) {
            $oEncargoDia = $cEncargosDia[0];
            $id_nom = $oEncargoDia->getId_nom();
            $hora_ini = $oEncargoDia->getTstart()->format('H:i');
            if ($hora_ini=='00:00')
                $hora_ini='';
            $iniciales = iniciales($id_nom);
            $color = '';

            $meta_dia["$num_dia"] = [
                "uuid_item" => $oEncargoDia->getUuid_item()->value(),
                "color" => $color,
                "key" => "$id_nom#$iniciales",
                "tstart" => $oEncargoDia->getTstart()->getHora(),
                "tend" => $oEncargoDia->getTend()->getHora(),
                "observ" => $oEncargoDia->getObserv(),
                "id_enc" => $id_enc,
            ];
            // añadir '*' si tiene observaciones
            $iniciales .= empty($oEncargoDia->getObserv())? '' : '*';
            $data_cols["$num_dia"] = $iniciales." ".$hora_ini;
        }
    }
    $data_cols["encargo"] = $desc_enc;  
    $data_cols["meta"] = $meta_dia;
    // añado una columna 'meta' con metadatos, invisible, porque no está
    // en la definición de columns
    $data_cuadricula[] = $data_cols;
}

$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$oHash = new Hash();
$oHash->setCamposForm('color!dia!id_enc!key!observ!tend!tstart!uuid_item');
$array_h = $oHash->getParamAjaxEnArray();


$a_campos = ['oPosicion' => $oPosicion,
    'oDesplSacd' => $oDesplSacd,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'array_h' => $array_h,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);
