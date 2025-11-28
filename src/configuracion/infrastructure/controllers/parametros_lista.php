<?php

use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use web\ContestarJson;


$ConfigRepository = $GLOBALS['container']->get(ConfigSchemaRepositoryInterface::class);

// ----------- Periodo Curso crt -------------------
$parametro = 'curso_crt';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

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
$a_campos['aCursoCrt'] = $aCursoCrt;

// ----------- Periodo Curso stgr -------------------
$parametro = 'curso_stgr';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

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

$a_campos['aCursoStgr'] = $aCursoStgr;

// ----------- Jefe(s) Calendario -------------------
$parametro = 'jefe_calendario';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = 'daniii';
}
$val_jefe_calendario = $valor;
$a_campos['jefe_calendario'] = $val_jefe_calendario;

// ----------- Lugar centro(s) estudios -------------------
$parametro = 'ce_lugar';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = 'daniii';
}
$val_ce_lugar = $valor;

$a_campos['ce_lugar'] = $val_ce_lugar;

// ----------- Nombre región en latin (html) -------------------
$parametro = 'region_latin';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "HISPANI&#198;";
}
$val_region_latin = $valor;

$a_campos['region_latin'] = $val_region_latin;

// ----------- Nombre secretario estudios región stgr (certificados) -------------------
$parametro = 'vstgr';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "?";
}
$val_vstgr = $valor;

$a_campos['vstgr'] = $val_vstgr;

// ----------- Lugar firma stgr (certificados) -------------------
$parametro = 'lugar_firma';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "?";
}

$a_campos['lugar_firma'] = $valor;

// ----------- Direccion stgr (certificados) -------------------
$parametro = 'dir_stgr';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "?";
}

$a_campos['dir_stgr'] = $valor;

// ----------- Nota de corte (sibre 1) -------------------
$parametro = 'nota_corte';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "0.6";
}
$val_nota_corte = $valor;

$a_campos['nota_corte'] = $val_nota_corte;

// ----------- Nota máxima evaluación -------------------
$parametro = 'nota_max';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "10";
}
$val_nota_max = $valor;

$a_campos['nota_max'] = $val_nota_max;

// ----------- Años en los que caduca el asignatura cursada  -------------------
$parametro = 'caduca_cursada';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "2";
}
$val_caduca_cursada = $valor;

$a_campos['caduca_cursada'] = $val_caduca_cursada;

// ----------- Idioma por defecto de la dl -------------------
$parametro = 'idioma_default';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

$LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
$a_locales = $LocalRepository->getArrayLocales();

$a_campos['a_locales'] = $a_locales;
$a_campos['idioma_select'] = $valor;

// ----------- Ámbito: delegación o región -------------------
$parametro = 'ambito';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "dl";
}
$chk_dl = ($valor === 'dl') ? 'checked' : '';
$chk_r = ($valor === 'r') ? 'checked' : '';
$chk_rstgr = ($valor === 'rstgr') ? 'checked' : '';

$a_campos['chk_dl'] = $chk_dl;
$a_campos['chk_r'] = $chk_r;
$a_campos['chk_rstgr'] = $chk_rstgr;

// ----------- Gestión calendario: centralizada o por oficinas -------------------
$parametro = 'gesCalendario';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

if (empty($valor)) {
    $valor = "central";
}
$chk_central = ($valor === 'central') ? 'checked' : '';
$chk_of = ($valor === 'oficinas') ? 'checked' : '';

$a_campos['chk_central'] = $chk_central;
$a_campos['chk_of'] = $chk_of;

// ----------- Inicio Contador Certificados -------------------
$parametro = 'ini_contador_certificados';
$oConfigSchema = $ConfigRepository->findById($parametro);
$valor = $oConfigSchema?->getValorVo()?->value();

$a_campos['ini_contador_certificados'] = $valor;

$error_txt = '';
$data = $a_campos;

// envía una Response
ContestarJson::enviar($error_txt, $data);