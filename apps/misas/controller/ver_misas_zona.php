<?php

use core\ViewTwig;
use src\encargossacd\domain\EncargoConstants;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


//$gestorPersonaSacd = new GestorPersonaSacd();

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QEmpiezaMin = (string)filter_input(INPUT_POST, 'empiezamin');
$QEmpiezaMax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qseleccion = (string)filter_input(INPUT_POST, 'seleccion');

$a_iniciales = [];
$PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);

if ($Qseleccion & 2) {
    $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
    $a_Id_nom = $ZonaRepository->getIdSacdsDeZona($Qid_zona);

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
}
if ($Qseleccion & 4) {
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['id_tabla'] = "'n','a'";
    $aOperador['id_tabla'] = 'IN';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';
    $cPersonas = $PersonaSacdRepository->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersonaSacd) {
        $id_nom = $oPersonaSacd->getId_nom();
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
}
if ($Qseleccion & 8) {
    $aWhere = [];
    $aOperador = [];
    $aWhere['sacd'] = 't';
    $aWhere['situacion'] = 'A';
    $aWhere['_ordre'] = 'apellido1,apellido2,nom';

    $cPersonas = $PersonaSacdRepository->getPersonas($aWhere, $aOperador);
    foreach ($cPersonas as $oPersonaSacd) {
        $id_nom = $oPersonaSacd->getId_nom();
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
}


$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(TRUE);

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 150, "cssClass" => "cell-title"],
];

$oInicio = new DateTimeLocal('QEmpiezamin');
$oFin = new DateTimeLocal('QEmpiezamax');
$interval = new DateInterval('P1D');
$date_range = new DatePeriod($oInicio, $interval, $oFin);
$a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
foreach ($date_range as $date) {
    $num_dia = $date->format('Y-m-d');
    $dia_week = $date->format('N');
    $nom_dia = $a_dias_semana[$dia_week];

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

        $inicio_dia = $num_dia . ' 00:00:00';
        $fin_dia = $num_dia . ' 23:59:59';
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
            $hora_ini = $oEncargoDia->getTstart()->format('H:i');
            if ($hora_ini === '00:00')
                $hora_ini = '';
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
            $data_cols["$num_dia"] = $iniciales . " " . $hora_ini;
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
