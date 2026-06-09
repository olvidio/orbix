<?php

use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;
use frontend\shared\FrontBootstrap;


// Crea los objetos de uso global **********************************************
require_once __DIR__ . '/../helpers/configuracion_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************


$url_backend = '/src/configuracion/parametros_lista';
$data = PostRequest::getDataFromUrl($url_backend);

if (!empty($data['error'])) {
    exit($data['error']);
}

$a_campos = configuracion_parametros_view_from_payload($data);
$idiomaDespl = configuracion_parametros_idioma_desplegable($data);

// añadir url update
$url = 'src/configuracion/parametros_update';
$a_campos['url'] = $url;

// añado los hash de cada campo
// ----------- Periodo Curso crt -------------------
$parametro = 'curso_crt';
$oHashCrt = new HashFront();
$oHashCrt->setUrl($url);
$oHashCrt->setCamposForm('ini_dia!ini_mes!fin_dia!fin_mes');
$oHashCrt->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCrt'] = $oHashCrt;

// ----------- Periodo Curso stgr -------------------
$parametro = 'curso_stgr';
$oHashStgr = new HashFront();
$oHashStgr->setUrl($url);
$oHashStgr->setCamposForm('ini_dia!ini_mes!fin_dia!fin_mes');
$oHashStgr->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashStgr'] = $oHashStgr;

// ----------- Jefe(s) Calendario -------------------
$parametro = 'jefe_calendario';
$oHashJC = new HashFront();
$oHashJC->setUrl($url);
$oHashJC->setCamposForm('valor');
$oHashJC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashJC'] = $oHashJC;

// ----------- Lugar centro(s) estudios -------------------
$parametro = 'ce_lugar';
$oHashCE = new HashFront();
$oHashCE->setUrl($url);
$oHashCE->setCamposForm('valor');
$oHashCE->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCE'] = $oHashCE;

// ----------- Nombre región en latin (html) -------------------
$parametro = 'region_latin';
$oHashRL = new HashFront();
$oHashRL->setUrl($url);
$oHashRL->setCamposForm('valor');
$oHashRL->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashRL'] = $oHashRL;

// ----------- Nombre secretario estudios región stgr (certificados) -------------------
$parametro = 'vstgr';
$oHashVE = new HashFront();
$oHashVE->setUrl($url);
$oHashVE->setCamposForm('valor');
$oHashVE->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashVE'] = $oHashVE;

// ----------- Lugar firma stgr (certificados) -------------------
$parametro = 'lugar_firma';
$oHashLF = new HashFront();
$oHashLF->setUrl($url);
$oHashLF->setCamposForm('valor');
$oHashLF->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashLF'] = $oHashLF;

// ----------- Direccion stgr (certificados) -------------------
$parametro = 'dir_stgr';
$oHashDir = new HashFront();
$oHashDir->setUrl($url);
$oHashDir->setCamposForm('valor');
$oHashDir->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashDir'] = $oHashDir;

// ----------- Nota de corte (sibre 1) -------------------
$parametro = 'nota_corte';
$oHashNC = new HashFront();
$oHashNC->setUrl($url);
$oHashNC->setCamposForm('valor');
$oHashNC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashNC'] = $oHashNC;


// ----------- Nota máxima evaluación -------------------
$parametro = 'nota_max';
$oHashN = new HashFront();
$oHashN->setUrl($url);
$oHashN->setCamposForm('valor');
$oHashN->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashN'] = $oHashN;

// ----------- Años en los que caduca el asignatura cursada  -------------------
$parametro = 'caduca_cursada';
$oHashC = new HashFront();
$oHashC->setUrl($url);
$oHashC->setCamposForm('valor');
$oHashC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashC'] = $oHashC;

// ----------- Idioma por defecto de la dl -------------------
$parametro = 'idioma_default';
$oHashI = new HashFront();
$oHashI->setUrl($url);
$oHashI->setCamposForm('valor');
$oHashI->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashI'] = $oHashI;

$oDeplIdiomas = new Desplegable('valor', $idiomaDespl['a_locales'], $idiomaDespl['idioma_select'], true);

if (empty($valor)) {
    //$valor = "es_ES.UTF-8";
    $oDeplIdiomas->setOpcion_sel('es_ES.UTF-8');
}
$val_idioma_default = $oDeplIdiomas;

$a_campos['idioma_default'] = $val_idioma_default;

// ----------- Ámbito: delegación o región -------------------
$parametro = 'ambito';
$oHashDLR = new HashFront();
$oHashDLR->setUrl($url);
$oHashDLR->setCamposForm('valor');
$oHashDLR->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashDLR'] = $oHashDLR;

// ----------- Gestión calendario: centralizada o por oficinas -------------------
$parametro = 'gesCalendario';
$oHashCal = new HashFront();
$oHashCal->setUrl($url);
$oHashCal->setCamposForm('valor');
$oHashCal->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCal'] = $oHashCal;

// ----------- Inicio Contador Certificados -------------------
$parametro = 'ini_contador_certificados';
$oHashC1 = new HashFront();
$oHashC1->setUrl($url);
$oHashC1->setcamposForm('valor');
$oHashC1->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashC1'] = $oHashC1;


$oView = new ViewNewTwig('frontend/configuracion/controller');
$oView->renderizar('parametros.html.twig', $a_campos);
