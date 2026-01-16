<?php

use core\ViewTwig;
use encargossacd\model\DesplCentros;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\value_objects\EncargoGrupo;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use web\Desplegable;
use web\Hash;

/**
 * Esta página muestra la atención sacerdotal de un centro.
 *
 * @package    delegacion
 * @subpackage    des
 * @author    Daniel Serrabou
 * @since        12/12/06.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qfiltro_ctr = (integer)filter_input(INPUT_POST, 'filtro_ctr');


if (!empty($Qid_ubi)) {
    // si empieza por 2 es de la sf.
    $Qid_ubi_txt = (string)$Qid_ubi;
    if ((int)$Qid_ubi_txt[0] === 2) {
        $CentroEllasRepository = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
        $oCentro = $CentroEllasRepository->findById($Qid_ubi);
    } else {
        $CentroDlRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $oCentro = $CentroDlRepository->findById($Qid_ubi);
    }
    $tipo_ubi = $oCentro->getTipo_ubi();
    $tipo_ctr = $oCentro->getTipo_ctr();

    if ($tipo_ubi === 'ctrsf') {
        $Qfiltro_ctr = EncargoGrupo::CENTRO_SF;
    } else {
        switch ($tipo_ctr) {
            case 'a':
            case 'n':
            case 's':
            case 'aj':
            case 'am':
            case 'nj':
            case 'nm':
            case 'sj':
            case 'sm':
                $Qfiltro_ctr = EncargoGrupo::CENTRO_SV;
                break;
            case 'ss':
                $Qfiltro_ctr = EncargoGrupo::CENTRO_SSSC;
                break;
            case 'igloc':
                $Qfiltro_ctr = EncargoGrupo::IGL;
                break;
            case 'cgioc':
            case 'oc':
                $Qfiltro_ctr = EncargoGrupo::CGI;
                break;
        }
    }
}

$EncargoAplicationService = $GLOBALS['container']->get(EncargoAplicacionService::class);

$opciones = $EncargoAplicationService->getArraySeccion();
$oDesplGrupoCtrs = new Desplegable();
$oDesplGrupoCtrs->setNombre('filtro_ctr');
$oDesplGrupoCtrs->setOpciones($opciones);
$oDesplGrupoCtrs->setOpcion_sel($Qfiltro_ctr);
$oDesplGrupoCtrs->setBlanco(1);
$oDesplGrupoCtrs->setAction("fnjs_lista_ctrs();");

$oGrupoCtr = new DesplCentros();
$oDesplCtrs = $oGrupoCtr->getDesplPorFiltro($Qfiltro_ctr);
$oDesplCtrs->setNombre('lst_ctrs');
$oDesplCtrs->setAction('fnjs_ver_ficha()');
if (!empty($Qid_ubi)) {
    $oDesplCtrs->setOpcion_sel($Qid_ubi);
}


$url_ctr = 'apps/encargossacd/controller/ctr_get_select.php';
$oHashCtr = new Hash();
$oHashCtr->setUrl($url_ctr);
$oHashCtr->setCamposForm('filtro_ctr!id_ubi');
$h_ctr = $oHashCtr->linkSinVal();

$url_ficha = 'apps/encargossacd/controller/ctr_get_ficha.php';
$oHashFicha = new Hash();
$oHashFicha->setUrl($url_ficha);
$oHashFicha->setCamposForm('id_ubi');
$h_ficha = $oHashFicha->linkSinVal();

$fase = 'fase real';

$a_campos = ['oPosicion' => $oPosicion,
    //'oHash' => $oHash,
    'fase' => $fase,
    'url_ctr' => $url_ctr,
    'h_ctr' => $h_ctr,
    'url_ficha' => $url_ficha,
    'h_ficha' => $h_ficha,
    'oDesplGrupoCtrs' => $oDesplGrupoCtrs,
    'oDesplCtrs' => $oDesplCtrs,
];

$oView = new ViewTwig('encargossacd/controller');
$oView->renderizar('ctr_ficha.html.twig', $a_campos);