<?php
namespace dossiers\model;
use core;
use personas\model as personas;
use web;
/**
 * Classe per gestionar permisos de dossiers
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/1/2014
 */
class PermDossier {

	function chek($r) {
		if (eregi("1|t|true|y|yes|s|si",$r)) $rta="checked";
		if (eregi("0|f|false|n|no",$r)) $rta="";
		return $rta;
	}

	function numero_dossiers($pau,$id_pau,$id_tipo_dossier,$oDB) {
		$sql="SELECT tabla_to FROM d_tipos_dossiers dt WHERE dt.id_tipo_dossier=$id_tipo_dossier";
		$oDBSt_nom=$oDB->query($sql);
		$tabla_dossier=$oDBSt_id->fetchColumn();
		// según sean personas, ubis o actividades:
		switch ($pau) {
			case 'p':
				$condicion="da.id_nom=$id_pau";
				break;
			case 'u':
				$condicion="da.id_ubi=$id_pau";
				break;
			case 'a':
				$condicion="da.id_activ=$id_pau";
				break;
		}
		$q_doss_a="SELECT * FROM $tabla_dossier da WHERE $condicion";
		$oDBSt_da=$oDB->query($q_doss_a);
		$n_da=$oDBSt_da->rowCount();
		return $n_da;
	}

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

		$userbits = $_SESSION['iPermMenus'];

		$lect=(($userbits & $r)); //true si tiene permiso de lectura
		$escr=(($userbits & $rw)); //true si tiene permiso de escritura

		$rta=1;
		if ($lect && $r) { $rta=2; }
		if ($escr && $rw) { $rta=3; }

		if ($depende=="t" && $rta==3 && $pau=="p"){
			// busco el id_tabla para saber de quién se trata y ver si es de mi oficina.
			$oPersona = personas\Persona::NewPersona($id_pau);
			if (!is_object($oPersona)) {
				$msg_err = "<br>$oPersona con id_nom: $id_pau";
				exit($msg_err);
			}
			$id_tabla = $oPersona->getId_tabla();
			switch ($id_tabla) {
				case "n":
					if (!$_SESSION['oPerm']->have_perm("sm")) { return 2; }
					break;
				case "x":
					if (!$_SESSION['oPerm']->have_perm("nax")) { return 2; }
					break;
				case "a":
					if (!$_SESSION['oPerm']->have_perm("agd")) { return 2; }
					break;
				case "s":
					if (!$_SESSION['oPerm']->have_perm("sg")) { return 2; }
					break;
				case "sssc":
					if (!$_SESSION['oPerm']->have_perm("des")) { return 2; }
					break;
				case "pn":
					if (!$_SESSION['oPerm']->have_perm("sm")) { return 2; }
					break;
				case "px":
					if (!$_SESSION['oPerm']->have_perm("nax")) { return 2; }
					break;
				case "pa":
					if (!$_SESSION['oPerm']->have_perm("agd")) { return 2; }
					break;
				case "psssc":
					if (!$_SESSION['oPerm']->have_perm("des")) { return 2; }
					break;
					default;
			}
		}
		return $rta;
	}

	function perm_activ_pers($id_tabla) {
		// Esta función devuelve un array con los permisos (si o no) para asignar las
		// actividades (según el tipo: nº) según el tipo de persona de que se trate y 
		// quién seamos nosotros.
		
		$oTiposActividades = new web\TiposActividades();
		$a_posibles_tipos = $oTiposActividades->getId_tipoPosibles('^...'); // Que sólo devuelva los tres primeros dígitos

		//para no repetir los permisos comunes a sr,sg
		$sf = 2;
		$sv = core\ConfigGlobal::mi_sfsv();
		$ref_perm_sg = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 1),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 0),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 1),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 0),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 1),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 1),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 1),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)
								);
		$ref_perm_sr = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 1),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."32" => array ( 'nom'=>"cv agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 1),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)
							);
		$ref_perm_ss = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 1),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 1),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 1),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 1),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 1),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 1),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 1),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 1),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 1),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 1),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 1),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 1),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 1),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 1),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 1),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 1),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 1),
								$sf.".." => array ( 'nom'=>"sf",	'perm'=> 1)
							);
		switch ($id_tabla) {
			case "n" : //------------------------- numerarios -------------------
			case "pn": 
				if ($_SESSION['oPerm']->have_perm("sm")) { 
					$ref_perm = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 1),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 1),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 1),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 1),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 1),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 1),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 1),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 1),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 1),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 1),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 1),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 1),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 1),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 1),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
							);


				}
				if ($_SESSION['oPerm']->have_perm("agd")) {
					$ref_perm = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 1),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 0),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 0),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
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
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 0),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 1),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 1),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 0),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
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
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 0),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 0),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 1),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
							);
				}
				if ($_SESSION['oPerm']->have_perm("agd")) {
					$ref_perm = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 1),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 1),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 1),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 1),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 1),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 1),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 1),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 1),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 1),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
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
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 0),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 1),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 0),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
							);
				}
				if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des") ) {
					$ref_perm = $ref_perm_ss;
				}
				break;
			case "x" : //------------------------- nax -------------------
			case "px":
				if ($_SESSION['oPerm']->have_perm("sm")) { 
					$ref_perm = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 0),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 0),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
							);
				}
				if ($_SESSION['oPerm']->have_perm("agd")) {
					$ref_perm = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 0),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 0),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
							);
				}
				if ($_SESSION['oPerm']->have_perm("nax")) {
					$ref_perm = array (
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 1),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 1),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 1),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 1),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 1),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
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
								$sv."11" => array ( 'nom'=>"crt n", 	'perm'=> 0),
								$sv."21" => array ( 'nom'=>"crt nax", 	'perm'=> 0),
								$sv."31" => array ( 'nom'=>"crt agd", 	'perm'=> 0),
								$sv."41" => array ( 'nom'=>"crt s", 	'perm'=> 0),
								$sv."71" => array ( 'nom'=>"crt sr", 	'perm'=> 0),
								$sv."12" => array ( 'nom'=>"ca n", 	'perm'=> 0),
								$sv."22" => array ( 'nom'=>"ca nax", 	'perm'=> 0),
								$sv."33" => array ( 'nom'=>"cv agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cv s", 	'perm'=> 0),
								$sv."73" => array ( 'nom'=>"cv sr", 	'perm'=> 0),
								$sv."14" => array ( 'nom'=>"cve n", 	'perm'=> 0),
								$sv."23" => array ( 'nom'=>"cv nax", 	'perm'=> 0),
								$sv."34" => array ( 'nom'=>"cve agd", 	'perm'=> 0),
								$sv."43" => array ( 'nom'=>"cve s", 	'perm'=> 0),
								$sv."51" => array ( 'nom'=>"sg crt", 	'perm'=> 0),
								$sv."53" => array ( 'nom'=>"sg cv", 	'perm'=> 0),
								$sv."61" => array ( 'nom'=>"crt sss+",	'perm'=> 0),
								$sv."63" => array ( 'nom'=>"cv sss+", 	'perm'=> 0),
								$sv."64" => array ( 'nom'=>"cve sss+",	'perm'=> 0)	
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
					//$ref_perm = $ref_perm_est;
					$ref_perm = $ref_perm_sr;
				}
				if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des") ) {
					$ref_perm = $ref_perm_ss;
				}
				break;
			case "psssc":
			case "sssc": //------------------------- sss+ -------------------
				if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des") ) {
					$ref_perm = $ref_perm_ss;
				}
			default;
		}

		//$ref_perm = $ref_perm_sg;
		// Quito los tipos que no exiten
		$ref_perm2 = array();
		foreach($ref_perm as $key=>$value) { 
			if (!isset($a_posibles_tipos[$key])) continue;
			$ref_perm2[$key] = $value;
		}
		return $ref_perm2;
	}

	function perm_pers_activ($id_tipo_activ) {
		// Esta función devuelve un array con los permisos (si o no) para añadir las
		// personas (agd, n...) según el tipo de actividad de que se trate y 
		// quién seamos nosotros.
		

	//para inicializar la matriz:
	$ref_perm = array (
							"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
							"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
							"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
							"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
							"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
							"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0),
							"sssc" => array ( 'nom'=> "sss+", 'obj'=>"PersonaSSSC", 	'perm'=> 0),
							"psssc" => array ( 'nom'=> "sss+ de paso", 'obj'=>"PersonaEx&na=sss", 	'perm'=> 0),
						);
	//para no repetir los permisos comunes a sr,sg est
	$ref_perm_sg = array (
							"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
							"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
							"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 1),
							"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
							"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
							"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
							"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
						);
						
	$oTipoActiv= new web\TiposActividades($id_tipo_activ);
	$asistentes = $oTipoActiv->getAsistentesText();

	switch ($asistentes) {
		case "sss+" :
			if ($_SESSION['oPerm']->have_perm("des")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"psssc" => array ( 'nom'=> "sss+ de paso", 'obj'=>"PersonaEx&na=sss", 	'perm'=> 1),
								"sssc" => array ( 'nom'=> "sss+", 'obj'=>"PersonaSSSC",	'perm'=> 1),
								);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			break;
		case "n" :
			if ($_SESSION['oPerm']->have_perm("sm")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("agd") AND ($id_tipo_activ=="114025" OR $id_tipo_activ=="114026")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("nax")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("vcsd") or $_SESSION['oPerm']->have_perm("des")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("est")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			break;
		case "agd":
			if ($_SESSION['oPerm']->have_perm("sm")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("agd")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("nax")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("des")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0),
								"sssc" => array ( 'nom'=> "sss+", 'obj'=>"PersonaSSSC",	'perm'=> 1)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("est")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			break;
		case "s":
			if ($_SESSION['oPerm']->have_perm("sm")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("agd")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("nax")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("sg")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 1),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("des")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			break;
		case "nax":
			if ($_SESSION['oPerm']->have_perm("sm")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("agd")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("nax")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 1),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 1)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("sg")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("des")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 1),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 1)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			break;
		case "sg":
			if ($_SESSION['oPerm']->have_perm("sm")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("agd")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("nax")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("sg")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 1),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("des")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0),
								"sssc" => array ( 'nom'=> "sss+", 'obj'=>"PersonaSSSC", 	'perm'=> 1),
								"psssc" => array ( 'nom'=> "sss+ de paso", 'obj'=>"PersonaEx&na=sss", 	'perm'=> 1)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			break;
		case "sr":
			if ($_SESSION['oPerm']->have_perm("sm")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("agd")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("nax")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 1),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 1)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("sg")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 0),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 0),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 1),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 0),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 0),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("sr")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 1),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 1),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 1)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			if ($_SESSION['oPerm']->have_perm("des")) {
				$ref_perm_of = array (
								"n" => array ( 'nom'=> "n", 'obj'=>"PersonaN", 	'perm'=> 1),
								"a" => array ( 'nom'=> "agd", 'obj'=>"PersonaAgd", 	'perm'=> 1),
								"s" => array ( 'nom'=> "s", 'obj'=>"PersonaS", 	'perm'=> 0),
								"x" => array ( 'nom'=> "nax", 'obj'=>"PersonaNax",   'perm'=> 0),
								"pn" => array ( 'nom'=> "n de paso", 'obj'=>"PersonaEx&na=n", 	'perm'=> 1),
								"pa" => array ( 'nom'=> "agd de paso", 'obj'=>"PersonaEx&na=a", 	'perm'=> 1),
								"px" => array ( 'nom'=> "nax de paso", 'obj'=>"PersonaEx&na=x", 	'perm'=> 0),
								"sssc" => array ( 'nom'=> "sss+", 'obj'=>"PersonaSSSC", 	'perm'=> 1),
								"psssc" => array ( 'nom'=> "sss+ de paso", 'obj'=>"PersonaEx&na=sss", 	'perm'=> 1)
							);
				$ref_perm = self::daniBoleanOr($ref_perm, $ref_perm_of);
			}
			break;
		}
		return $ref_perm;
	}

	/**
	  * Hago un or logico de los permisos por si un usuario tienen permiso para más de una oficina
	  * que se quede con el máximo de permisos.
	  *
	  *
	  */
	function daniBoleanOr($ref_perm,$ref_perm_of){
		$ref_perm_or = array();
		foreach ($ref_perm as $asis=>$a) {
			if (isset($ref_perm_of[$asis])) {
				$b = $ref_perm_of[$asis];
			} else {
				$b = array('nom'=> $a['nom'],'obj'=>$a['obj'],'perm'=> 0);
			}
			//$a = array ( 'nom'=> "s", 'tabla'=>"p_supernumerarios", 	'perm'=> 0),
			// Para asegurar:
			$perm_or = 0;
			if (($a['nom'] == $b['nom']) && ($a['obj'] == $b['obj'])) {
				$perm_or = $a['perm']||$b['perm'];
			}
			$ref_perm_or[$asis] = array ( 'nom'=> $a['nom'], 'obj'=>$a['obj'],	'perm'=> $perm_or);
		}
		return $ref_perm_or;
	}
}
?>
