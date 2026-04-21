<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewPhtml;
use src\encargossacd\domain\EncargoConstants;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\EncargosZona;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use web\Hash;

require_once 'frontend/shared/global_header_front.inc';

$Qid_zona = (int)filter_input(INPUT_POST, 'id_zona');
$QEmpiezaMin = (string)filter_input(INPUT_POST, 'empiezamin');
$QEmpiezaMax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qseleccion = (int)filter_input(INPUT_POST, 'seleccion');

$dmYtoIso = static function (string $dmY): string {
    $partes = explode('/', $dmY);
    if (count($partes) !== 3) {
        return date('Y-m-d');
    }

    return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
};

$isoMin = $dmYtoIso($QEmpiezaMin) . ' 00:00:00';
$isoMax = $dmYtoIso($QEmpiezaMax) . ' 23:59:59';

$a_iniciales = [];
$a_sacd = [];

$PersonaSacdRepository = $GLOBALS['container']->get(PersonaSacdRepositoryInterface::class);

if ($Qseleccion & 2) {
    $ZonaRepository = $GLOBALS['container']->get(ZonaRepositoryInterface::class);
    $a_Id_nom = $ZonaRepository->getIdSacdsDeZona($Qid_zona);

    foreach ($a_Id_nom as $id_nom) {
        $PersonaSacd = $PersonaSacdRepository->findById($id_nom);
        if ($PersonaSacd === null) {
            continue;
        }
        $sacd = $PersonaSacd->getNombreApellidos();
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
        $sacd = $oPersonaSacd->getNombreApellidos();
        $nom = mb_substr($oPersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($oPersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($oPersonaSacd->getApellido2(), 0, 1);
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
        $sacd = $oPersonaSacd->getNombreApellidos();
        $nom = mb_substr($oPersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($oPersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($oPersonaSacd->getApellido2(), 0, 1);
        $iniciales = strtoupper($nom . $ap1 . $ap2);

        $a_iniciales[$id_nom] = $iniciales;

        $key = $id_nom . '#' . $iniciales;

        $a_sacd[$key] = $sacd ?? '?';
    }
}

$columns_cuadricula = [
    ['id' => 'encargo', 'name' => 'Encargo', 'field' => 'encargo', 'width' => 150, 'cssClass' => 'cell-title'],
];

$oInicio = new DateTimeLocal($isoMin);
$oFin = new DateTimeLocal($isoMax);
$interval = new DateInterval('P1D');
$date_range = new DatePeriod($oInicio, $interval, $oFin);
$a_dates = iterator_to_array($date_range);

$a_dias_semana = EncargoConstants::OPCIONES_DIA_SEMANA;
foreach ($a_dates as $date) {
    $num_dia = $date->format('Y-m-d');
    $dia_week = $date->format('N');
    $nom_dia = $a_dias_semana[$dia_week] ?? $date->format('D');

    $columns_cuadricula[] =
        ['id' => $num_dia, 'name' => $nom_dia, 'field' => $num_dia, 'width' => 80, 'cssClass' => 'cell-title'];
}

$data_cuadricula = [];
$a_tipo_enc = [8010, 8011];
$EncargosZona = new EncargosZona($Qid_zona, $oInicio, $oFin);
$EncargosZona->setATipoEnc($a_tipo_enc);
$cEncargosZona = $EncargosZona->getEncargos();

$EncargoDiaRepository = $GLOBALS['container']->get(EncargoDiaRepositoryInterface::class);

foreach ($cEncargosZona as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
    $data_cols = [];
    $meta_dia = [];
    foreach ($a_dates as $date) {
        $num_dia = $date->format('Y-m-d');

        $data_cols[$num_dia] = ' -- ';

        $meta_dia[$num_dia] = [
            'uuid_item' => '',
            'color' => '',
            'key' => '',
            'tstart' => '',
            'tend' => '',
            'observ' => '',
            'id_enc' => $id_enc,
            'dia' => $num_dia,
            'tipo' => '',
            'texto' => '',
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
        $cEncargosDia = $EncargoDiaRepository->getEncargoDias($aWhere, $aOperador);

        if (count($cEncargosDia) > 1) {
            exit(_('sólo debería haber uno'));
        }

        if (count($cEncargosDia) === 1) {
            $oEncargoDia = $cEncargosDia[0];
            $id_nom = $oEncargoDia->getId_nom();
            $hora_ini = $oEncargoDia->getTstart()->format('H:i');
            if ($hora_ini === '00:00') {
                $hora_ini = '';
            }
            $iniciales = $a_iniciales[$id_nom] ?? '';
            $color = '';

            $meta_dia[$num_dia] = [
                'uuid_item' => $oEncargoDia->getUuidItemVo()->value(),
                'color' => $color,
                'key' => "$id_nom#$iniciales",
                'tstart' => $oEncargoDia->getTstart()->getHora(),
                'tend' => $oEncargoDia->getTend()->getHora(),
                'observ' => $oEncargoDia->getObserv(),
                'id_enc' => $id_enc,
                'dia' => $num_dia,
                'tipo' => 'misas',
                'texto' => '',
            ];
            $iniciales_out = $iniciales . (empty($oEncargoDia->getObserv()) ? '' : '*');
            $data_cols[$num_dia] = $iniciales_out . ' ' . $hora_ini;
        }
    }
    $data_cols['encargo'] = $desc_enc;
    $data_cols['meta'] = $meta_dia;
    $data_cuadricula[] = $data_cols;
}

$json_columns_cuadricula = json_encode($columns_cuadricula, JSON_UNESCAPED_UNICODE);

$url_cuadricula_update = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/cuadricula_update';
$oHashUpd = new Hash();
$oHashUpd->setUrl($url_cuadricula_update);
$oHashUpd->setCamposForm('dia!id_enc!key!observ!tend!tstart!uuid_item!tipo_plantilla!id_zona');
$h_cuadricula_update = $oHashUpd->linkSinVal();

$url_desplegable_sacd = rtrim(ConfigGlobal::getWeb(), '/') . '/src/misas/desplegable_sacd';
$oHashDs = new Hash();
$oHashDs->setUrl($url_desplegable_sacd);
$oHashDs->setCamposForm('id_zona!id_sacd!seleccion!dia');
$h_desplegable_sacd = $oHashDs->linkSinVal();

$url_self = 'frontend/misas/controller/ver_misas_zona.php';
$oHashSelf = new Hash();
$oHashSelf->setUrl($url_self);
$oHashSelf->setCamposForm('id_zona!seleccion!empiezamin!empiezamax!fila!columna');
$h_ver = $oHashSelf->linkSinVal();

$a_campos = [
    'columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $data_cuadricula,
    'url_desplegable_sacd' => $url_desplegable_sacd,
    'h_desplegable_sacd' => $h_desplegable_sacd,
    'url_ver_cuadricula_zona' => $url_self,
    'h_ver_cuadricula_zona' => $h_ver,
    'id_zona' => $Qid_zona,
    'tipo_plantilla' => 'p',
    'orden' => 'prioridad',
    'seleccion' => $Qseleccion,
    'periodo' => '',
    'empieza_min' => $QEmpiezaMin,
    'empieza_max' => $QEmpiezaMax,
    'fila' => 0,
    'columna' => 0,
    'h_cuadricula_update' => $h_cuadricula_update,
    'url_cuadricula_update' => $url_cuadricula_update,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('ver_cuadricula_zona.phtml', $a_campos);
