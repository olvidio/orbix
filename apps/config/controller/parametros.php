<?php

use config\model\entity\ConfigSchema;
use core\ViewTwig;
use src\usuarios\application\repositories\LocalRepository;
use web\Desplegable;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
//	require_once ("classes/personas/ext_web_preferencias_gestor.class");

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$url = 'apps/config/controller/parametros_update.php';
$a_campos = ['url' => $url];

// ----------- Periodo Curso crt -------------------
$parametro = 'curso_crt';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

// valor es un json representa un array:
// ini_dia, ini_mes, fin_dia, fin_mes
if (empty($valor)) {
    $aCursoCrt = [
        'ini_dia' => 1,
        'ini_mes' => 9,
        'fin_dia' => 31,
        'fin_mes' => 8,
    ];
    $valor = json_encode($aCursoCrt);
}

$aCursoCrt = json_decode($valor, TRUE);

$oHashCrt = new Hash();
$oHashCrt->setUrl($url);
$oHashCrt->setCamposForm('ini_dia!ini_mes!fin_dia!fin_mes');
$oHashCrt->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCrt'] = $oHashCrt;
$a_campos['aCursoCrt'] = $aCursoCrt;

// ----------- Periodo Curso stgr -------------------
$parametro = 'curso_stgr';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

// valor es un json representa un array:
// ini_dia, ini_mes, fin_dia, fin_mes
if (empty($valor)) {
    $aCursoStgr = [
        'ini_dia' => 1,
        'ini_mes' => 10,
        'fin_dia' => 30,
        'fin_mes' => 9,
    ];
    $valor = json_encode($aCursoStgr);
}

$aCursoStgr = json_decode($valor, TRUE);

$oHashStgr = new Hash();
$oHashStgr->setUrl($url);
$oHashStgr->setCamposForm('ini_dia!ini_mes!fin_dia!fin_mes');
$oHashStgr->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashStgr'] = $oHashStgr;
$a_campos['aCursoStgr'] = $aCursoStgr;

// ----------- Jefe(s) Calendario -------------------
$parametro = 'jefe_calendario';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = 'daniii';
}
$val_jefe_calendario = $valor;

$oHashJC = new Hash();
$oHashJC->setUrl($url);
$oHashJC->setCamposForm('valor');
$oHashJC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashJC'] = $oHashJC;
$a_campos['jefe_calendario'] = $val_jefe_calendario;

// ----------- Lugar centro(s) estudios -------------------
$parametro = 'ce_lugar';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = 'daniii';
}
$val_ce_lugar = $valor;

$oHashCE = new Hash();
$oHashCE->setUrl($url);
$oHashCE->setCamposForm('valor');
$oHashCE->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCE'] = $oHashCE;
$a_campos['ce_lugar'] = $val_ce_lugar;

// ----------- Nombre región en latin (html) -------------------
$parametro = 'region_latin';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "HISPANI&#198;";
}
$val_region_latin = $valor;

$oHashRL = new Hash();
$oHashRL->setUrl($url);
$oHashRL->setCamposForm('valor');
$oHashRL->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashRL'] = $oHashRL;
$a_campos['region_latin'] = $val_region_latin;

// ----------- Nombre secretario estudios región stgr (certificados) -------------------
$parametro = 'vstgr';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "?";
}
$val_vstgr = $valor;

$oHashVE = new Hash();
$oHashVE->setUrl($url);
$oHashVE->setCamposForm('valor');
$oHashVE->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashVE'] = $oHashVE;
$a_campos['vstgr'] = $val_vstgr;

// ----------- Lugar firma stgr (certificados) -------------------
$parametro = 'lugar_firma';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "?";
}

$oHashLF = new Hash();
$oHashLF->setUrl($url);
$oHashLF->setCamposForm('valor');
$oHashLF->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashLF'] = $oHashLF;
$a_campos['lugar_firma'] = $valor;

// ----------- Direccion stgr (certificados) -------------------
$parametro = 'dir_stgr';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "?";
}

$oHashDir = new Hash();
$oHashDir->setUrl($url);
$oHashDir->setCamposForm('valor');
$oHashDir->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashDir'] = $oHashDir;
$a_campos['dir_stgr'] = $valor;

// ----------- Nota de corte (sibre 1) -------------------
$parametro = 'nota_corte';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "0.6";
}
$val_nota_corte = $valor;

$oHashNC = new Hash();
$oHashNC->setUrl($url);
$oHashNC->setCamposForm('valor');
$oHashNC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashNC'] = $oHashNC;
$a_campos['nota_corte'] = $val_nota_corte;


// ----------- Nota máxima evaluación -------------------
$parametro = 'nota_max';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "10";
}
$val_nota_max = $valor;

$oHashN = new Hash();
$oHashN->setUrl($url);
$oHashN->setCamposForm('valor');
$oHashN->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashN'] = $oHashN;
$a_campos['nota_max'] = $val_nota_max;

// ----------- Años en los que caduca el asignatura cursada  -------------------
$parametro = 'caduca_cursada';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "2";
}
$val_caduca_cursada = $valor;

$oHashC = new Hash();
$oHashC->setUrl($url);
$oHashC->setCamposForm('valor');
$oHashC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashC'] = $oHashC;
$a_campos['caduca_cursada'] = $val_caduca_cursada;

// ----------- Idioma por defecto de la dl -------------------
$parametro = 'idioma_default';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

$LocalRepository = new LocalRepository();
$a_locales = $LocalRepository->getArrayLocales();
$oDeplIdiomas = new Desplegable('valor', $a_locales,$valor,true);

if (empty($valor)) {
    //$valor = "es_ES.UTF-8";
    $oDeplIdiomas->setOpcion_sel('es_ES.UTF-8');
}
$val_idioma_default = $oDeplIdiomas;

$oHashI = new Hash();
$oHashI->setUrl($url);
$oHashI->setCamposForm('valor');
$oHashI->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashI'] = $oHashI;
$a_campos['idioma_default'] = $val_idioma_default;

// ----------- Ámbito: delegación o región -------------------
$parametro = 'ambito';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "dl";
}
$chk_dl = ($valor === 'dl') ? 'checked' : '';
$chk_r = ($valor === 'r') ? 'checked' : '';
$chk_rstgr = ($valor === 'rstgr') ? 'checked' : '';

$oHashDLR = new Hash();
$oHashDLR->setUrl($url);
$oHashDLR->setCamposForm('valor');
$oHashDLR->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashDLR'] = $oHashDLR;
$a_campos['chk_dl'] = $chk_dl;
$a_campos['chk_r'] = $chk_r;
$a_campos['chk_rstgr'] = $chk_rstgr;

// ----------- Gestión calendario: centralizada o por oficinas -------------------
$parametro = 'gesCalendario';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "central";
}
$chk_central = ($valor === 'central') ? 'checked' : '';
$chk_of = ($valor === 'oficinas') ? 'checked' : '';

$oHashCal = new Hash();
$oHashCal->setUrl($url);
$oHashCal->setCamposForm('valor');
$oHashCal->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCal'] = $oHashCal;
$a_campos['chk_central'] = $chk_central;
$a_campos['chk_of'] = $chk_of;

// ----------- Inicio Contador Certificados -------------------
$parametro = 'ini_contador_certificados';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

$oHashC1 = new Hash();
$oHashC1->setUrl($url);
$oHashC1->setcamposForm('valor');
$oHashC1->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashC1'] = $oHashC1;
$a_campos['ini_contador_certificados'] = $valor;


$oView = new ViewTwig('config/controller');
$oView->renderizar('parametros.html.twig', $a_campos);