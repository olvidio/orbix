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
use encargossacd\model\entity\GestorEncargoTipo;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use ubis\model\entity\Ubi;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$QTipoPlantilla = 's';

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "tipo_encargo", "name" => "Tipo de encargo", "field" => "tipo_encargo", "width" => 150, "cssClass" => "cell-title"],
//    ["id" => "id_tipo_enc", "name" => "id Tipo de encargo", "field" => "id_tipo_enc", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "lugar", "name" => "Lugar", "field" => "lugar", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "descripcion_lugar", "name" => "Descripción lugar", "field" => "descripcion_lugar", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "nom_idioma", "name" => "Idioma", "field" => "nom_idioma", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "observ", "name" => "Observaciones", "field" => "observ", "width" => 150, "cssClass" => "cell-title"],  
];

$data_cuadricula = [];
// encargos de misa (8010) para la zona
/*$grupo='8...';
    $aWhere = [];
    $aOperador = [];
    $aWhere['id_tipo_enc'] = '^' . $grupo;
    $aOperador['id_tipo_enc'] = '~';
    $oGesEncargoTipo = new GestorEncargoTipo();
    $cEncargoTipos = $oGesEncargoTipo->getEncargoTipos($aWhere, $aOperador);

    // desplegable de nom_tipo
    $posibles_encargo_tipo = [];
    foreach ($cEncargoTipos as $oEncargoTipo) {
*/


$a_tipo_enc = [8010, 8011];

$aWhere = array();
$aOperador = array();
$cond_tipo_enc = "{" . implode(', ', $a_tipo_enc) . "}";
$aWhere['id_tipo_enc'] = $cond_tipo_enc;
$aOperador['id_tipo_enc'] = 'ANY';
$aWhere['id_zona'] = $Qid_zona;

//$aWhere['_ordre'] = 'desc_enc';
$aWhere['_ordre'] = 'observ';
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

    $nom_idioma = '';
    $GesLocales = new usuarios\model\entity\GestorLocal();
    $cIdiomas = $GesLocales->getLocales(['idioma' => $idioma_enc]);
    if (is_array($cIdiomas) && count($cIdiomas) > 0) {
        $nom_idioma = $cIdiomas[0]->getNom_idioma();
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
    $data_cols["nom_idioma"] = $nom_idioma;
    $data_cols["descripcion_lugar"] = $desc_lugar;
    $data_cols["observ"] = $observ;
//    echo $data_cols["encargo"].$data_cols["observ"].'ZZ<br>';
    // añado una columna 'meta' con metadatos, invisible, porque no está
    // en la definición de columns
    $data_cuadricula[] = $data_cols;
}

$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$url_update_encargos_zona = 'apps/misas/controller/update_encargos_zona.php';
$oHashEncargosZona = new Hash();
$oHashEncargosZona->setUrl($url_update_encargos_zona);
$oHashEncargosZona->setCamposForm('id_enc!que!id_tipo_enc!id_ubi!id_zona!descripcion_lugar!encargo!idioma_enc!observ');
$h_encargos_zona = $oHashEncargosZona->linkSinVal();

$oHashBorrarEncargosZona = new Hash();
$oHashBorrarEncargosZona->setUrl($url_update_encargos_zona);
$oHashBorrarEncargosZona->setCamposForm('id_enc!que');
$h_borrar_encargos_zona = $oHashBorrarEncargosZona->linkSinVal();


$oHashNuevoEncargosZona = new Hash();
$oHashNuevoEncargosZona->setUrl($url_update_encargos_zona);
$oHashNuevoEncargosZona->setCamposForm('que!id_zona');
$h_borrar_encargos_zona = $oHashBorrarEncargosZona->linkSinVal();


$oGesEncargoTipo = new GestorEncargoTipo();

$grupo='8...';
//if (!empty($grupo)) {
    $aWhere = [];
    $aOperador = [];
    $aWhere['id_tipo_enc'] = '^' . $grupo;
    $aOperador['id_tipo_enc'] = '~';
    $oGesEncargoTipo = new GestorEncargoTipo();
    $cEncargoTipos = $oGesEncargoTipo->getEncargoTipos($aWhere, $aOperador);

    // desplegable de nom_tipo
    $posibles_encargo_tipo = [];
    foreach ($cEncargoTipos as $oEncargoTipo) {
        $posibles_encargo_tipo[$oEncargoTipo->getId_tipo_enc()] = $oEncargoTipo->getTipo_enc();
//        echo $oEncargoTipo->getId_tipo_enc().'-->'.$oEncargoTipo->getTipo_enc().'<br>';
    }
    $oDesplNoms = new Desplegable();
    $oDesplNoms->setNombre('id_tipo_enc');
    $oDesplNoms->setOpciones($posibles_encargo_tipo);
    $oDesplNoms->setOpcion_sel($id_tipo_enc);
    $oDesplNoms->setBlanco('t');

$aWhere = [];
$aWhere['status'] = 't';
$aWhere['id_zona'] = $Qid_zona;
$aWhere['_ordre'] = 'nombre_ubi';
$GesCentrosDl = new GestorCentroDl();
$cCentrosDl = $GesCentrosDl->getCentros($aWhere);
$GesCentrosSf = new GestorCentroEllas();
$cCentrosSf = $GesCentrosSf->getCentros($aWhere);
$cCentros = array_merge($cCentrosDl, $cCentrosSf);

$aCentros = [];
foreach ($cCentros as $oCentro) {
    $id_ubi = $oCentro->getId_ubi();
    $nombre_ubi = $oCentro->getNombre_ubi();

    $aCentros[$id_ubi] = $nombre_ubi;
}

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpcion_sel($id_ubi);
$oDesplCentros->setOpciones($aCentros);


$GesLocales = new usuarios\model\entity\GestorLocal();
$oDesplIdiomas = $GesLocales->getListaIdiomas();
$oDesplIdiomas->setNombre("idioma_enc");
$oDesplIdiomas->setOpcion_sel($idioma_enc);
$oDesplIdiomas->setBlanco(1);

$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'h_encargos_zona' => $h_encargos_zona,
    'h_borrar_encargos_zona' => $h_borrar_encargos_zona,
    'oDesplCentros' => $oDesplCentros,
    'oDesplNoms' => $oDesplNoms,
    'oDesplIdiomas' => $oDesplIdiomas,
    'id_zona' => $Qid_zona,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_encargos_zona.html.twig', $a_campos);