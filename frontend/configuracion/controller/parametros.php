<?php

use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\web\Hash;


// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$url_backend = '/src/configuracion/infrastructure/controllers/parametros_lista.php';
$data = PostRequest::getDataFromUrl($url_backend);

if (!empty($data['error'])) {
    exit($data['error']);
}

$a_campos = $data;

// añadir url update
$url = 'src/configuracion/infrastructure/controllers/parametros_update.php';
$a_campos['url'] = $url;

// añado los hash de cada campo
// ----------- Periodo Curso crt -------------------
$parametro = 'curso_crt';
$oHashCrt = new Hash();
$oHashCrt->setUrl($url);
$oHashCrt->setCamposForm('ini_dia!ini_mes!fin_dia!fin_mes');
$oHashCrt->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCrt'] = $oHashCrt;

// ----------- Periodo Curso stgr -------------------
$parametro = 'curso_stgr';
$oHashStgr = new Hash();
$oHashStgr->setUrl($url);
$oHashStgr->setCamposForm('ini_dia!ini_mes!fin_dia!fin_mes');
$oHashStgr->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashStgr'] = $oHashStgr;

// ----------- Jefe(s) Calendario -------------------
$parametro = 'jefe_calendario';
$oHashJC = new Hash();
$oHashJC->setUrl($url);
$oHashJC->setCamposForm('valor');
$oHashJC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashJC'] = $oHashJC;

// ----------- Lugar centro(s) estudios -------------------
$parametro = 'ce_lugar';
$oHashCE = new Hash();
$oHashCE->setUrl($url);
$oHashCE->setCamposForm('valor');
$oHashCE->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCE'] = $oHashCE;

// ----------- Nombre región en latin (html) -------------------
$parametro = 'region_latin';
$oHashRL = new Hash();
$oHashRL->setUrl($url);
$oHashRL->setCamposForm('valor');
$oHashRL->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashRL'] = $oHashRL;

// ----------- Nombre secretario estudios región stgr (certificados) -------------------
$parametro = 'vstgr';
$oHashVE = new Hash();
$oHashVE->setUrl($url);
$oHashVE->setCamposForm('valor');
$oHashVE->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashVE'] = $oHashVE;

// ----------- Lugar firma stgr (certificados) -------------------
$parametro = 'lugar_firma';
$oHashLF = new Hash();
$oHashLF->setUrl($url);
$oHashLF->setCamposForm('valor');
$oHashLF->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashLF'] = $oHashLF;

// ----------- Direccion stgr (certificados) -------------------
$parametro = 'dir_stgr';
$oHashDir = new Hash();
$oHashDir->setUrl($url);
$oHashDir->setCamposForm('valor');
$oHashDir->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashDir'] = $oHashDir;

// ----------- Nota de corte (sibre 1) -------------------
$parametro = 'nota_corte';
$oHashNC = new Hash();
$oHashNC->setUrl($url);
$oHashNC->setCamposForm('valor');
$oHashNC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashNC'] = $oHashNC;


// ----------- Nota máxima evaluación -------------------
$parametro = 'nota_max';
$oHashN = new Hash();
$oHashN->setUrl($url);
$oHashN->setCamposForm('valor');
$oHashN->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashN'] = $oHashN;

// ----------- Años en los que caduca el asignatura cursada  -------------------
$parametro = 'caduca_cursada';
$oHashC = new Hash();
$oHashC->setUrl($url);
$oHashC->setCamposForm('valor');
$oHashC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashC'] = $oHashC;

// ----------- Idioma por defecto de la dl -------------------
$parametro = 'idioma_default';
$oHashI = new Hash();
$oHashI->setUrl($url);
$oHashI->setCamposForm('valor');
$oHashI->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashI'] = $oHashI;

$a_locales = $data['a_locales'];
$idioma_select = $data['idioma_select'];
$oDeplIdiomas = new Desplegable('valor', $a_locales,$idioma_select,true);

if (empty($valor)) {
    //$valor = "es_ES.UTF-8";
    $oDeplIdiomas->setOpcion_sel('es_ES.UTF-8');
}
$val_idioma_default = $oDeplIdiomas;

$a_campos['idioma_default'] = $val_idioma_default;

// ----------- Ámbito: delegación o región -------------------
$parametro = 'ambito';
$oHashDLR = new Hash();
$oHashDLR->setUrl($url);
$oHashDLR->setCamposForm('valor');
$oHashDLR->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashDLR'] = $oHashDLR;

// ----------- Gestión calendario: centralizada o por oficinas -------------------
$parametro = 'gesCalendario';
$oHashCal = new Hash();
$oHashCal->setUrl($url);
$oHashCal->setCamposForm('valor');
$oHashCal->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCal'] = $oHashCal;

// ----------- Inicio Contador Certificados -------------------
$parametro = 'ini_contador_certificados';
$oHashC1 = new Hash();
$oHashC1->setUrl($url);
$oHashC1->setcamposForm('valor');
$oHashC1->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashC1'] = $oHashC1;


$oView = new ViewNewTwig('configuracion\\controller');
$oView->renderizar('parametros.html.twig', $a_campos);
