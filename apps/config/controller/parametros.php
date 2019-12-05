<?php
use config\model\entity\ConfigSchema;
use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
//	require_once ("classes/personas/ext_web_preferencias_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$url = 'apps/config/controller/parametros_update.php';
$a_campos = [ 'url' => $url];

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
$oHashCrt->setcamposForm('ini_dia!ini_mes!fin_dia!fin_mes');
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
$oHashStgr->setcamposForm('ini_dia!ini_mes!fin_dia!fin_mes');
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
$oHashJC->setcamposForm('valor');
$oHashJC->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashJC'] = $oHashJC;
$a_campos['jefe_calendario'] = $val_jefe_calendario;

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
$oHashRL->setcamposForm('valor');
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
$oHashVE->setcamposForm('valor');
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
$oHashLF->setcamposForm('valor');
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
$oHashDir->setcamposForm('valor');
$oHashDir->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashDir'] = $oHashDir;
$a_campos['dir_stgr'] = $valor;

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
$oHashN->setcamposForm('valor');
$oHashN->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashN'] = $oHashN;
$a_campos['nota_max'] = $val_nota_max;

// ----------- Idioma por defecto de la dl -------------------
$parametro = 'idioma_default';
$oConfigSchema = new ConfigSchema($parametro);
$valor = $oConfigSchema->getValor();

if (empty($valor)) {
    $valor = "es_ES.UTF-8";
}
$val_idioma_default = $valor;

$oHashI = new Hash();
$oHashI->setUrl($url);
$oHashI->setcamposForm('valor');
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
$chk_dl = ($valor == 'dl')? 'checked' : ''; 
$chk_r = ($valor == 'r')? 'checked' : ''; 
$chk_rstgr = ($valor == 'rstgr')? 'checked' : ''; 

$oHashDLR = new Hash();
$oHashDLR->setUrl($url);
$oHashDLR->setcamposForm('valor');
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
$chk_central = ($valor == 'central')? 'checked' : ''; 
$chk_of = ($valor == 'oficinas')? 'checked' : ''; 

$oHashCal = new Hash();
$oHashCal->setUrl($url);
$oHashCal->setcamposForm('valor');
$oHashCal->setArrayCamposHidden(['parametro' => $parametro]);

$a_campos['oHashCal'] = $oHashCal;
$a_campos['chk_central'] = $chk_central;
$a_campos['chk_of'] = $chk_of;


$oView = new core\ViewTwig('config/controller');
echo $oView->render('parametros.html.twig',$a_campos);