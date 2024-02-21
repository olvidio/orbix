<?php


// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\EncargoConstants;
use misas\domain\repositories\EncargoDiaRepository;
use misas\model\EncargosZona;
use personas\model\entity\PersonaSacd;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZonaSacd;
use personas\model\entity\GestorPersona;
use personas\model\entity\PersonaEx;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\EncargoTipo;
use ubis\model\entity\Ubi;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

function iniciales($id_nom) {
    if ($id_nom>0) {
        $PersonaSacd = new PersonaSacd($id_nom);
        // iniciales
        $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
    } else {
        $PersonaEx = new PersonaEx($id_nom);
        $sacdEx = $PersonaEx->getNombreApellidos();
        // iniciales
        $nom = mb_substr($PersonaEx->getNom(), 0, 1);
        $ap1 = mb_substr($PersonaEx->getApellido1(), 0, 1);
        $ap2 = mb_substr($PersonaEx->getApellido2(), 0, 1);
    }
    $iniciales = strtoupper($nom . $ap1 . $ap2);
    return $iniciales;
}

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QTipoPlantilla = 's';

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "tipo_encargo", "name" => "Tipo de encargo", "field" => "tipo_encargo", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "lugar", "name" => "Lugar", "field" => "lugar", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "descripcion_lugar", "name" => "Descripción lugar", "field" => "descripcion_lugar", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "idioma_enc", "name" => "Idioma", "field" => "idioma_enc", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "observ", "name" => "Observaciones", "field" => "observ", "width" => 150, "cssClass" => "cell-title"],  
];







$data_cuadricula = [];
// encargos de misa (8010) para la zona
$a_tipo_enc = [8010, 8011];

$aWhere = array();
$aOperador = array();
$cond_tipo_enc = "{" . implode(', ', $a_tipo_enc) . "}";
$aWhere['id_tipo_enc'] = $cond_tipo_enc;
$aOperador['id_tipo_enc'] = 'ANY';
$aWhere['id_zona'] = $Qid_zona;

$aWhere['_ordre'] = 'desc_enc';
$GesEncargos = new GestorEncargo();
$cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);

$e = 0;
//foreach ($cEncargosZona as $oEncargo) {
foreach ($cEncargos as $oEncargo) {
        $e++;
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
    $id_ubi = $oEncargo->getId_ubi();
    $id_tipo_enc = $oEncargo->getId_tipo_enc();
    $desc_lugar = $oEncargo->getDesc_lugar();
    $idioma_enc = $oEncargo->getIdioma_enc();
    $observ = $oEncargo->getObserv();

    if (!empty($id_ubi)) {
        $oUbi = Ubi::newUbi($id_ubi);
        $nombre_ubi = $oUbi->getNombre_ubi();
    } else {
        $nombre_ubi = '';
    }

    if (!empty($id_tipo_enc)) {
        $oEncargoTipo = new EncargoTipo($id_tipo_enc);
        $tipo_enc = $oEncargoTipo->getTipo_enc();
    //$nom_tipo=$tipo['nom_tipo'];
    } else {
        $tipo_enc = '';
    }

    $d = 0;
    $data_cols = [];
    $meta_dia='';

    $data_cols["encargo"] = $desc_enc;
    $data_cols["id_enc"] = $id_enc;
    $data_cols["id_tipo_enc"] = $id_tipo_enc;
    $data_cols["tipo_encargo"] = $tipo_enc;
    $data_cols["meta"] = $meta_dia;
    $data_cols["lugar"] = $nombre_ubi;
    $data_cols["idioma_enc"] = $idioma_enc;
    $data_cols["descripcion_lugar"] = $desc_lugar;
    $data_cols["observ"] = $observ;
//    echo $data_cols["encargo"].$data_cols["observ"].'ZZ<br>';
    // añado una columna 'meta' con metadatos, invisible, porque no está
    // en la definición de columns
    $data_cuadricula[] = $data_cols;
}

$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$oHash = new Hash();
$oHash->setCamposForm('color!dia!id_enc!key!observ!tend!tstart!uuid_item');
$array_h = $oHash->getParamAjaxEnArray();

$GesLocales = new usuarios\model\entity\GestorLocal();
$oDesplIdiomas = $GesLocales->getListaIdiomas();
$oDesplIdiomas->setNombre("idioma_enc");
//$oDesplIdiomas->setOpcion_sel($idioma_enc);
$oDesplIdiomas->setOpcion_sel($idioma_enc);
$oDesplIdiomas->setBlanco(1);

$url_desplegable_sacd = 'apps/misas/controller/desplegable_sacd.php';
$oHash_desplegable_sacd = new Hash();
$oHash_desplegable_sacd->setUrl($url_desplegable_sacd);
//$oHash_desplegable_sacd->setCamposForm('id_zona');
//$oHash_desplegable_sacd->setCamposForm('id_zona!id_sacd');
$oHash_desplegable_sacd->setCamposForm('id_zona!id_sacd!seleccion');
//$oHash_desplegable_sacd->setCamposNo('seleccion');
$h_desplegable_sacd = $oHash_desplegable_sacd->linkSinVal();

$a_iniciales = [];
$Qseleccion = 2;

if ($Qseleccion & 2) {
    $gesZonaSacd = new GestorZonaSacd();
    $a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);
    
    foreach ($a_Id_nom as $id_nom) {
        $PersonaSacd = new PersonaSacd($id_nom);
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

$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'oDesplSacd' => $oDesplSacd,
    'url_desplegable_sacd' =>$url_desplegable_sacd,
    'h_desplegable_sacd' => $h_desplegable_sacd,
    'oDesplIdiomas' => $oDesplIdiomas,
    'id_zona' => $Qid_zona,
    'array_h' => $array_h,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_encargos_zona.html.twig', $a_campos);