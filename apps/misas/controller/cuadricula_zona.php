<?php

use core\ViewTwig;
use src\encargossacd\domain\EncargoConstants;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$ZonaSacdRepository = $GLOBALS['container']->get(ZonaSacdRepositoryInterface::class);
$a_Id_nom = $ZonaSacdRepository->getIdSacdsDeZona($Qid_zona);
$a_iniciales = [];

$PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);
$a_sacd = [];
foreach ($a_Id_nom as $id_nom) {
    $PersonaSacd = $PersonaSacdRepository->findById($id_nom);
    $sacd = $PersonaSacd->getNombreApellidos();
    // iniciales
    $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
    $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
    $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
    $iniciales = strtoupper($nom . $ap1 . $ap2);

    $a_iniciales[$id_nom] = $iniciales;

    $key = $id_nom . '#' . $iniciales;

    $a_sacd[$key] = $sacd ?? '?';
}

$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(TRUE);


$oInicio = new DateTimeLocal('2023-12-01');
$oFin = new DateTimeLocal('2023-12-21');
$interval = new DateInterval('P1D');
$date_range = new DatePeriod($oInicio, $interval, $oFin);

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 150, "cssClass" => "cell-title"],
];
$a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
foreach ($date_range as $date) {
    $num_dia = $date->format('Y-m-d');
    //$nom_dia = $date->format('D');
    $dia_week = $date->format('N');
    $dia_mes = $date->format('d');
    $nom_dia = $dia_mes . '-' . $a_dias_semana[$dia_week];

    $columns_cuadricula[] =
        ["id" => "$num_dia", "name" => "$nom_dia", "field" => "$num_dia", "width" => 60, "cssClass" => "cell-title"];
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

        $data_cols["$num_dia"] = "xx " . $d;

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
        $inicio_dia = $num_dia . ' 00:00:00';
        $fin_dia = $num_dia . ' 24:00:00';
        $aWhere = [
            'id_enc' => $id_enc,
            'tstart' => "'$inicio_dia', '$fin_dia'",
        ];
        $aOperador = [
            'tstart' => 'BETWEEN',
        ];
        $EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

        if (count($cEncargosDia) > 1) {
            exit(_("sólo debería haber uno"));
        }

        if (count($cEncargosDia) === 1) {
            $oEncargoDia = $cEncargosDia[0];
            $id_nom = $oEncargoDia->getId_nom();
            $iniciales = $a_iniciales[$id_nom];
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
            $iniciales .= empty($oEncargoDia->getObserv()) ? '' : '*';
            $data_cols["$num_dia"] = $iniciales;
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

$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);