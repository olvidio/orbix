<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ViewTwig;
use encargossacd\model\entity\EncargoTipo;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoTipo;
use src\ubis\application\repositories\CentroDlRepository;
use src\ubis\application\repositories\CentroEllasRepository;
use src\ubis\domain\entity\Ubi;
use src\usuarios\application\repositories\LocalRepository;
use web\Desplegable;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qorden = (string)filter_input(INPUT_POST, 'orden');

$columns_cuadricula = [
    ["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 250, "cssClass" => "cell-title"],
    ["id" => "tipo_encargo", "name" => "Tipo de encargo", "field" => "tipo_encargo", "width" => 200, "cssClass" => "cell-title"],
//    ["id" => "id_tipo_enc", "name" => "id Tipo de encargo", "field" => "id_tipo_enc", "width" => 150, "cssClass" => "cell-title"],
//    ["id" => "id_ubi", "name" => "id ubi", "field" => "id_ubi", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "lugar", "name" => "Lugar", "field" => "lugar", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "orden", "name" => "Orden", "field" => "orden", "width" => 100, "cssClass" => "cell-title"],
    ["id" => "prioridad", "name" => "Prioridad", "field" => "prioridad", "width" => 100, "cssClass" => "cell-title"],
    ["id" => "descripcion_lugar", "name" => "Descripción lugar", "field" => "descripcion_lugar", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "nom_idioma", "name" => "Idioma", "field" => "nom_idioma", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "observ", "name" => "Observaciones", "field" => "observ", "width" => 150, "cssClass" => "cell-title"],
];

$data_cuadricula = [];

$oGesEncargoTipo = new GestorEncargoTipo();

$grupo = '8...';
$aWhere = [];
$aOperador = [];
$aWhere['id_tipo_enc'] = '^' . $grupo;
$aOperador['id_tipo_enc'] = '~';
$oGesEncargoTipo = new GestorEncargoTipo();
$cEncargoTipos = $oGesEncargoTipo->getEncargoTipos($aWhere, $aOperador);

$a_tipo_enc = [];
$posibles_encargo_tipo = [];
foreach ($cEncargoTipos as $oEncargoTipo) {
    if ($oEncargoTipo->getId_tipo_enc() >= 8100) {
        $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
        $posibles_encargo_tipo[$oEncargoTipo->getId_tipo_enc()] = $oEncargoTipo->getTipo_enc();
    }

}

$aWhere = [];
$aOperador = [];
$cond_tipo_enc = "{" . implode(', ', $a_tipo_enc) . "}";
$aWhere['id_tipo_enc'] = $cond_tipo_enc;
$aOperador['id_tipo_enc'] = 'ANY';
$aWhere['id_zona'] = $Qid_zona;

$aWhere['_ordre'] = $Qorden;
$GesEncargos = new GestorEncargo();
$cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);

$id_tipo_enc = '';
$idioma_enc = '';
foreach ($cEncargos as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
    $id_ubi = $oEncargo->getId_ubi();
    $id_tipo_enc = $oEncargo->getId_tipo_enc();
    $desc_lugar = $oEncargo->getDesc_lugar();
    $idioma_enc = $oEncargo->getIdioma_enc();
    $orden = $oEncargo->getOrden();
    $prioridad = $oEncargo->getPrioridad();
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
    $LocalRepository = new LocalRepository();
    $cIdiomas = $LocalRepository->getLocales(['idioma' => $idioma_enc]);
    if (is_array($cIdiomas) && count($cIdiomas) > 0) {
        $nom_idioma = $cIdiomas[0]->getNom_idioma();
    }

    $d = 0;
    $data_cols = [];
    $meta_dia = '';
    $data_cols["encargo"] = $desc_enc;
    $data_cols["id_enc"] = $id_enc;
    $data_cols["id_tipo_enc"] = $id_tipo_enc;
    $data_cols["tipo_encargo"] = $tipo_enc;
    $data_cols["meta"] = $meta_dia;
    $data_cols["id_ubi"] = $id_ubi;
    $data_cols["lugar"] = $nombre_ubi;
    $data_cols["idioma_enc"] = $idioma_enc;
    $data_cols["nom_idioma"] = $nom_idioma;
    $data_cols["descripcion_lugar"] = $desc_lugar;
    $data_cols["orden"] = $orden;
    $data_cols["prioridad"] = $prioridad;
    $data_cols["observ"] = $observ;
    $data_cuadricula[] = $data_cols;
}

$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$url_update_encargos_zona = 'apps/misas/controller/update_encargos_zona.php';
$oHashEncargosZona = new Hash();
$oHashEncargosZona->setUrl($url_update_encargos_zona);
$oHashEncargosZona->setCamposForm('id_enc!que!id_tipo_enc!id_ubi!id_zona!descripcion_lugar!encargo!idioma_enc!observ!orden!prioridad');
$h_encargos_zona = $oHashEncargosZona->linkSinVal();

$url_ver_encargos_zona = 'apps/misas/controller/ver_encargos_zona.php';
$oHashVerEncargosZona = new Hash();
$oHashVerEncargosZona->setUrl($url_ver_encargos_zona);
$oHashVerEncargosZona->setCamposForm('id_zona!orden');
$h_ver_encargos_zona = $oHashVerEncargosZona->linkSinVal();

$oHashBorrarEncargosZona = new Hash();
$oHashBorrarEncargosZona->setUrl($url_update_encargos_zona);
$oHashBorrarEncargosZona->setCamposForm('id_enc!que');
$h_borrar_encargos_zona = $oHashBorrarEncargosZona->linkSinVal();

$oHashNuevoEncargosZona = new Hash();
$oHashNuevoEncargosZona->setUrl($url_update_encargos_zona);
$oHashNuevoEncargosZona->setCamposForm('que!id_zona');
$h_borrar_encargos_zona = $oHashBorrarEncargosZona->linkSinVal();

$aWhere = [];
$aOperador = [];
$cond_tipo_enc = "{" . implode(', ', $a_tipo_enc) . "}";
$aWhere['id_tipo_enc'] = $cond_tipo_enc;
$aOperador['id_tipo_enc'] = 'ANY';
$aWhere['id_zona'] = $Qid_zona;

$aWhere['_ordre'] = $Qorden;
$GesEncargos = new GestorEncargo();
$cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);

$id_tipo_enc = '';
$idioma_enc = '';
$oDesplNoms = new Desplegable();
$oDesplNoms->setNombre('id_tipo_enc');
$oDesplNoms->setOpciones($posibles_encargo_tipo);
$oDesplNoms->setOpcion_sel($id_tipo_enc);
$oDesplNoms->setBlanco('t');

$aWhere = [];
$aWhere['status'] = 't';
$aWhere['id_zona'] = $Qid_zona;
$aWhere['_ordre'] = 'nombre_ubi';
$GesCentrosDl = new CentroDlRepository();
$cCentrosDl = $GesCentrosDl->getCentros($aWhere);
$GesCentrosSf = new CentroEllasRepository();
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
$oDesplCentros->setOpciones($aCentros);


$LocalRepository = new LocalRepository();
$a_locales = $LocalRepository->getArrayLocales();
$oDesplIdiomas = new Desplegable("idioma_enc", $a_locales, $idioma_enc, true);

$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'h_encargos_zona' => $h_encargos_zona,
    'h_ver_encargos_zona' => $h_ver_encargos_zona,
    'url_ver_encargos_zona' => $url_ver_encargos_zona,
    'h_borrar_encargos_zona' => $h_borrar_encargos_zona,
    'url_update_encargos_zona' => $url_update_encargos_zona,
    'oDesplCentros' => $oDesplCentros,
    'oDesplNoms' => $oDesplNoms,
    'oDesplIdiomas' => $oDesplIdiomas,
    'id_zona' => $Qid_zona,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_encargos_zona.html.twig', $a_campos);