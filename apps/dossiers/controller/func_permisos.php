<?php
namespace dossiers\controller;
use web;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
function permiso($r,$rw,$depende,$pau,$id_pau){
	/*	$r = número referente al bit de lectura en decimal
		$rw = número referente al bit de lectura y escritura en decimal
		Se compara con el permiso que tengo; devuelve:
			1. Si no hay permisos,
			2. Si el permiso es de lectura y
			3. Si el permiso es de lectura/escritura.
		Los bits de las oficinas son los mismos que para el phplib (definidos en local.inc):
			adl(1),agd(2),des(8),est(16),scl(32),sg(64),sm(128),soi(256),sr(512),ss(1024),ocs(2048),dtor(4096)
	
	28.10.02	añado:  (para que tenga en cuenta la oficina)
		$depende = t o f (true, false) si se debe comprobar la oficina
		$pau = p,a,u según estemos hablando de personas, actividades o ubis
		$id_pau = el id correspondiente: id_nom, id_activ, id_ubi
		
	*/
	$oDB=$GLOBALS['oDB'];

	$userperm=preg_split('/,/',core\ConfigGlobal::permisos()); //array con los permisos del usuario
	list ($ok,$userbits) = $_SESSION['oPerm']->permsum($userperm); //suma de todos los permisos del usuario

	$lect=(($userbits & $r)); //true si tiene permiso de lectura
	$escr=(($userbits & $rw)); //true si tiene permiso de escritura

	$rta=1;
	if ($lect && $r) { $rta=2; }
	if ($escr && $rw) { $rta=3; }

	if ($depende=="t" && $rta==3 && $pau=="p"){
		// busco el id_tabla para saber de quién se trata y ver si es de mi oficina.
		$sql="SELECT id_tabla FROM personas WHERE id_nom=$id_pau";
		$oDBSt_id=$oDB->query($sql);
		$id_tabla=$oDBSt_id->fetchColumn();
		switch ($id_tabla) {
			case "n":
				if (!$_SESSION['oPerm']->have_perm("sm")) { return 2; }
				break;
			case "a":
				if (!$_SESSION['oPerm']->have_perm("agd")) { return 2; }
				break;
			case "s":
				if (!$_SESSION['oPerm']->have_perm("sg")) { return 2; }
				break;
			case "sss":
				if (!$_SESSION['oPerm']->have_perm("des")) { return 2; }
				break;
			case "cp_sss":
				if (!$_SESSION['oPerm']->have_perm("des")) { return 2; }
				break;
			case "pn":
				if (!$_SESSION['oPerm']->have_perm("sm")) { return 2; }
				break;
			case "pa":
				if (!$_SESSION['oPerm']->have_perm("agd")) { return 2; }
				break;
			case "psss":
				if (!$_SESSION['oPerm']->have_perm("des")) { return 2; }
				break;
				default;
		}
	}
	return $rta;
}

function perm_activ_pers($id_tabla,$sv) {
	// Esta función devuelve un array con los permisos (si o no) para asignar las
	// actividades (según el tipo: nº) según el tipo de persona de que se trate y 
	// quién seamos nosotros.
	
	//para no repetir los permisos comunes a sr,sg
	if (empty($sv)) { $sv="1"; }  //aqui sólo es sv.
	$sf=2;
	$ref_perm_sg = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 0),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 0),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 1),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 0),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 0),
						$sv."33..." => array ( 'nom'=>"cv agd", 	'perm'=> 0),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 1),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 0),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 0),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 0),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 1),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 1),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 1),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 0),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 0)
						);
	$ref_perm_sr = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 0),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 0),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 0),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 1),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 0),
						$sv."32..." => array ( 'nom'=>"cv agd", 	'perm'=> 0),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 0),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 1),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 0),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 0),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 0),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 0),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 0),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 0),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 0)
					);
	$ref_perm_ss = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 1),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 1),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 1),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 1),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 1),
						$sv."33..." => array ( 'nom'=>"cv agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 1),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 1),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 1),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 1),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 1),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 1),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 1),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 1),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 1),
						$sf."....." => array ( 'nom'=>"sf",	'perm'=> 1)
					);
	switch ($id_tabla) {
		case "n" : //------------------------- numerarios -------------------
		case "pn": 
			if ($_SESSION['oPerm']->have_perm("sm")) { 
				$ref_perm = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 1),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 1),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 1),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 1),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 1),
						$sv."33..." => array ( 'nom'=>"cv agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 1),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 1),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 1),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 1),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 1),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 1),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 0),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
					);
			}
			if ($_SESSION['oPerm']->have_perm("agd")) {
				$ref_perm = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 0),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 1),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 0),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 0),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 0),
						$sv."33..." => array ( 'nom'=>"cv agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 0),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 0),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 0),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 0),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 0),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 0),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 0),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
					);
			}
			if ($_SESSION['oPerm']->have_perm("sg")) {
				$ref_perm = $ref_perm_sg;
			}
			if ($_SESSION['oPerm']->have_perm("sr")) {
				$ref_perm = $ref_perm_sr;
			}
			if ($_SESSION['oPerm']->have_perm("est")) {
				$ref_perm = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 0),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 0),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 0),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 0),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 1),
						$sv."33..." => array ( 'nom'=>"cv agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 0),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 0),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 0),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 0),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 0),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 0),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 0),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 0),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
					);
			}
			if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des") ) {
				$ref_perm = $ref_perm_ss;
			}
			break;
		case "a" : //------------------------- agregados -------------------
		case "pa":
			if ($_SESSION['oPerm']->have_perm("sm")) { 
				$ref_perm = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 0),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 0),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 0),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 0),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 0),
						$sv."33..." => array ( 'nom'=>"cv agd", 	'perm'=> 0),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 0),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 0),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 1),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 0),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 0),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 0),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 0),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 0),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
					);
			}
			if ($_SESSION['oPerm']->have_perm("agd")) {
				$ref_perm = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 0),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 1),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 1),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 1),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 0),
						$sv."33..." => array ( 'nom'=>"cv agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 1),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 1),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 1),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 1),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 1),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 1),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 0),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
					);
			}
			if ($_SESSION['oPerm']->have_perm("sg")) {
				$ref_perm = $ref_perm_sg;
			}
			if ($_SESSION['oPerm']->have_perm("sr")) {
				$ref_perm = $ref_perm_sr;
			}
			if ($_SESSION['oPerm']->have_perm("est")) {
				$ref_perm = array (
						$sv."11..." => array ( 'nom'=>"crt n", 	'perm'=> 0),
						$sv."31..." => array ( 'nom'=>"crt agd", 	'perm'=> 0),
						$sv."41..." => array ( 'nom'=>"crt s", 	'perm'=> 0),
						$sv."71..." => array ( 'nom'=>"crt sr", 	'perm'=> 0),
						$sv."12..." => array ( 'nom'=>"ca n", 	'perm'=> 0),
						$sv."33..." => array ( 'nom'=>"cv agd", 	'perm'=> 1),
						$sv."43..." => array ( 'nom'=>"cv s", 	'perm'=> 0),
						$sv."73..." => array ( 'nom'=>"cv sr", 	'perm'=> 0),
						$sv."14..." => array ( 'nom'=>"cve n", 	'perm'=> 0),
						$sv."34..." => array ( 'nom'=>"cve agd", 	'perm'=> 0),
						$sv."43..." => array ( 'nom'=>"cve s", 	'perm'=> 0),
						$sv."51..." => array ( 'nom'=>"sg crt", 	'perm'=> 0),
						$sv."53..." => array ( 'nom'=>"sg cv", 	'perm'=> 0),
						$sv."61..." => array ( 'nom'=>"crt sss+",	'perm'=> 0),
						$sv."63..." => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
						$sv."64..." => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
					);
			}
			if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des") ) {
				$ref_perm = $ref_perm_ss;
			}
			break;
		case "s": //------------------------- supernumerarios -------------------
			if ($_SESSION['oPerm']->have_perm("agd") || $_SESSION['oPerm']->have_perm("sm") || $_SESSION['oPerm']->have_perm("sg")) {
				$ref_perm = $ref_perm_sg;
			}
			if ($_SESSION['oPerm']->have_perm("sr")) {
				$ref_perm = $ref_perm_sr;
			}
			if ($_SESSION['oPerm']->have_perm("est")) {
				$ref_perm = $ref_perm_est;
			}
			if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des") ) {
				$ref_perm = $ref_perm_ss;
			}
			break;
		case "psss":
		case "sss": //------------------------- sss+ -------------------
			if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des") ) {
				$ref_perm = $ref_perm_ss;
			}
		case "cp_sss": //------------------------- sss+ -------------------
			if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des") ) {
				$ref_perm = $ref_perm_ss;
			}
		break;
		default;
	}
//$ref_perm = $ref_perm_sg;
return $ref_perm;
}

function perm_pers_activ($id_tipo_activ) {
	// Esta función devuelve un array con los permisos (si o no) para añadir las
	// personas (agd, n...) según el tipo de actividad de que se trate y 
	// quién seamos nosotros.
	

//para inicializar la matriz:
$ref_perm = array (
						"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 0),
						"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 0),
						"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
						"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 0),
						"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 0),
						"sss" => array ( 'nom'=> "sss+", 'tabla'=>"p_sssc", 	'perm'=> 0),
						"psss" => array ( 'nom'=> "sss+ de paso", 'tabla'=>"p_de_paso&na=sss", 	'perm'=> 0),
						"cp_sss" => array ( 'nom'=> "cp&ae sss+", 'tabla'=>"p_cp_ae_sssc&na=cp_sss", 	'perm'=> 0)
					);
//para no repetir los permisos comunes a sr,sg est
$ref_perm_sg = array (
						"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
						"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 0),
						"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 1),
						"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
						"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 0)
					);
					
//$tipo=actividad_de_tipo($id_activ);
//$asistentes=$tipo["asistentes"];

$oTipoActiv= new web\TiposActividades($id_tipo_activ);
$asistentes=$oTipoActiv->getAsistentesText();
switch ($asistentes) {
	case "sss+" :
		if ($_SESSION['oPerm']->have_perm("des")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"psss" => array ( 'nom'=> "sss+ de paso", 'tabla'=>"p_de_paso&na=sss", 	'perm'=> 1),
							"sss" => array ( 'nom'=> "sss+", 'tabla'=>"p_sssc",	'perm'=> 1),
							"cp_sss" => array ( 'nom'=> "cp&ae sss+", 'tabla'=>"p_cp_ae_sssc", 	'perm'=> 1)
							);
		}
		break;
	case "n" :
		if ($_SESSION['oPerm']->have_perm("sm")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 0),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 0)
						);
		}
		if ($_SESSION['oPerm']->have_perm("agd") AND ($id_tipo_activ=="114025" OR $id_tipo_activ=="114026")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 0),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 0),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		if ($_SESSION['oPerm']->have_perm("est")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		break;
	case "agd":
		if ($_SESSION['oPerm']->have_perm("sm")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 0),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 0)
						);
		}
		if ($_SESSION['oPerm']->have_perm("agd")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		if ($_SESSION['oPerm']->have_perm("des")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1),
							"sss" => array ( 'nom'=> "sss+", 'tabla'=>"p_sssc",	'perm'=> 1)
						);
		}
		if ($_SESSION['oPerm']->have_perm("est")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		break;
		break;
	case "s":
		if ($_SESSION['oPerm']->have_perm("sm")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 0),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 0)
						);
		}
		if ($_SESSION['oPerm']->have_perm("agd")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 0),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 0),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}																																																																								
		if ($_SESSION['oPerm']->have_perm("sg")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 1),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		if ($_SESSION['oPerm']->have_perm("des")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		break;
	case "sg":
		if ($_SESSION['oPerm']->have_perm("sm")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 0),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 0)
						);
		}
		if ($_SESSION['oPerm']->have_perm("agd")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 0),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 0),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}																																																																								
		if ($_SESSION['oPerm']->have_perm("sg")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 1),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		if ($_SESSION['oPerm']->have_perm("des")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1),
							"sss" => array ( 'nom'=> "sss+", 'tabla'=>"p_sssc", 	'perm'=> 1),
							"psss" => array ( 'nom'=> "sss+ de paso", 'tabla'=>"p_de_paso&na=sss", 	'perm'=> 1)
						);
		}
		break;
	case "sr":
		if ($_SESSION['oPerm']->have_perm("sm")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 0),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 0)
						);
		}
		if ($_SESSION['oPerm']->have_perm("agd")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 0),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 0),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}																																																																								
		if ($_SESSION['oPerm']->have_perm("sg")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 0),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 0),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 1),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 0),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 0)
						);
		}
		if ($_SESSION['oPerm']->have_perm("sr")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 1),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1)
						);
		}
		if ($_SESSION['oPerm']->have_perm("des")) {
			$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'tabla'=>"p_numerarios", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'tabla'=>"p_agregados", 	'perm'=> 1),
							"s" => array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'tabla'=>"p_de_paso&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'tabla'=>"p_de_paso&na=a", 	'perm'=> 1),
							"sss" => array ( 'nom'=> "sss+", 'tabla'=>"p_sssc", 	'perm'=> 1),
							"psss" => array ( 'nom'=> "sss+ de paso", 'tabla'=>"p_de_paso&na=sss", 	'perm'=> 1)
						);
		}
		break;
}
//$ref_perm = $ref_perm_sg;
return $ref_perm;
}
?>
