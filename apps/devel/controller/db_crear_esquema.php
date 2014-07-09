<?php
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************



$region = empty($_POST['region'])? '' : $_POST['region'];
$dl = empty($_POST['dl'])? '' : $_POST['dl'];
$sv = empty($_POST['sv'])? '' : $_POST['sv'];
$sf = empty($_POST['sf'])? '' : $_POST['sf'];

$esquema = "$region-$dl";
$esquemav = $esquema.'v';
$esquemaf = $esquema.'f';

$aUsuarios = array( 
		'H-dlmE' => 'H-dlmE',
		'H-dlmEv' => 'H-dlmEv',
		'H-dlmEf' => 'H-dlmEf',
		);

/*
// CREAR USUARIOS 
$oDBRol = new core\DBRol();
foreach ($aUsuarios as $user=>$pwd) {
	$oDBRol->setUser($user);
	$oDBRol->setPwd($pwd);
	$oDBRol->crearUsuario();
}
*/

// COMUN
$esquemaRef = 'H-dlb';
$esquemaNew = $esquema;

$oDBEsquema = new core\DBEsquema();
$oDBEsquema->setDb('comun');
$oDBEsquema->setRef($esquemaRef);
$oDBEsquema->setNew($esquemaNew);
$oDBEsquema->crear();

// SV
if (!empty($sv)) {
	$esquemaRef = 'H-dlbv';
	$esquemaNew = $esquemav;

	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDb('sv');
	$oDBEsquema->setRef($esquemaRef);
	$oDBEsquema->setNew($esquemaNew);
	$oDBEsquema->crear();

	$oDBEsquema->fix_seq();
}
// SF
if (!empty($sf)) {
	$esquemaRef = 'H-dlbv';
	$esquemaNew = $esquemaf;

	$oDBEsquema = new core\DBEsquema();
	$oDBEsquema->setDbRef('sv');
	$oDBEsquema->setDb('sf');
	$oDBEsquema->setRef($esquemaRef);
	$oDBEsquema->setNew($esquemaNew);
	$oDBEsquema->crear();

	$oDBEsquema->fix_seq();
}
?>
