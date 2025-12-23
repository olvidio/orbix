<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ViewTwig;
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\GestorEncargo;
use encargossacd\model\entity\GestorEncargoTipo;
use misas\domain\repositories\EncargoCtrRepository;
use web\Desplegable;
use web\Hash;
use ubis\model\entity\GestorCentroEllos;
use ubis\model\entity\GestorCentroEllas;
use zonassacd\model\entity\GestorZona;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$columns_cuadricula = [
//   ["id" => "id_item", "name" => "Id Item", "field" => "id_item", "width" => 100, "cssClass" => "cell-title"],
//    ["id" => "id_encargo", "name" => "Id Encargo", "field" => "id_encargo", "width" => 50, "cssClass" => "cell-title"],
["id" => "encargo", "name" => "Encargo", "field" => "encargo", "width" => 250, "cssClass" => "cell-title"],
//    ["id" => "id_centro", "name" => "Id Centro", "field" => "id_centro", "width" => 100, "cssClass" => "cell-title"],
    ["id" => "centro", "name" => "Centro", "field" => "centro", "width" => 150, "cssClass" => "cell-title"],
];

$data_cuadricula = [];

$aCentros = [];
if (isset($Qid_zona)) {
    $aWhere = [];
    $aWhere['status'] = 't';
    $aWhere['id_zona'] = $Qid_zona;
    $aWhere['_ordre'] = 'nombre_ubi';
    $GesCentrossv = new GestorCentroEllos();
    $cCentrossv = $GesCentrossv->getCentros($aWhere);
    $GesCentrosSf = new GestorCentroEllas();
    $cCentrosSf = $GesCentrosSf->getCentros($aWhere);
    $cCentros = array_merge($cCentrossv, $cCentrosSf);
    
    foreach ($cCentros as $oCentro) {
        $id_ubi = $oCentro->getId_ubi();
        $nombre_ubi = $oCentro->getNombre_ubi();

        $EncargoCtrRepository = new EncargoCtrRepository();
        $cEncargosCtr = $EncargoCtrRepository->getEncargosCentro($id_ubi);
        foreach ($cEncargosCtr as $oEncargo) {
            $id_enc = $oEncargo->getId_enc();
            $id_item = $oEncargo->getUuid_item()->value();
            $data_cols = [];
            $data_cols["id_item"] = $id_item;
            $data_cols["id_encargo"] = $id_enc;
            $oEncargo = new Encargo($id_enc);
            $desc_enc = $oEncargo->getDesc_enc();
            $data_cols["encargo"] = $desc_enc;
            $data_cols["id_centro"] = $id_ubi;
            $data_cols["centro"] = $nombre_ubi;
            $data_cuadricula[] = $data_cols;
        }
    }    
}

$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$url_update_encargos_centros = 'apps/misas/controller/update_encargos_centros.php';
$oHashEncargosCtr = new Hash();
$oHashEncargosCtr->setUrl($url_update_encargos_centros);
$oHashEncargosCtr->setCamposForm('id_item!id_enc!que!id_ctr!id_zona');
$h_update_encargos_centros = $oHashEncargosCtr->linkSinVal();

$url_ver_encargos_centros = 'apps/misas/controller/ver_encargos_centros.php';
$oHashVerEncargosCtr = new Hash();
$oHashVerEncargosCtr->setUrl($url_ver_encargos_centros);
$oHashVerEncargosCtr->setCamposForm('id_zona');
$h_ver_encargos_centros = $oHashVerEncargosCtr->linkSinVal();

$oHashBorrarEncargosCtr = new Hash();
$oHashBorrarEncargosCtr->setUrl($url_update_encargos_centros);
$oHashBorrarEncargosCtr->setCamposForm('id_item');
$h_borrar_encargos_centros = $oHashBorrarEncargosCtr->linkSinVal();

$oHashNuevoEncargosCtr = new Hash();
$oHashNuevoEncargosCtr->setUrl($url_update_encargos_centros);
$oHashNuevoEncargosCtr->setCamposForm('id_enc!id_ubi');
$h_nuevo_encargos_centros = $oHashNuevoEncargosCtr->linkSinVal();

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
    if ($oEncargoTipo->getId_tipo_enc()>=8100) {
        $a_tipo_enc[] = $oEncargoTipo->getId_tipo_enc();
        $posibles_encargo_tipo[$oEncargoTipo->getId_tipo_enc()] = $oEncargoTipo->getTipo_enc();
    }
}

$aWhere = [];
$aOperador = [];
$aEncargos = [];
$cond_tipo_enc = "{" . implode(', ', $a_tipo_enc) . "}";
$aWhere['id_tipo_enc'] = $cond_tipo_enc;
$aOperador['id_tipo_enc'] = 'ANY';
$aWhere['id_zona'] = $Qid_zona;

$GesEncargos = new GestorEncargo();
$cEncargos = $GesEncargos->getEncargos($aWhere, $aOperador);
foreach ($cEncargos as $oEncargo) {
    $id_enc = $oEncargo->getId_enc();
    $desc_enc = $oEncargo->getDesc_enc();
    $aEncargos[$id_enc] = $desc_enc;
}

$oDesplEncargos = new Desplegable();
$oDesplEncargos->setNombre('id_enc');
$oDesplEncargos->setOpciones($aEncargos);

$oGestorZonaCtr = new GestorZona();
$aOpciones = $oGestorZonaCtr->getArrayZonas();
$oDesplZonasCtr = new Desplegable();
$oDesplZonasCtr->setOpciones($aOpciones);
$oDesplZonasCtr->setBlanco(FALSE);
$oDesplZonasCtr->setNombre('id_zona_ctr');
$oDesplZonasCtr->setOpcion_sel($Qid_zona);
$oDesplZonasCtr->setAction('fnjs_prepara_select_ctr()');

$aWhere = [];
$aWhere['status'] = 't';
$aWhere['id_zona'] = $Qid_zona;
$aWhere['_ordre'] = 'nombre_ubi';
$GesCentrossv = new GestorCentroEllos();
$cCentrossv = $GesCentrossv->getCentros($aWhere);
$GesCentrosSf = new GestorCentroEllas();
$cCentrosSf = $GesCentrosSf->getCentros($aWhere);
$cCentros = array_merge($cCentrossv, $cCentrosSf);

$aCentros = [];
foreach ($cCentros as $oCentro) {
    $id_ubi = $oCentro->getId_ubi();
    $nombre_ubi = $oCentro->getNombre_ubi();

    $aCentros[$id_ubi] = $nombre_ubi;
}

$oDesplCentros = new Desplegable();
$oDesplCentros->setNombre('id_ubi');
$oDesplCentros->setOpciones($aCentros);

$url_desplegable_ctr = 'apps/misas/controller/desplegable_ctr.php';
$oHash_desplegable_ctr = new Hash();
$oHash_desplegable_ctr->setUrl($url_desplegable_ctr);
$oHash_desplegable_ctr->setCamposForm('id_zona!id_ctr!ctr_otras_zonas');
$h_desplegable_ctr = $oHash_desplegable_ctr->linkSinVal();

$url_update_encargos_zona = 'apps/misas/controller/update_encargos_centros.php';
$oHashEncargosZona = new Hash();
$oHashEncargosZona->setUrl($url_update_encargos_zona);
$oHashEncargosZona->setCamposForm('id_enc!que!id_ctr!id_zona');
$h_encargos_zona = $oHashEncargosZona->linkSinVal();

$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'h_update_encargos_centros' => $h_update_encargos_centros,
    'h_ver_encargos_centros' => $h_ver_encargos_centros,
    'url_ver_encargos_centros' => $url_ver_encargos_centros,
    'h_borrar_encargos_centros' => $h_borrar_encargos_centros,
    'url_update_encargos_centros' => $url_update_encargos_centros,
    'url_desplegable_ctr' => $url_desplegable_ctr,
    'h_desplegable_ctr' => $h_desplegable_ctr,
    'oDesplZonasCtr' => $oDesplZonasCtr,
    'oDesplCentros' => $oDesplCentros,
    'oDesplEncargos' => $oDesplEncargos,
    'id_zona' => $Qid_zona,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_encargos_centros.html.twig', $a_campos);